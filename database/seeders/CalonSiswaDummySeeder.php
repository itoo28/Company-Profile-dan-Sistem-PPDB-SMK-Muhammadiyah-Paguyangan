<?php

namespace Database\Seeders;

use App\Models\BerkasSiswa;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\JenisBerkas;
use App\Models\Jurusan;
use App\Models\Pembayaran;
use App\Models\PengambilanSeragam;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CalonSiswaDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Data Master Induk Tersedia
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first() ?? TahunAjaran::first();
        if (!$tahunAjaran) {
            $this->command?->error('Tahun Ajaran aktif belum tersedia. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $pengaturan = PengaturanSistem::instance();
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminPiket = User::where('role', 'admin_piket')->first() ?? $superAdmin;

        $jurusans = Jurusan::all()->keyBy('kode');
        $gelombangs = Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->get()->keyBy('kode_gelombang');
        $jenisBerkasList = JenisBerkas::all()->keyBy('id');

        if ($jurusans->isEmpty() || $gelombangs->isEmpty() || $jenisBerkasList->isEmpty()) {
            $this->command?->error('Data Jurusan, Gelombang, atau Jenis Berkas belum lengkap. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // 2. Siapkan File Fisik Dummy di Private Storage agar tidak 404 saat dipratinjau Admin
        $dummyPdfPath = 'dummy/dummy_dokumen.pdf';
        $dummyImgPath = 'dummy/dummy_bukti_transfer.jpg';

        if (!Storage::disk('local')->exists($dummyPdfPath)) {
            $minimalPdf = "%PDF-1.4\n1 0 obj<</Type /Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type /Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type /Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
            Storage::disk('local')->put($dummyPdfPath, $minimalPdf);
        }

        if (!Storage::disk('local')->exists($dummyImgPath)) {
            $sourceImage = public_path('images/gambargedungbaru.jpg');
            if (file_exists($sourceImage)) {
                Storage::disk('local')->put($dummyImgPath, file_get_contents($sourceImage));
            } else {
                Storage::disk('local')->put($dummyImgPath, 'DUMMY_IMAGE_BINARY');
            }
        }

        // 3. Bersihkan Data Calon Siswa Sebelumnya (jika ada) untuk Menjamin Konsistensi & Idempotensi
        Schema::disableForeignKeyConstraints();
        PengambilanSeragam::query()->delete();
        Pembayaran::query()->delete();
        BerkasSiswa::query()->delete();
        CalonSiswa::query()->delete();
        Schema::enableForeignKeyConstraints();

        // 4. Kumpulan Bank & Alasan Penolakan
        $daftarBank = [
            'Bank Syariah Indonesia (BSI)',
            'Bank Rakyat Indonesia (BRI)',
            'Bank Central Asia (BCA)',
            'Bank Mandiri',
            'Bank Jateng',
            'Bank Negara Indonesia (BNI)',
            'DANA / E-Wallet',
        ];

        $daftarAlasanTolakBerkas = [
            'Foto dokumen terpotong di bagian sudut dan nomor identitas tidak terlihat jelas. Harap unggah ulang dengan posisi tegak lurus dan pencahayaan terang.',
            'Dokumen ijazah / SKL belum dilegalisir basah oleh pihak sekolah asal.',
            'Hasil scan berkas buram dan tidak terbaca jelas saat diperbesar. Mohon upload ulang dokumen asli.',
            'Format dokumen tidak sesuai ketentuan atau halaman identitas tidak lengkap.',
            'Nama pada dokumen berbeda dengan nama pendaftar di formulir. Harap lampirkan surat keterangan perbaikan dari sekolah asal.',
        ];

        $daftarCatatanTolakBayar = [
            'Nominal transfer tidak sesuai dengan biaya pendaftaran resmi (Rp 100.000). Harap melunasi sisa kekurangan.',
            'Bukti transfer buram/gelap, nomor referensi dan tanggal transaksi tidak terlihat jelas.',
            'Bukti transaksi terindikasi duplikasi dari mutasi rekening yang sudah pernah digunakan.',
            'Nama rekening pengirim dan tanggal transfer tidak ditemukan dalam mutasi rekening bank sekolah.',
        ];

        $daftarNamaPrestasi = [
            'Juara 1 Lomba Keterampilan Siswa (LKS) Otomotif Tingkat SMP Kab. Brebes',
            'Juara 2 Olimpiade Sains Nasional (OSN) Matematika MTs Se-Jawa Tengah',
            'Juara 1 Turnamen Futsal Pelajar Brebes Selatan Cup 2025',
            'Juara 1 Lomba MTQ Tartil Al-Qur\'an Tingkat Karesidenan Pekalongan',
            'Juara 2 Kompetisi Robotika & Line Follower Pelajar Banyumas',
            'Juara 1 Story Telling Bahasa Inggris Pelajar Se-Kabupaten Brebes',
            'Juara 3 Pencak Silat Tapak Suci Kategori Tanding Putra Jateng',
            'Juara 1 Desain Poster Digital FLS2N Tingkat SMP',
            'Hafidz Qur\'an 3 Juz Bersertifikat Resmi PDM Brebes',
            'Juara 1 Pidato Bahasa Arab MTs Muhammadiyah Tingkat Wilayah',
            'Juara 2 Bola Voli Pelajar O2SN Sub Rayon Bumiayu',
            'Juara 1 Catur Cepat Tingkat Pelajar SMP Kabupaten Brebes',
        ];

        // 5. Generator 150 Data Calon Siswa yang Unik & Beragam
        $dataSiswaMaster = $this->getKoleksi150Siswa();

        $kuotaPromo = (int) ($pengaturan->kuota_promo_benefit ?? 100);
        $biayaPendaftaran = (int) ($pengaturan->biaya_pendaftaran ?? 100000);

        foreach ($dataSiswaMaster as $index => $item) {
            $nomorUrut = $index + 1; // 1 s.d. 150
            $nomorPendaftaran = sprintf('PPDB/2026/%04d', $nomorUrut);

            // Status benefit pendaftar
            $statusBenefit = 'tidak_dapat';
            if ($nomorUrut <= $kuotaPromo) {
                // Untuk siswa urut 1 - 35 yang berkas lengkap, lunas, & ambil seragam: statusnya 'klaim_aktif'
                $statusBenefit = ($nomorUrut <= 35) ? 'klaim_aktif' : 'kandidat_100_pertama';
            }

            // Tentukan Gelombang
            $kodeGel = ($nomorUrut <= 60) ? 'G1' : (($nomorUrut <= 130) ? 'G2' : 'G3');
            $gelombang = $gelombangs->get($kodeGel) ?? $gelombangs->first();

            // Tentukan Jurusan
            $jurusan = $jurusans->get($item['jurusan']) ?? $jurusans->first();

            // Password akun = Hash dari DDMMYYYY tanggal lahir
            $passwordRaw = Carbon::parse($item['tanggal_lahir'])->format('dmY');
            $passwordHash = Hash::make($passwordRaw);

            // Waktu Pendaftaran Dibuat Natural
            $createdAt = $this->hitungWaktuPendaftaran($nomorUrut);

            // Buat Record Calon Siswa
            $calonSiswa = CalonSiswa::create([
                'nomor_pendaftaran' => $nomorPendaftaran,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'gelombang_id' => $gelombang->id,
                'jurusan_id' => $jurusan->id,
                'jalur_pendaftaran' => $item['jalur_pendaftaran'],
                'jalur_registrasi' => $item['jalur_registrasi'],
                'nomor_urut' => $nomorUrut,
                'status_benefit' => $statusBenefit,
                'nama_lengkap' => $item['nama_lengkap'],
                'nik' => $item['nik'],
                'nisn' => $item['nisn'],
                'tempat_lahir' => $item['tempat_lahir'],
                'tanggal_lahir' => $item['tanggal_lahir'],
                'jenis_kelamin' => $item['jenis_kelamin'],
                'alamat' => $item['alamat'],
                'asal_sekolah' => $item['asal_sekolah'],
                'nama_orang_tua' => $item['nama_orang_tua'],
                'no_hp_orang_tua' => $item['no_hp_orang_tua'],
                'password' => $passwordHash,
                'is_unlocked' => ($nomorUrut % 25 === 0), // Beberapa akun sengaja di-unlock Super Admin
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 6. Buat Record Pembayaran untuk Setiap Calon Siswa
            $this->buatDataPembayaran(
                $calonSiswa,
                $nomorUrut,
                $biayaPendaftaran,
                $superAdmin,
                $adminPiket,
                $daftarBank,
                $dummyImgPath,
                $daftarCatatanTolakBayar,
                $createdAt
            );

            // 7. Buat Record Berkas Siswa Sesuai Variasi Skenario
            $this->buatDataBerkas(
                $calonSiswa,
                $nomorUrut,
                $jenisBerkasList,
                $superAdmin,
                $adminPiket,
                $dummyPdfPath,
                $dummyImgPath,
                $daftarAlasanTolakBerkas,
                $daftarNamaPrestasi,
                $createdAt
            );

            // 8. Buat Record Pengambilan Seragam (Siswa 1 s.d. 35)
            if ($nomorUrut <= 35) {
                PengambilanSeragam::create([
                    'calon_siswa_id' => $calonSiswa->id,
                    'petugas_id' => ($nomorUrut % 2 === 0) ? $adminPiket->id : $superAdmin->id,
                    'waktu_ambil' => $createdAt->copy()->addDays(rand(2, 5))->setTime(rand(8, 14), rand(10, 50)),
                    'catatan' => 'Paket 3 stel seragam (Wearpack/Kejuruan, Batik Muhammadiyah, Seragam HW) + Jas Almamater & kelengkapan atribut telah diterima lengkap.',
                    'created_at' => $createdAt->copy()->addDays(rand(2, 5)),
                    'updated_at' => $createdAt->copy()->addDays(rand(2, 5)),
                ]);
            }
        }

        // 9. Sinkronisasi Pengaturan Sistem: Update nomor_urut_terakhir = 150
        $pengaturan->nomor_urut_terakhir = 150;
        $pengaturan->save();

        $this->command?->info('Berhasil membuat 150 data dummy calon siswa pendaftar PPDB yang lengkap dan beragam!');
    }

    /**
     * Hitung waktu pendaftaran secara realistis & bertahap
     */
    private function hitungWaktuPendaftaran(int $nomorUrut): Carbon
    {
        if ($nomorUrut <= 60) {
            // Gelombang 1: November 2025 - Januari 2026
            return Carbon::parse('2025-11-05 08:30:00')->addHours($nomorUrut * 28 + rand(1, 10));
        } elseif ($nomorUrut <= 125) {
            // Gelombang 2: Februari 2026 - April 2026
            $offset = $nomorUrut - 60;
            return Carbon::parse('2026-02-02 09:15:00')->addHours($offset * 25 + rand(1, 12));
        } elseif ($nomorUrut <= 145) {
            // Gelombang 3: Mei 2026 hingga beberapa hari lalu
            $offset = $nomorUrut - 125;
            return Carbon::parse('2026-05-02 10:00:00')->addDays($offset * 3)->addHours(rand(1, 8));
        } else {
            // Nomor 146 - 150: Pendaftar Baru HARI INI
            $jamLalu = 151 - $nomorUrut; // 1 s.d. 5 jam yang lalu
            return Carbon::now()->subHours($jamLalu)->subMinutes(rand(5, 45));
        }
    }

    /**
     * Buat data pembayaran calon siswa dengan status yang beragam
     */
    private function buatDataPembayaran(
        CalonSiswa $calonSiswa,
        int $nomorUrut,
        int $biayaPendaftaran,
        User $superAdmin,
        User $adminPiket,
        array $daftarBank,
        string $dummyImgPath,
        array $daftarCatatanTolakBayar,
        Carbon $createdAt
    ): void {
        $kodeTransaksi = sprintf('INV/2026/%05d', $nomorUrut);
        $petugas = ($nomorUrut % 2 === 0) ? $adminPiket : $superAdmin;
        $bank = $daftarBank[$nomorUrut % count($daftarBank)];

        // Siswa 1 - 70: Lunas via Transfer
        if ($nomorUrut <= 70) {
            Pembayaran::create([
                'calon_siswa_id' => $calonSiswa->id,
                'kode_transaksi' => $kodeTransaksi,
                'nominal' => $biayaPendaftaran,
                'metode_pembayaran' => 'transfer',
                'status' => 'lunas',
                'nama_pengirim' => $calonSiswa->nama_orang_tua,
                'bank_pengirim' => $bank,
                'tanggal_bayar' => $createdAt->copy()->addHours(rand(2, 24))->format('Y-m-d'),
                'bukti_transfer' => $dummyImgPath,
                'catatan_admin' => 'Pembayaran transfer telah diverifikasi masuk ke rekening resmi sekolah.',
                'diverifikasi_oleh' => $petugas->id,
                'diverifikasi_pada' => $createdAt->copy()->addHours(rand(25, 48)),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(48),
            ]);
            return;
        }

        // Siswa 71 - 105: Lunas via Tunai di Loket Sekolah
        if ($nomorUrut <= 105) {
            Pembayaran::create([
                'calon_siswa_id' => $calonSiswa->id,
                'kode_transaksi' => $kodeTransaksi,
                'nominal' => $biayaPendaftaran,
                'metode_pembayaran' => 'tunai',
                'status' => 'lunas',
                'nama_pengirim' => $calonSiswa->nama_lengkap,
                'bank_pengirim' => null,
                'tanggal_bayar' => $createdAt->copy()->format('Y-m-d'),
                'bukti_transfer' => null,
                'catatan_admin' => 'Pembayaran tunai lunas diterima langsung oleh petugas loket PPDB.',
                'diverifikasi_oleh' => $petugas->id,
                'diverifikasi_pada' => $createdAt->copy()->addMinutes(rand(10, 60)),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(60),
            ]);
            return;
        }

        // Siswa 106 - 125: Menunggu Verifikasi Transfer (sudah upload bukti)
        if ($nomorUrut <= 125) {
            Pembayaran::create([
                'calon_siswa_id' => $calonSiswa->id,
                'kode_transaksi' => $kodeTransaksi,
                'nominal' => $biayaPendaftaran,
                'metode_pembayaran' => 'transfer',
                'status' => 'menunggu_verifikasi',
                'nama_pengirim' => $calonSiswa->nama_orang_tua,
                'bank_pengirim' => $bank,
                'tanggal_bayar' => $createdAt->copy()->addHours(rand(1, 12))->format('Y-m-d'),
                'bukti_transfer' => $dummyImgPath,
                'catatan_admin' => null,
                'diverifikasi_oleh' => null,
                'diverifikasi_pada' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(12),
            ]);
            return;
        }

        // Siswa 126 - 142: Menunggu Pembayaran (belum bayar)
        if ($nomorUrut <= 142) {
            Pembayaran::create([
                'calon_siswa_id' => $calonSiswa->id,
                'kode_transaksi' => $kodeTransaksi,
                'nominal' => $biayaPendaftaran,
                'metode_pembayaran' => 'transfer',
                'status' => 'menunggu_pembayaran',
                'nama_pengirim' => null,
                'bank_pengirim' => null,
                'tanggal_bayar' => null,
                'bukti_transfer' => null,
                'catatan_admin' => null,
                'diverifikasi_oleh' => null,
                'diverifikasi_pada' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            return;
        }

        // Siswa 143 - 150: Ditolak (bukti transfer bermasalah)
        Pembayaran::create([
            'calon_siswa_id' => $calonSiswa->id,
            'kode_transaksi' => $kodeTransaksi,
            'nominal' => $biayaPendaftaran,
            'metode_pembayaran' => 'transfer',
            'status' => 'ditolak',
            'nama_pengirim' => $calonSiswa->nama_orang_tua,
            'bank_pengirim' => $bank,
            'tanggal_bayar' => $createdAt->copy()->addHours(rand(1, 5))->format('Y-m-d'),
            'bukti_transfer' => $dummyImgPath,
            'catatan_admin' => $daftarCatatanTolakBayar[$nomorUrut % count($daftarCatatanTolakBayar)],
            'diverifikasi_oleh' => $petugas->id,
            'diverifikasi_pada' => $createdAt->copy()->addHours(rand(6, 15)),
            'created_at' => $createdAt,
            'updated_at' => $createdAt->copy()->addHours(15),
        ]);
    }

    /**
     * Buat data berkas pendaftaran dengan variasi status realistis
     */
    private function buatDataBerkas(
        CalonSiswa $calonSiswa,
        int $nomorUrut,
        $jenisBerkasList,
        User $superAdmin,
        User $adminPiket,
        string $dummyPdfPath,
        string $dummyImgPath,
        array $daftarAlasanTolakBerkas,
        array $daftarNamaPrestasi,
        Carbon $createdAt
    ): void {
        $petugas = ($nomorUrut % 2 === 0) ? $adminPiket : $superAdmin;

        // Kelompok 5: Siswa 136 - 150 (Pendaftar baru, belum upload berkas)
        if ($nomorUrut > 135) {
            return;
        }

        // Tentukan daftar jenis berkas yang diunggah
        // 1 = KK, 2 = Akta, 3 = SKL, 4 = Sertifikat Prestasi, 5 = SKTM, 6 = KIP
        $berkasDiunggah = [1, 2];

        // Kelompok 4: Siswa 116 - 135 (Berkas belum lengkap: hanya KK & Akta)
        if ($nomorUrut <= 115) {
            $berkasDiunggah[] = 3; // Tambah SKL
        }

        // Jika jalur prestasi, wajib upload sertifikat prestasi (id 4)
        if ($calonSiswa->jalur_pendaftaran === 'prestasi') {
            $berkasDiunggah[] = 4;
        }

        // Variasi opsional: Beberapa siswa memiliki KIP atau SKTM
        if ($nomorUrut % 7 === 0) {
            $berkasDiunggah[] = 5; // SKTM
        } elseif ($nomorUrut % 5 === 0) {
            $berkasDiunggah[] = 6; // KIP
        }

        foreach ($berkasDiunggah as $jenisBerkasId) {
            $jenis = $jenisBerkasList->get($jenisBerkasId);
            if (!$jenis) {
                continue;
            }

            $ekstensi = ($jenisBerkasId % 2 === 0) ? 'jpg' : 'pdf';
            $filePath = ($ekstensi === 'pdf') ? $dummyPdfPath : $dummyImgPath;
            $mimeType = ($ekstensi === 'pdf') ? 'application/pdf' : 'image/jpeg';
            $namaFileAsli = strtolower(str_replace([' ', '/', '(', ')', '-'], '_', $jenis->nama)) . '_' . $calonSiswa->nomor_urut . '.' . $ekstensi;

            // Tentukan status berkas
            $status = 'disetujui';
            $catatanTolak = null;
            $diverifikasiOleh = $petugas->id;
            $waktuVerifikasi = $createdAt->copy()->addHours(rand(12, 48));

            // Kelompok 1: Siswa 1 - 60 -> Disetujui Semua (Clearance Approved)
            if ($nomorUrut <= 60) {
                $status = 'disetujui';
            }
            // Kelompok 2: Siswa 61 - 95 -> Menunggu Verifikasi
            elseif ($nomorUrut <= 95) {
                $status = 'menunggu';
                $diverifikasiOleh = null;
                $waktuVerifikasi = null;
            }
            // Kelompok 3: Siswa 96 - 115 -> Ada berkas yang Ditolak
            elseif ($nomorUrut <= 115) {
                // Berkas KK atau SKL ditolak
                if ($jenisBerkasId === 1 || $jenisBerkasId === 3) {
                    $status = 'ditolak';
                    $catatanTolak = $daftarAlasanTolakBerkas[($nomorUrut + $jenisBerkasId) % count($daftarAlasanTolakBerkas)];
                } else {
                    $status = 'disetujui';
                }
            }
            // Kelompok 4: Siswa 116 - 135 -> Belum lengkap (status menunggu atau disetujui)
            else {
                $status = ($nomorUrut % 2 === 0) ? 'disetujui' : 'menunggu';
                if ($status === 'menunggu') {
                    $diverifikasiOleh = null;
                    $waktuVerifikasi = null;
                }
            }

            BerkasSiswa::create([
                'calon_siswa_id' => $calonSiswa->id,
                'jenis_berkas_id' => $jenis->id,
                'file_path' => $filePath,
                'nama_file_asli' => $namaFileAsli,
                'file_size_kb' => rand(450, 1850),
                'mime_type' => $mimeType,
                'status' => $status,
                'catatan_penolakan' => $catatanTolak,
                'diverifikasi_oleh' => $diverifikasiOleh,
                'waktu_verifikasi' => $waktuVerifikasi,
                'created_at' => $createdAt->copy()->addHours(rand(1, 10)),
                'updated_at' => $waktuVerifikasi ?? $createdAt->copy()->addHours(rand(1, 10)),
            ]);
        }
    }

    /**
     * Master Data 150 Calon Siswa Lengkap & Beragam
     */
    private function getKoleksi150Siswa(): array
    {
        // 95 Nama Laki-laki Autentik Indonesia
        $namaLakiLaki = [
            'Muhammad Rizky Pratama', 'Dimas Aditya Nugraha', 'Fajar Ramadhan', 'Ahmad Fauzi',
            'Bagas Dwi Cahyono', 'Ilham Wahyu Kurniawan', 'Raditya Arya Saputra', 'Bayu Setiawan',
            'Gilang Ramadhan', 'Tegar Prasetyo', 'Kevin Ardiansyah', 'Danu Wicaksono',
            'Rizqi Maulana', 'Hendra Kusuma', 'Aldy Firmansyah', 'Bima Sakti Wibowo',
            'Farhan Alamsyah', 'Zaky Mubarak', 'Galih Pangestu', 'Rian Hidayat',
            'Yusuf Al-Fatih', 'Daffa Ibnu Sina', 'Rio Febrian', 'Wildan Hakim',
            'Fikri Ardianto', 'Aditya Pratama', 'Alif Nur Hidayat', 'Andre Kurniawan',
            'Aris Munandar', 'Bagus Tri Atmojo', 'Candra Wijaya', 'Danang Suryanto',
            'Dodi Hermawan', 'Eko Wahyudi', 'Fahmi Idris', 'Guntur Bumi Pratama',
            'Hafiz Firmansyah', 'Ihsan Maulana', 'Jefri Ardiansyah', 'Lukman Hakim',
            'Miftah Farid', 'Naufal Abiyyu', 'Pandu Dewantara', 'Rafi Ahmad Hidayat',
            'Rendy Kurniawan', 'Satria Pamungkas', 'Taufik Hidayat', 'Wahyu Hidayatullah',
            'Yoga Pratama', 'Zainal Abidin', 'Arya Sena Wibawa', 'Brian Immanuel',
            'Cipto Raharjo', 'Doni Tata Pradana', 'Erwin Gutawa Saputra', 'Faisal Basri',
            'Gunawan Dwi Cahyo', 'Hendi Pratama', 'Irfan Bachdim Saputra', 'Joko Prasetyo',
            'Khoirul Anam', 'Lutfi Fauzan', 'Marwan Syahputra', 'Najib Habibi',
            'Oktavian Dwi Cahyo', 'Panji Gumilang Wibowo', 'Qori Iskandar', 'Reza Maulana Syahputra',
            'Syafiq Riza Rahman', 'Triyono Saputro', 'Ujang Supriatna', 'Vicky Prasetya',
            'Wisnu Wardhana', 'Yahya Waloni', 'Zulkifli Hasan', 'Ade Kurniawan',
            'Boby Santoso', 'Candra Gunawan', 'Deni Kurnia Sandy', 'Evan Dimas Saputra',
            'Febri Hariyanto', 'Gilang Permana Putra', 'Hariono Saputro', 'Indra Lesmana',
            'Jamaludin Al-Afghani', 'Kurnia Sandi', 'Lukman Nulhakim', 'Muchlis Tri Cahyono',
            'Nurhidayat Ramadhan', 'Oka Sulistyo', 'Pratama Yudha', 'Ricky Firmansyah',
            'Saddil Hidayat', 'Tomi Suhendra', 'Umar Bakri Santoso'
        ];

        // 55 Nama Perempuan Autentik Indonesia
        $namaPerempuan = [
            'Siti Nur Azizah', 'Amanda Putri Salsabila', 'Dwi Lestari', 'Nabila Zahra Ramadhani',
            'Rina Anggraini', 'Putri Aulia Rahma', 'Annisa Fitriani', 'Cindy Claudia Permata',
            'Dewi Sartika', 'Febriana Kusuma Wardani', 'Gita Maharani', 'Indah Puspitasari',
            'Maya Safira', 'Nadia Nurul Hidayah', 'Olivia Putri', 'Riska Amelia',
            'Salma Fauziyah', 'Tiara Maharani', 'Vina Septiana', 'Zahra Aurelia',
            'Alifia Syahrani', 'Berliana Cantika', 'Chairunisa Putri', 'Dina Marlina',
            'Elsa Pitaloka', 'Fitri Handayani', 'Hana Annisa', 'Intan Permatasari',
            'Juwita Baharudin', 'Karina Nadila', 'Larasati Wulandari', 'Mega Utami',
            'Nova Eliza', 'Oktavia Nur Halizah', 'Prilia Anggita', 'Qonita Diniyah',
            'Raisa Nur Fadilah', 'Salsabila Audiva', 'Tari Rahmadani', 'Ulfa Nurul Azizah',
            'Vivi Indriyani', 'Winda Astuti', 'Yasmin Aulia Rahmah', 'Zaskia Nur Hasanah',
            'Alya Nur Salsabila', 'Bunga Citra Kirana', 'Citra Tari Wardani', 'Dian Paramita',
            'Erina Dwi Cahyani', 'Febby Anggraini', 'Gita Septiani', 'Hesti Sundari',
            'Ika Rahmawati', 'Jelita Ayu Pramesti', 'Kusuma Ayu Wardani'
        ];

        // Kumpulan Asal Sekolah Menengah Pertama / MTs
        $sekolahList = [
            'SMP Negeri 1 Paguyangan',
            'SMP Negeri 2 Paguyangan',
            'MTs Muhammadiyah Paguyangan',
            'SMP Muhammadiyah Bumiayu',
            'SMP Negeri 1 Bumiayu',
            'SMP Negeri 2 Bumiayu',
            'SMP Negeri 3 Bumiayu',
            'MTs Negeri 2 Brebes',
            'MTs Ma\'arif NU 1 Paguyangan',
            'SMP Negeri 1 Tonjong',
            'SMP Negeri 2 Tonjong',
            'MTs Muhammadiyah Tonjong',
            'SMP Negeri 1 Sirampog',
            'SMP Negeri 2 Sirampog',
            'MTs Al-Hikmah 1 Benda Sirampog',
            'MTs Al-Hikmah 2 Benda Sirampog',
            'SMP IT Darussalam Paguyangan',
            'SMP Negeri 1 Ajibarang',
            'MTs Ma\'arif NU 1 Ajibarang',
            'SMP Negeri 1 Bantarkawung',
            'SMP Negeri 1 Salem',
            'SMP Negeri 2 Bantarkawung',
            'SMP Muhammadiyah Sirampog',
            'SMP Negeri 1 Larangan',
        ];

        // Kumpulan Alamat & Desa Nyata di Brebes & Banyumas
        $alamatList = [
            'RT 02 / RW 04, Desa Ragatunjung, Kec. Paguyangan, Kab. Brebes',
            'RT 01 / RW 03, Desa Wanatirta, Kec. Paguyangan, Kab. Brebes',
            'RT 05 / RW 02, Desa Pagojengan, Kec. Paguyangan, Kab. Brebes',
            'RT 03 / RW 01, Desa Winduaji, Kec. Paguyangan, Kab. Brebes',
            'RT 04 / RW 06, Desa Taraban, Kec. Paguyangan, Kab. Brebes',
            'RT 02 / RW 02, Desa Kretek, Kec. Paguyangan, Kab. Brebes',
            'RT 01 / RW 05, Desa Cilibur, Kec. Paguyangan, Kab. Brebes',
            'RT 06 / RW 03, Desa Kaligua, Kec. Paguyangan, Kab. Brebes',
            'RT 03 / RW 02, Desa Pakujati, Kec. Paguyangan, Kab. Brebes',
            'RT 04 / RW 02, Desa Pandansari, Kec. Paguyangan, Kab. Brebes',
            'RT 02 / RW 01, Desa Dukuhturi, Kec. Bumiayu, Kab. Brebes',
            'RT 04 / RW 05, Desa Jatisawit, Kec. Bumiayu, Kab. Brebes',
            'RT 01 / RW 04, Desa Kalierang, Kec. Bumiayu, Kab. Brebes',
            'RT 03 / RW 07, Desa Langkap, Kec. Bumiayu, Kab. Brebes',
            'RT 05 / RW 02, Desa Pruwatan, Kec. Bumiayu, Kab. Brebes',
            'RT 02 / RW 03, Desa Negaradaha, Kec. Bumiayu, Kab. Brebes',
            'RT 01 / RW 02, Desa Penggarit, Kec. Bumiayu, Kab. Brebes',
            'RT 04 / RW 01, Desa Linggapura, Kec. Tonjong, Kab. Brebes',
            'RT 02 / RW 04, Desa Kutamendala, Kec. Tonjong, Kab. Brebes',
            'RT 03 / RW 05, Desa Pepedan, Kec. Tonjong, Kab. Brebes',
            'RT 01 / RW 03, Desa Tanggeran, Kec. Tonjong, Kab. Brebes',
            'RT 02 / RW 02, Desa Igirklanceng, Kec. Sirampog, Kab. Brebes',
            'RT 05 / RW 04, Desa Mlayang, Kec. Sirampog, Kab. Brebes',
            'RT 01 / RW 01, Desa Mendala, Kec. Sirampog, Kab. Brebes',
            'RT 03 / RW 06, Desa Buniwah, Kec. Sirampog, Kab. Brebes',
            'RT 02 / RW 05, Desa Ajibarang Kulon, Kec. Ajibarang, Kab. Banyumas',
            'RT 01 / RW 02, Desa Ajibarang Wetan, Kec. Ajibarang, Kab. Banyumas',
            'RT 03 / RW 04, Desa Kranggan, Kec. Pekuncen, Kab. Banyumas',
            'RT 02 / RW 01, Desa Pangebatan, Kec. Bantarkawung, Kab. Brebes',
            'RT 04 / RW 03, Desa Banjarsari, Kec. Bantarkawung, Kab. Brebes',
        ];

        // Tempat Lahir
        $tempatLahirList = [
            'Brebes', 'Bumiayu', 'Paguyangan', 'Sirampog', 'Tonjong',
            'Banyumas', 'Ajibarang', 'Purwokerto', 'Tegal', 'Slawi',
            'Salem', 'Bantarkawung', 'Cilacap', 'Purbalingga'
        ];

        // Nama Orang Tua (Ayah / Ibu)
        $namaOrangTuaList = [
            'Bambang Suhartono', 'Slamet Riyadi', 'Sutrisno Wibowo', 'Joko Susilo',
            'Agus Purwanto', 'Budi Santoso', 'Hendro Prasetyo', 'Suherman Wijaya',
            'Dedi Supriyadi', 'Subagyo Raharjo', 'Wahyudi Pratama', 'Sugeng Priyono',
            'Eko Prasetyo', 'Rohim Hidayat', 'Sukirman', 'Kuswanto',
            'Mulyono Hadi', 'Herman Wijaya', 'Sri Rahayu', 'Siti Khotimah',
            'Endang Sulastri', 'Tri Wahyuni', 'Nurhayati', 'Ahmad Darsono',
            'Maryanto', 'Saryono', 'Ruswanto', 'Sunardi Saputra',
            'Supriyanto', 'Taryono', 'Wawan Setiawan', 'Yanto Subekti',
            'Zaenal Arifin', 'Ansori', 'Basuki Rahmat', 'Casmadi',
            'Darsum', 'Edy Sutanto', 'Fatkhur Rohman', 'Giman Susanto'
        ];

        // Rencana Alokasi Jurusan & Gender untuk 150 Siswa:
        // TKR: 32 siswa (28 L, 4 P)
        // TSM: 34 siswa (30 L, 4 P)
        // TOTR: 24 siswa (21 L, 3 P)
        // RPL: 32 siswa (16 L, 16 P)
        // BROADCASTING: 28 siswa (10 L, 18 P)
        // Total L = 105, Total P = 45 => Total 150
        $distribusi = [
            'TKR' => ['L' => 28, 'P' => 4],
            'TSM' => ['L' => 30, 'P' => 4],
            'TOTR' => ['L' => 21, 'P' => 3],
            'RPL' => ['L' => 16, 'P' => 16],
            'BROADCASTING' => ['L' => 10, 'P' => 18],
        ];

        $daftarHasil = [];
        $ptrL = 0;
        $ptrP = 0;
        $globalIdx = 1;

        foreach ($distribusi as $jurusanKode => $counts) {
            $totalJurusan = $counts['L'] + $counts['P'];
            $itemsJurusan = [];

            // Sisipkan Laki-laki
            for ($i = 0; $i < $counts['L']; $i++) {
                $nama = $namaLakiLaki[$ptrL % count($namaLakiLaki)];
                $ptrL++;
                $itemsJurusan[] = ['nama' => $nama, 'jk' => 'L'];
            }

            // Sisipkan Perempuan
            for ($i = 0; $i < $counts['P']; $i++) {
                $nama = $namaPerempuan[$ptrP % count($namaPerempuan)];
                $ptrP++;
                $itemsJurusan[] = ['nama' => $nama, 'jk' => 'P'];
            }

            // Shuffle deterministik per jurusan agar urutan L dan P bercampur natural
            // Gunakan seed tetap agar hasilnya konsisten
            mt_srand(42 + $globalIdx);
            shuffle($itemsJurusan);

            foreach ($itemsJurusan as $item) {
                // Tanggal Lahir antara 2009-01-10 s.d. 2011-06-25
                $tahunLahir = 2009 + ($globalIdx % 3); // 2009, 2010, atau 2011
                $bulanLahir = ($globalIdx % 12) + 1;
                $hariLahir = ($globalIdx % 28) + 1;
                $tglLahirStr = sprintf('%04d-%02d-%02d', $tahunLahir, $bulanLahir, $hariLahir);

                // NIK unik 16 digit format Jawa Tengah Brebes (3329...)
                // Digit 7-8: tanggal lahir (jika P, hari + 40 sesuai format KTP Indonesia)
                $hariNik = ($item['jk'] === 'P') ? ($hariLahir + 40) : $hariLahir;
                $nik = sprintf('332901%02d%02d%02d%04d', $hariNik, $bulanLahir, $tahunLahir % 100, $globalIdx + 100);

                // NISN unik 10 digit (misal: 009xxxxxxx atau 010xxxxxxx)
                $nisn = sprintf('00%d%07d', $tahunLahir % 10, $globalIdx + 2451000);

                // Jalur Pendaftaran: Siswa 1 - 35 Prestasi, sisanya Umum
                $jalurDaftar = ($globalIdx <= 35) ? 'prestasi' : 'umum';

                // Jalur Registrasi: ~70% online, ~30% offline
                $jalurReg = ($globalIdx % 3 === 0) ? 'offline' : 'online';

                // No HP Orang Tua format Indonesia variatif
                $prefixes = ['0812', '0813', '0821', '0852', '0856', '0857', '0858', '0877', '0878', '0895', '0896', '0838'];
                $prefix = $prefixes[$globalIdx % count($prefixes)];
                $noHp = $prefix . sprintf('%08d', 11000000 + ($globalIdx * 12345) % 88999999);

                $daftarHasil[] = [
                    'nama_lengkap' => $item['nama'],
                    'jenis_kelamin' => $item['jk'],
                    'jurusan' => $jurusanKode,
                    'jalur_pendaftaran' => $jalurDaftar,
                    'jalur_registrasi' => $jalurReg,
                    'nik' => $nik,
                    'nisn' => $nisn,
                    'tempat_lahir' => $tempatLahirList[$globalIdx % count($tempatLahirList)],
                    'tanggal_lahir' => $tglLahirStr,
                    'alamat' => $alamatList[$globalIdx % count($alamatList)],
                    'asal_sekolah' => $sekolahList[$globalIdx % count($sekolahList)],
                    'nama_orang_tua' => $namaOrangTuaList[$globalIdx % count($namaOrangTuaList)],
                    'no_hp_orang_tua' => $noHp,
                ];

                $globalIdx++;
            }
        }

        return $daftarHasil;
    }
}
