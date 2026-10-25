<?php

namespace App\Http\Controllers;

use App\Models\BerkasSiswa;
use App\Models\JenisBerkas;
use App\Models\PengaturanSistem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiswaPortalController extends Controller
{
    /**
     * Dashboard Utama Calon Siswa (FR-23, FR-08, FR-09, FR-43)
     */
    public function dashboard()
    {
        /** @var \App\Models\CalonSiswa $siswa */
        $siswa = Auth::guard('siswa')->user()->load([
            'jurusan',
            'gelombang',
            'tahunAjaran',
            'berkas.jenisBerkas',
            'pengambilanSeragam.petugas',
            'pembayaran.verifikator',
        ]);

        $pengaturan = PengaturanSistem::instance();
        $pembayaran = $siswa->getOrCreatePembayaran();

        // Ambil jenis berkas yang relevan dengan jalur pendaftaran siswa
        $jenisBerkasQuery = JenisBerkas::where('is_aktif', true)->orderBy('urutan');
        if ($siswa->jalur_pendaftaran === 'prestasi') {
            $jenisBerkasList = $jenisBerkasQuery->get();
        } else {
            $jenisBerkasList = $jenisBerkasQuery->whereIn('aturan_wajib', ['semua', 'opsional'])->get();
        }

        // Hitung progres unggahan berkas wajib
        $berkasWajibIds = $jenisBerkasList->filter(fn ($b) => $b->isWajibUntukJalur($siswa->jalur_pendaftaran))->pluck('id')->toArray();
        $totalWajib = count($berkasWajibIds);

        $uploadedBerkasMap = $siswa->berkas->keyBy('jenis_berkas_id');
        $uploadedWajibCount = 0;
        foreach ($berkasWajibIds as $idWajib) {
            if (isset($uploadedBerkasMap[$idWajib]) && in_array($uploadedBerkasMap[$idWajib]->status, ['menunggu', 'disetujui'])) {
                $uploadedWajibCount++;
            }
        }

        $persentaseProgres = $totalWajib > 0 ? round(($uploadedWajibCount / $totalWajib) * 100) : 0;

        // Clearance seragam (FR-09) & Lockdown status (FR-10)
        $isClearanceSeragam = $siswa->isSeragamClearanceApproved();
        $isLockdown = $siswa->isLockdownActive();

        return view('pages.siswa.dashboard', compact(
            'siswa',
            'pengaturan',
            'pembayaran',
            'jenisBerkasList',
            'uploadedBerkasMap',
            'totalWajib',
            'uploadedWajibCount',
            'persentaseProgres',
            'isClearanceSeragam',
            'isLockdown'
        ));
    }

    /**
     * Unggah berkas dokumen pendaftaran secara bertahap (FR-07, FR-24, FR-25)
     */
    public function uploadBerkas(Request $request)
    {
        /** @var \App\Models\CalonSiswa $siswa */
        $siswa = Auth::guard('siswa')->user();
        $pengaturan = PengaturanSistem::instance();

        // Validasi penguncian server (Lockdown - FR-10 & BR-08)
        if ($siswa->isLockdownActive()) {
            return back()->with('error', 'Akses unggah berkas telah dikunci otomatis oleh server karena melewati batas waktu. Silakan hubungi Panitia PPDB / Super Admin sekolah untuk permohonan pembukaan akses.');
        }

        $maxSizeKb = ($pengaturan->max_file_size_mb ?? 2) * 1024;

        $request->validate([
            'jenis_berkas_id' => ['required', 'exists:jenis_berkas,id'],
            'berkas' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:' . $maxSizeKb,
            ],
        ], [
            'jenis_berkas_id.required' => 'Jenis berkas wajib ditentukan.',
            'berkas.required' => 'Silakan pilih file dokumen yang akan diunggah.',
            'berkas.mimes' => 'Format file harus berupa PDF, JPG, JPEG, atau PNG.',
            'berkas.max' => 'Ukuran file tidak boleh melebihi ' . ($pengaturan->max_file_size_mb ?? 2) . ' MB.',
        ]);

        $jenisBerkas = JenisBerkas::findOrFail($request->jenis_berkas_id);

        // Aturan boleh/tidak upload (FR-07 default): Boleh jika Belum Diunggah atau Ditolak.
        $berkasLama = BerkasSiswa::where('calon_siswa_id', $siswa->id)
            ->where('jenis_berkas_id', $jenisBerkas->id)
            ->first();

        if ($berkasLama && in_array($berkasLama->status, ['menunggu', 'disetujui'])) {
            return back()->with('error', 'Berkas ini berstatus ' . ucfirst($berkasLama->status) . ' dan tidak dapat diganti kecuali dinyatakan Ditolak oleh panitia.');
        }

        $uploadedFile = $request->file('berkas');
        $extension = $uploadedFile->getClientOriginalExtension();
        $randomFileName = Str::random(32) . '.' . $extension;
        $folderPath = 'berkas_siswa/' . $siswa->id;

        // Hapus file lama dari penyimpanan privat jika ada
        if ($berkasLama && $berkasLama->file_path && Storage::disk('local')->exists($berkasLama->file_path)) {
            Storage::disk('local')->delete($berkasLama->file_path);
        }

        // Simpan file ke disk privat (local = storage/app/private)
        $savedPath = $uploadedFile->storeAs($folderPath, $randomFileName, 'local');

        // Simpan atau perbarui record berkas siswa (FR-25: catatan penolakan lama tetap terlihat sampai diverifikasi ulang)
        BerkasSiswa::updateOrCreate(
            [
                'calon_siswa_id' => $siswa->id,
                'jenis_berkas_id' => $jenisBerkas->id,
            ],
            [
                'file_path' => $savedPath,
                'nama_file_asli' => $uploadedFile->getClientOriginalName(),
                'file_size_kb' => round($uploadedFile->getSize() / 1024),
                'mime_type' => $uploadedFile->getMimeType(),
                'status' => 'menunggu',
                // Pertahankan catatan penolakan lama jika ada
                'catatan_penolakan' => $berkasLama ? $berkasLama->catatan_penolakan : null,
                'diverifikasi_oleh' => null,
                'waktu_verifikasi' => null,
            ]
        );

        return redirect()->route('siswa.dashboard')->with('sukses', 'Berkas ' . $jenisBerkas->nama . ' berhasil diunggah. Menunggu verifikasi panitia.');
    }

    /**
     * Akses unduh / pratinjau berkas secara aman (NFR-07)
     */
    public function lihatBerkas(int $id)
    {
        /** @var \App\Models\CalonSiswa $siswa */
        $siswa = Auth::guard('siswa')->user();

        // Hanya pemilik berkas yang berhak membuka via rute siswa
        $berkas = BerkasSiswa::where('calon_siswa_id', $siswa->id)->findOrFail($id);

        if (!Storage::disk('local')->exists($berkas->file_path)) {
            abort(404, 'Dokumen berkas tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('local')->response($berkas->file_path, $berkas->nama_file_asli);
    }

    /**
     * Siswa mengonfirmasi pembayaran transfer dengan mengunggah bukti transfer
     */
    public function konfirmasiPembayaran(Request $request)
    {
        /** @var \App\Models\CalonSiswa $siswa */
        $siswa = Auth::guard('siswa')->user();
        $pembayaran = $siswa->getOrCreatePembayaran();

        if ($pembayaran->isLunas()) {
            return back()->with('error', 'Biaya pendaftaran Anda sudah dinyatakan Lunas. Tidak perlu mengunggah bukti pembayaran kembali.');
        }

        $pengaturan = PengaturanSistem::instance();
        $maxSizeKb = ($pengaturan->max_file_size_mb ?? 2) * 1024;

        $request->validate([
            'nama_pengirim' => ['required', 'string', 'max:150'],
            'bank_pengirim' => ['required', 'string', 'max:100'],
            'tanggal_bayar' => ['required', 'date', 'before_or_equal:today'],
            'bukti_transfer' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:' . $maxSizeKb,
            ],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_pengirim.required' => 'Nama pemilik rekening / pengirim transfer wajib diisi.',
            'bank_pengirim.required' => 'Nama bank atau metode transfer pengirim wajib diisi.',
            'tanggal_bayar.required' => 'Tanggal transfer wajib dipilih.',
            'tanggal_bayar.before_or_equal' => 'Tanggal transfer tidak boleh di masa depan.',
            'bukti_transfer.required' => 'Silakan lampirkan foto struk atau tangkapan layar bukti transfer.',
            'bukti_transfer.mimes' => 'Format file bukti transfer harus berupa PDF, JPG, JPEG, atau PNG.',
            'bukti_transfer.max' => 'Ukuran file bukti transfer maksimal ' . ($pengaturan->max_file_size_mb ?? 2) . ' MB.',
        ]);

        $file = $request->file('bukti_transfer');
        $ext = $file->getClientOriginalExtension();
        $randomName = Str::random(32) . '.' . $ext;
        $folderPath = 'bukti_pembayaran/' . $siswa->id;

        // Hapus file bukti lama jika ada
        if ($pembayaran->bukti_transfer && Storage::disk('local')->exists($pembayaran->bukti_transfer)) {
            Storage::disk('local')->delete($pembayaran->bukti_transfer);
        }

        $savedPath = $file->storeAs($folderPath, $randomName, 'local');

        $pembayaran->update([
            'metode_pembayaran' => 'transfer',
            'status' => 'menunggu_verifikasi',
            'nama_pengirim' => $request->input('nama_pengirim'),
            'bank_pengirim' => $request->input('bank_pengirim'),
            'tanggal_bayar' => $request->input('tanggal_bayar'),
            'bukti_transfer' => $savedPath,
            'catatan_admin' => null, // Reset alasan tolak lama jika unggah ulang
        ]);

        return redirect()->route('siswa.dashboard')->with('sukses', 'Konfirmasi pembayaran transfer berhasil dikirim. Menunggu verifikasi oleh panitia/keuangan sekolah.');
    }

    /**
     * Siswa melihat pratinjau bukti transfer yang diunggahnya (NFR-07)
     */
    public function lihatBuktiPembayaran()
    {
        /** @var \App\Models\CalonSiswa $siswa */
        $siswa = Auth::guard('siswa')->user();
        $pembayaran = $siswa->pembayaran;

        if (!$pembayaran || !$pembayaran->bukti_transfer || !Storage::disk('local')->exists($pembayaran->bukti_transfer)) {
            abort(404, 'Bukti pembayaran belum diunggah atau file tidak ditemukan.');
        }

        return Storage::disk('local')->response($pembayaran->bukti_transfer);
    }

    /**
     * Cetak Kuitansi Pembayaran Resmi Calon Siswa (PDF / Print View)
     */
    public function cetakKuitansi()
    {
        /** @var \App\Models\CalonSiswa $siswa */
        $siswa = Auth::guard('siswa')->user()->load(['jurusan', 'gelombang', 'tahunAjaran', 'pembayaran.verifikator']);
        $pembayaran = $siswa->pembayaran;

        if (!$pembayaran || !$pembayaran->isLunas()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kuitansi pembayaran resmi hanya dapat dicetak setelah status pembayaran diverifikasi Lunas.');
        }

        $pengaturan = PengaturanSistem::instance();

        return view('pages.siswa.kuitansi-cetak', compact('siswa', 'pembayaran', 'pengaturan'));
    }

    /**
     * Memperbarui kata sandi calon siswa (mengubah dari default tanggal lahir atau sandi lama)
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\CalonSiswa $siswa */
        $siswa = Auth::guard('siswa')->user();

        $request->validate([
            'password_saat_ini' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password_saat_ini.required' => 'Kata sandi saat ini (atau tanggal lahir) wajib diisi.',
            'password_baru.required' => 'Kata sandi baru wajib diisi.',
            'password_baru.min' => 'Kata sandi baru minimal terdiri dari :min karakter.',
            'password_baru.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $rawCurrent = trim($request->input('password_saat_ini'));

        if (!$siswa->checkPassword($rawCurrent)) {
            return back()->withErrors([
                'password_saat_ini' => 'Kata sandi saat ini tidak cocok dengan data Anda.',
            ]);
        }

        $siswa->update([
            'password' => Hash::make($request->input('password_baru')),
        ]);

        return redirect()->route('siswa.dashboard')->with('sukses', 'Kata sandi berhasil diperbarui. Silakan gunakan kata sandi baru ini untuk login berikutnya.');
    }
}
