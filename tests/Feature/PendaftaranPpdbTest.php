<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PendaftaranPpdbTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        // Pastikan gelombang pendaftaran aktif mencakup hari ini agar pendaftaran online valid
        Gelombang::where('is_aktif', true)->first()?->update([
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
        ]);
    }

    public function test_halaman_pendaftaran_ppdb_dapat_diakses(): void
    {
        $response = $this->get('/ppdb/daftar');

        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran Siswa Baru');
        $response->assertSee('Rekayasa Perangkat Lunak');
    }

    public function test_calon_siswa_dapat_mendaftar_secara_online_dan_mendapatkan_nomor_pendaftaran(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();

        $payload = [
            'nama_lengkap' => 'Muhammad Rifqi Pratama',
            'nik' => '3329012304080001',
            'nisn' => '0081234567',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Karanganyar No. 45, Paguyangan',
            'asal_sekolah' => 'SMP Negeri 1 Paguyangan',
            'nama_orang_tua' => 'Bambang Sutrisno',
            'no_hp_orang_tua' => '081234567890',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'jalur_pendaftaran' => 'umum',
            'persetujuan_data' => '1',
        ];

        $response = $this->post('/ppdb/daftar', $payload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('calon_siswas', [
            'nik' => '3329012304080001',
            'nisn' => '0081234567',
            'nama_lengkap' => 'Muhammad Rifqi Pratama',
            'jalur_registrasi' => 'online',
            'nomor_urut' => 1,
            'status_benefit' => 'kandidat_100_pertama',
        ]);

        $siswa = CalonSiswa::where('nik', '3329012304080001')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals('PPDB/' . date('Y') . '/0001', $siswa->nomor_pendaftaran);
        
        // Password harus ter-hash dari tanggal lahir format DDMMYYYY (15052008)
        $this->assertTrue(Hash::check('15052008', $siswa->password));

        // Redirect ke halaman berhasil
        $response->assertRedirect(route('ppdb.berhasil'));
    }

    public function test_pendaftaran_gagal_jika_nik_kurang_dari_16_digit(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();

        $payload = [
            'nama_lengkap' => 'Siti Nurhaliza',
            'nik' => '332901230408', // Hanya 12 digit (tidak valid)
            'nisn' => '0081234568',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-08-20',
            'jenis_kelamin' => 'P',
            'alamat' => 'Desa Taraban',
            'asal_sekolah' => 'MTs Negeri Paguyangan',
            'nama_orang_tua' => 'Ahmad',
            'no_hp_orang_tua' => '081298765432',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'jalur_pendaftaran' => 'umum',
            'persetujuan_data' => '1',
        ];

        $response = $this->from('/ppdb/daftar')->post('/ppdb/daftar', $payload);

        $response->assertSessionHasErrors(['nik']);
        $this->assertDatabaseMissing('calon_siswas', ['nama_lengkap' => 'Siti Nurhaliza']);
    }

    public function test_duplikasi_nik_pada_tahun_ajaran_yang_sama_ditolak(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();

        // Pendaftar pertama
        CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0001',
            'nomor_urut' => 1,
            'nama_lengkap' => 'Pendaftar Pertama',
            'nik' => '3329012304089999',
            'nisn' => '0089999991',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat A',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Orang Tua A',
            'no_hp_orang_tua' => '0811111111',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => TahunAjaran::first()->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('01012008'),
        ]);

        // Pendaftar kedua mencoba dengan NIK sama
        $payload = [
            'nama_lengkap' => 'Pendaftar Kedua Duplikat',
            'nik' => '3329012304089999', // NIK sama persis
            'nisn' => '0089999992',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-02-02',
            'jenis_kelamin' => 'P',
            'alamat' => 'Alamat B',
            'asal_sekolah' => 'SMP 2',
            'nama_orang_tua' => 'Orang Tua B',
            'no_hp_orang_tua' => '0822222222',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'jalur_pendaftaran' => 'reguler',
            'persetujuan_data' => '1',
        ];

        $response = $this->from('/ppdb/daftar')->post('/ppdb/daftar', $payload);

        $response->assertSessionHasErrors(['nik']);
        $this->assertDatabaseMissing('calon_siswas', ['nama_lengkap' => 'Pendaftar Kedua Duplikat']);
    }

    public function test_cetak_bukti_pendaftaran_dapat_dibuka(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();

        $siswa = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0005',
            'nomor_urut' => 5,
            'nama_lengkap' => 'Anisa Rahmawati',
            'nik' => '3329012304080005',
            'nisn' => '0081234505',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-03-25',
            'jenis_kelamin' => 'P',
            'alamat' => 'Desa Paguyangan',
            'asal_sekolah' => 'SMP 2 Paguyangan',
            'nama_orang_tua' => 'Supriyadi',
            'no_hp_orang_tua' => '081233445566',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => TahunAjaran::first()->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('25032008'),
        ]);

        // 1. Akses sah oleh pendaftar yang memiliki session aktif setelah daftar mandiri
        $response = $this->withSession(['ppdb_sukses_id' => $siswa->id])
            ->get(route('ppdb.bukti', $siswa->id));

        $response->assertStatus(200);
        $response->assertSee('BUKTI PENDAFTARAN');
        $response->assertSee('PPDB/' . date('Y') . '/0005');
        $response->assertSee('Anisa Rahmawati');
    }

    public function test_cetak_bukti_pendaftaran_ditolak_jika_diakses_oleh_tamu_tanpa_sesi(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();

        $siswa = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0006',
            'nomor_urut' => 6,
            'nama_lengkap' => 'Rian Hidayat',
            'nik' => '3329012304080006',
            'nisn' => '0081234506',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-04-12',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Hidayat',
            'no_hp_orang_tua' => '081233445577',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => TahunAjaran::first()->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('12042008'),
        ]);

        // Tamu tanpa sesi / login diarahkan ke halaman login portal siswa
        $response = $this->get(route('ppdb.bukti', $siswa->id));
        $response->assertRedirect(route('siswa.login'));
    }

    public function test_pendaftaran_gagal_jika_tidak_ada_gelombang_aktif(): void
    {
        // Hapus semua gelombang di database
        Gelombang::query()->delete();

        $jurusan = Jurusan::first();

        $payload = [
            'nama_lengkap' => 'Tes Tanpa Gelombang',
            'nik' => '3329012304081111',
            'nisn' => '0081234111',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Orang Tua',
            'no_hp_orang_tua' => '081234567811',
            'jurusan_id' => $jurusan->id,
            'jalur_pendaftaran' => 'umum',
        ];

        $response = $this->from('/ppdb/daftar')->post('/ppdb/daftar', $payload);

        $response->assertRedirect('/ppdb/daftar');
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('calon_siswas', ['nik' => '3329012304081111']);
    }

    public function test_pendaftaran_memiliki_proteksi_rate_limiting(): void
    {
        $jurusan = Jurusan::first();

        // 10 request pertama masih dalam kuota rate limiting
        for ($i = 1; $i <= 10; $i++) {
            $this->post('/ppdb/daftar', [
                'nama_lengkap' => "Calon Siswa {$i}",
                'nik' => sprintf('332901230408%04d', $i + 500),
                'nisn' => sprintf('008123%04d', $i + 500),
                'tempat_lahir' => 'Brebes',
                'tanggal_lahir' => '2008-01-01',
                'jenis_kelamin' => 'L',
                'alamat' => 'Paguyangan',
                'asal_sekolah' => 'SMP 1',
                'nama_orang_tua' => 'Ortu',
                'no_hp_orang_tua' => '081234567890',
                'jurusan_id' => $jurusan->id,
                'jalur_pendaftaran' => 'umum',
                'persetujuan_data' => '1',
            ]);
        }

        // Request ke-11 dalam 1 menit harus diblokir (HTTP 429 Too Many Requests)
        $blocked = $this->post('/ppdb/daftar', [
            'nama_lengkap' => 'Calon Siswa Ke 11',
            'nik' => '3329012304080999',
            'nisn' => '0081230999',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Ortu',
            'no_hp_orang_tua' => '081234567890',
            'jurusan_id' => $jurusan->id,
            'jalur_pendaftaran' => 'umum',
            'persetujuan_data' => '1',
        ]);

        $blocked->assertStatus(429);

        // Bersihkan cache rate-limiting agar tidak mempengaruhi test lain
        \Illuminate\Support\Facades\Cache::flush();
    }

    public function test_pendaftaran_mandiri_menormalisasi_format_nomor_hp_plus62_dan_62(): void
    {
        $jurusan = Jurusan::first();

        $response = $this->post('/ppdb/daftar', [
            'nama_lengkap' => 'Calon Siswa Plus62',
            'nik' => '3329012304087777',
            'nisn' => '0081237777',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Ortu',
            'no_hp_orang_tua' => '+62 812-9876-5432',
            'jurusan_id' => $jurusan->id,
            'jalur_pendaftaran' => 'umum',
            'persetujuan_data' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/ppdb/berhasil');

        $siswa = CalonSiswa::where('nik', '3329012304087777')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals('081298765432', $siswa->no_hp_orang_tua);
        $this->assertEquals('https://wa.me/6281298765432', $siswa->wa_link_orang_tua);
    }
}
