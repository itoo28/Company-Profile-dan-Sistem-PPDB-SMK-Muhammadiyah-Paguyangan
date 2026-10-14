<?php

namespace App\Http\Controllers;

use App\Models\BerkasSiswa;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\JenisBerkas;
use App\Models\Jurusan;
use App\Models\LogAktivitas;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminPendaftarController extends Controller
{
    /**
     * Daftar Pendaftar PPDB dengan Filter dan Pencarian (FR-12, FR-26)
     */
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam'])
            ->where('tahun_ajaran_id', $tahunAjaran->id);

        // Pencarian Cepat Nama / Nomor Pendaftaran / Asal Sekolah
        if ($cari = trim($request->input('q', ''))) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$cari}%")
                  ->orWhere('nik', 'like', "%{$cari}%")
                  ->orWhere('nisn', 'like', "%{$cari}%")
                  ->orWhere('asal_sekolah', 'like', "%{$cari}%");
            });
        }

        // Filter Gelombang
        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        // Filter Jurusan
        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        // Filter Jalur Pendaftaran (Umum / Prestasi)
        if ($jalur = $request->input('jalur_pendaftaran')) {
            $query->where('jalur_pendaftaran', $jalur);
        }

        // Filter Jalur Registrasi (Online / Offline)
        if ($registrasi = $request->input('jalur_registrasi')) {
            $query->where('jalur_registrasi', $registrasi);
        }

        // Filter Benefit SPP 3 Bulan
        if ($benefit = $request->input('status_benefit')) {
            $query->where('status_benefit', $benefit);
        }

        // Filter Status Berkas Ringkas
        if ($statusBerkas = $request->input('status_berkas')) {
            if ($statusBerkas === 'ditolak') {
                $query->whereHas('berkas', fn ($b) => $b->where('status', 'ditolak'));
            } elseif ($statusBerkas === 'menunggu') {
                $query->whereHas('berkas', fn ($b) => $b->where('status', 'menunggu'))
                      ->whereDoesntHave('berkas', fn ($b) => $b->where('status', 'ditolak'));
            }
        }

        $pendaftarList = $query->latest('id')->paginate(15)->withQueryString();

        $gelombangs = Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->get();
        $jurusans = Jurusan::aktif()->get();

        return view('pages.admin.pendaftar.index', compact(
            'pendaftarList',
            'gelombangs',
            'jurusans',
            'tahunAjaran'
        ));
    }

    /**
     * Tampilkan Detail Pendaftar & Panel Verifikasi Berkas (FR-13, FR-29)
     */
    public function show(int $id)
    {
        $siswa = CalonSiswa::with([
            'jurusan',
            'gelombang',
            'tahunAjaran',
            'berkas.jenisBerkas',
            'berkas.verifikator',
            'pengambilanSeragam.petugas',
        ])->findOrFail($id);

        $jenisBerkasList = JenisBerkas::where('is_aktif', true)->orderBy('urutan')->get();
        $berkasMap = $siswa->berkas->keyBy('jenis_berkas_id');
        $isClearanceSeragam = $siswa->isSeragamClearanceApproved();

        $allJurusans = Jurusan::aktif()->get();
        $allGelombangs = Gelombang::where('tahun_ajaran_id', $siswa->tahun_ajaran_id)->get();

        return view('pages.admin.pendaftar.show', compact(
            'siswa',
            'jenisBerkasList',
            'berkasMap',
            'isClearanceSeragam',
            'allJurusans',
            'allGelombangs'
        ));
    }

    /**
     * Formulir Pendaftaran Offline di Loket Piket (FR-11)
     */
    public function createOffline()
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $pengaturan = PengaturanSistem::instance();
        $jurusans = Jurusan::aktif()->get();
        $gelombangs = Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->where('is_aktif', true)->get();

        return view('pages.admin.pendaftar.create-offline', compact(
            'tahunAjaran',
            'pengaturan',
            'jurusans',
            'gelombangs'
        ));
    }

    /**
     * Simpan Pendaftaran Offline oleh Petugas (FR-11)
     */
    public function storeOffline(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('calon_siswas', 'nik')->where(fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id)),
            ],
            'nisn' => [
                'required',
                'digits:10',
                Rule::unique('calon_siswas', 'nisn')->where(fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id)),
            ],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string', 'max:1000'],
            'asal_sekolah' => ['required', 'string', 'max:150'],
            'nama_orang_tua' => ['required', 'string', 'max:150'],
            'no_hp_orang_tua' => ['required', 'string', 'max:25'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'jalur_pendaftaran' => ['required', 'in:umum,prestasi'],
            'gelombang_id' => ['required', 'exists:gelombangs,id'],
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar pada tahun ajaran ini.',
            'nisn.unique' => 'NISN ini sudah terdaftar pada tahun ajaran ini.',
        ]);

        $siswa = DB::transaction(function () use ($validated, $tahunAjaran) {
            $pengaturan = PengaturanSistem::where('id', 1)->lockForUpdate()->first() ?? PengaturanSistem::instance();

            $nextUrut = ($pengaturan->nomor_urut_terakhir ?? 0) + 1;
            $pengaturan->nomor_urut_terakhir = $nextUrut;
            $pengaturan->save();

            $tahunString = Carbon::now()->format('Y');
            $nomorPendaftaran = sprintf('PPDB/%s/%04d', $tahunString, $nextUrut);

            $statusBenefit = 'tidak_dapat';
            if ($pengaturan->is_promo_aktif && $nextUrut <= $pengaturan->kuota_promo_benefit) {
                $statusBenefit = 'kandidat_100_pertama';
            }

            $passwordRaw = Carbon::parse($validated['tanggal_lahir'])->format('dmY');
            $passwordHash = Hash::make($passwordRaw);

            return CalonSiswa::create([
                'nomor_pendaftaran' => $nomorPendaftaran,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'gelombang_id' => $validated['gelombang_id'],
                'jurusan_id' => $validated['jurusan_id'],
                'jalur_pendaftaran' => $validated['jalur_pendaftaran'],
                'jalur_registrasi' => 'offline',
                'nomor_urut' => $nextUrut,
                'status_benefit' => $statusBenefit,
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'],
                'nisn' => $validated['nisn'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'alamat' => $validated['alamat'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'nama_orang_tua' => $validated['nama_orang_tua'],
                'no_hp_orang_tua' => $validated['no_hp_orang_tua'],
                'password' => $passwordHash,
                'is_unlocked' => false,
            ]);
        });

        LogAktivitas::catat($user, 'pendaftaran_offline', 'Calon Siswa', "Pendaftaran offline untuk {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran})");

        return redirect()->route('admin.pendaftar.show', $siswa->id)
            ->with('sukses', "Pendaftaran offline berhasil disimpan dengan Nomor Pendaftaran: {$siswa->nomor_pendaftaran}.");
    }

    /**
     * Verifikasi Berkas Siswa (Approve / Reject) (FR-13 & BR-04, BR-10)
     */
    public function verifikasiBerkas(Request $request, int $berkasId)
    {
        $user = Auth::guard('web')->user();

        $request->validate([
            'status' => ['required', 'in:disetujui,ditolak'],
            'catatan_penolakan' => [
                Rule::requiredIf($request->status === 'ditolak'),
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'catatan_penolakan.required' => 'Penolakan berkas wajib disertai alasan atau catatan perbaikan yang jelas (BR-04).',
        ]);

        $berkas = BerkasSiswa::with(['calonSiswa', 'jenisBerkas'])->findOrFail($berkasId);
        $siswa = $berkas->calonSiswa;

        $statusBaru = $request->status;
        $catatan = $statusBaru === 'ditolak' ? trim($request->catatan_penolakan) : null;

        $berkas->update([
            'status' => $statusBaru,
            'catatan_penolakan' => $catatan,
            'diverifikasi_oleh' => $user->id,
            'waktu_verifikasi' => Carbon::now(),
        ]);

        // Catat di log aktivitas (BR-10)
        LogAktivitas::catat(
            $user,
            'verifikasi_berkas',
            "Berkas: {$berkas->jenisBerkas->nama}",
            "Verifikasi {$statusBaru} untuk siswa {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran})." . ($catatan ? " Catatan: {$catatan}" : "")
        );

        // Sinkronisasi status benefit promo (FR-43 & BR-14): Jika semua berkas wajib disetujui, benefit menjadi aktif
        if ($siswa->isSeragamClearanceApproved()) {
            if ($siswa->status_benefit === 'kandidat_100_pertama') {
                $siswa->update(['status_benefit' => 'klaim_aktif']);
            }
        } else {
            if ($siswa->status_benefit === 'klaim_aktif') {
                $siswa->update(['status_benefit' => 'kandidat_100_pertama']);
            }
        }

        return back()->with('sukses', "Status berkas {$berkas->jenisBerkas->nama} berhasil diubah menjadi " . ucfirst($statusBaru) . '.');
    }

    /**
     * Bypass Lockdown / Unlock Akses Unggah Siswa (FR-17 & BR-08) - Hanya Super Admin
     */
    public function unlock(int $id)
    {
        $user = Auth::guard('web')->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang berhak melakukan unlock batas waktu berkas (FR-17).');
        }

        $siswa = CalonSiswa::findOrFail($id);
        $siswa->is_unlocked = !$siswa->is_unlocked;
        $siswa->save();

        $statusTeks = $siswa->is_unlocked ? 'Dibuka (Unlocked)' : 'Dikunci (Locked)';
        LogAktivitas::catat($user, 'unlock_berkas', 'Calon Siswa', "Akses upload diubah menjadi {$statusTeks} untuk {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran}).");

        return back()->with('sukses', "Status akses unggah berkas untuk {$siswa->nama_lengkap} berhasil diubah menjadi: {$statusTeks}.");
    }

    /**
     * Koreksi Data Siswa oleh Super Admin (FR-29)
     */
    public function koreksi(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang memiliki hak koreksi data siswa (FR-29).');
        }

        $siswa = CalonSiswa::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('calon_siswas', 'nik')->where(fn ($q) => $q->where('tahun_ajaran_id', $siswa->tahun_ajaran_id))->ignore($siswa->id),
            ],
            'nisn' => [
                'required',
                'digits:10',
                Rule::unique('calon_siswas', 'nisn')->where(fn ($q) => $q->where('tahun_ajaran_id', $siswa->tahun_ajaran_id))->ignore($siswa->id),
            ],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string', 'max:1000'],
            'asal_sekolah' => ['required', 'string', 'max:150'],
            'nama_orang_tua' => ['required', 'string', 'max:150'],
            'no_hp_orang_tua' => ['required', 'string', 'max:25'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'jalur_pendaftaran' => ['required', 'in:umum,prestasi'],
            'gelombang_id' => ['required', 'exists:gelombangs,id'],
        ]);

        // Periksa apakah tanggal lahir berubah (FR-29: Password otomatis di-hash ulang dari tanggal lahir baru)
        $tanggalLahirLama = $siswa->tanggal_lahir->format('Y-m-d');
        $tanggalLahirBaru = Carbon::parse($validated['tanggal_lahir'])->format('Y-m-d');

        if ($tanggalLahirLama !== $tanggalLahirBaru) {
            $newPasswordRaw = Carbon::parse($validated['tanggal_lahir'])->format('dmY');
            $validated['password'] = Hash::make($newPasswordRaw);
        }

        $siswa->update($validated);

        LogAktivitas::catat($user, 'koreksi_data', 'Calon Siswa', "Koreksi data pendaftar {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran}) oleh Super Admin.");

        return back()->with('sukses', 'Data calon siswa berhasil diperbarui.' . ($tanggalLahirLama !== $tanggalLahirBaru ? ' Password login siswa otomatis disinkronkan ke tanggal lahir baru.' : ''));
    }

    /**
     * Pratinjau Dokumen Berkas Aman untuk Petugas Admin (NFR-07)
     */
    public function lihatBerkas(int $berkasId)
    {
        $berkas = BerkasSiswa::findOrFail($berkasId);

        if (!Storage::disk('local')->exists($berkas->file_path)) {
            abort(404, 'Dokumen berkas tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('local')->response($berkas->file_path, $berkas->nama_file_asli);
    }
}
