<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran mandiri (Fase 1 - FR-04)
     */
    public function daftar()
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::firstOrCreate(['nama' => '2026/2027'], ['is_aktif' => true]);
        $pengaturan = PengaturanSistem::instance();
        $jurusans = Jurusan::aktif()->get();
        $gelombangAktif = Gelombang::gelombangAktifSaatIni($tahunAjaran->id);

        // Rekap kuota promo benefit 100 pendaftar pertama (FR-43)
        $jumlahPendaftarTahunIni = CalonSiswa::where('tahun_ajaran_id', $tahunAjaran->id)->count();
        $sisaKuotaPromo = max(0, $pengaturan->kuota_promo_benefit - $jumlahPendaftarTahunIni);

        return view('pages.ppdb.daftar', compact(
            'tahunAjaran',
            'pengaturan',
            'jurusans',
            'gelombangAktif',
            'jumlahPendaftarTahunIni',
            'sisaKuotaPromo'
        ));
    }

    /**
     * Simpan pendaftaran siswa baru secara mandiri (Fase 1 - FR-04 & FR-21)
     */
    public function simpan(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $tahunAjaranId = $tahunAjaran->id;

        // Validasi kelengkapan & format (FR-21 & BR-01)
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('calon_siswas', 'nik')->where(fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId)),
            ],
            'nisn' => [
                'required',
                'digits:10',
                Rule::unique('calon_siswas', 'nisn')->where(fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId)),
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
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar dalam sistem PPDB tahun ajaran ini. Silakan hubungi panitia jika Anda merasa ada kekeliruan.',
            'nisn.unique' => 'NISN ini sudah terdaftar dalam sistem PPDB tahun ajaran ini. Pendaftaran ganda tidak diizinkan.',
            'nik.digits' => 'NIK wajib berupa 16 digit angka sesuai Kartu Keluarga.',
            'nisn.digits' => 'NISN wajib berupa 10 digit angka dari sekolah asal.',
            'tanggal_lahir.before' => 'Tanggal lahir tidak boleh di masa depan.',
        ]);

        // Eksekusi pembuatan nomor pendaftaran dan akun dalam transaksi dengan row lock
        $siswa = DB::transaction(function () use ($validated, $tahunAjaran, $request) {
            $pengaturan = PengaturanSistem::where('id', 1)->lockForUpdate()->first() ?? PengaturanSistem::instance();

            // Hitung nomor urut berikutnya
            $nextUrut = ($pengaturan->nomor_urut_terakhir ?? 0) + 1;
            $pengaturan->nomor_urut_terakhir = $nextUrut;
            $pengaturan->save();

            // Format Nomor Pendaftaran: PPDB/{tahun}/{urut 4 digit} (BR-01 & Default Bagian 12)
            $tahunString = Carbon::now()->format('Y');
            $nomorPendaftaran = sprintf('PPDB/%s/%04d', $tahunString, $nextUrut);

            // Alokasi benefit promo 100 pendaftar pertama (FR-43 & BR-14)
            $statusBenefit = 'tidak_dapat';
            if ($pengaturan->is_promo_aktif && $nextUrut <= $pengaturan->kuota_promo_benefit) {
                $statusBenefit = 'kandidat_100_pertama';
            }

            // Password awal = tanggal lahir format DDMMYYYY (BR-02), disimpan hash
            $passwordRaw = Carbon::parse($validated['tanggal_lahir'])->format('dmY');
            $passwordHash = Hash::make($passwordRaw);

            // Tentukan gelombang pendaftaran aktif
            $gelombang = Gelombang::gelombangAktifSaatIni($tahunAjaran->id)
                ?? Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->first();

            return CalonSiswa::create([
                'nomor_pendaftaran' => $nomorPendaftaran,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'gelombang_id' => $gelombang->id,
                'jurusan_id' => $validated['jurusan_id'],
                'jalur_pendaftaran' => $validated['jalur_pendaftaran'],
                'jalur_registrasi' => 'online',
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

        // Catat nomor pendaftaran di session untuk halaman sukses (FR-05) - TANPA auto-login
        session(['ppdb_sukses_id' => $siswa->id]);

        return redirect()->route('ppdb.berhasil');
    }

    /**
     * Halaman instruksi login & sukses pendaftaran (Fase 1 - FR-05)
     */
    public function berhasil()
    {
        $siswaId = session('ppdb_sukses_id');
        if (!$siswaId) {
            return redirect()->route('ppdb.daftar');
        }

        $siswa = CalonSiswa::with(['jurusan', 'gelombang', 'tahunAjaran'])->findOrFail($siswaId);

        return view('pages.ppdb.berhasil', compact('siswa'));
    }

    /**
     * Cetak Lembar Bukti Pendaftaran Resmi (FR-22 & DESIGN.md Section 6)
     */
    public function cetakBukti(int $id)
    {
        $siswa = CalonSiswa::with(['jurusan', 'gelombang', 'tahunAjaran'])->findOrFail($id);
        $pengaturan = PengaturanSistem::instance();

        return view('pages.ppdb.bukti-cetak', compact('siswa', 'pengaturan'));
    }
}
