<?php

namespace App\Http\Controllers;

use App\Models\BerkasSiswa;
use App\Models\JenisBerkas;
use App\Models\PengaturanSistem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ]);

        $pengaturan = PengaturanSistem::instance();

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
}
