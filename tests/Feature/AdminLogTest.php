<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminPiket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->superAdmin = User::where('role', 'super_admin')->first();
        $this->adminPiket = User::where('role', 'admin_piket')->first();
    }

    public function test_super_admin_dapat_mengakses_halaman_log_aktivitas(): void
    {
        LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => $this->superAdmin->name,
            'peran' => 'super_admin',
            'jenis_aksi' => 'login',
            'objek' => 'Autentikasi Staf',
            'keterangan' => 'Login berhasil sebagai super_admin.',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.log.index'));

        $response->assertOk();
        $response->assertSee('Total Jejak Audit');
        $response->assertSee('Aktivitas Hari Ini');
        $response->assertSee('Peringatan Keamanan');
        $response->assertSee('Autentikasi Staf');
        $response->assertSee('Login Berhasil');
        $response->assertSee('admin-desktop-table');
        $response->assertSee('admin-mobile-cards');
    }

    public function test_admin_piket_dilarang_mengakses_halaman_log_aktivitas(): void
    {
        $response = $this->actingAs($this->adminPiket)->get(route('admin.log.index'));

        $response->assertStatus(403);
    }

    public function test_filter_log_berdasarkan_kata_kunci_q(): void
    {
        LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => 'Super Admin Khusus',
            'peran' => 'super_admin',
            'jenis_aksi' => 'hapus_pendaftar',
            'objek' => 'Calon Siswa',
            'keterangan' => 'Menghapus pendaftar NIK 3329012345670001',
            'ip_address' => '192.168.1.50',
        ]);

        LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => 'Staf Lain',
            'peran' => 'admin_piket',
            'jenis_aksi' => 'tambah_berita',
            'objek' => 'Warta & Berita',
            'keterangan' => 'Publikasi artikel warta sekolah',
            'ip_address' => '192.168.1.100',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.log.index', ['q' => '3329012345670001']));

        $response->assertOk();
        $response->assertSee('Super Admin Khusus');
        $response->assertSee('Hapus Pendaftar');
        $response->assertDontSee('Publikasi artikel warta sekolah');
    }

    public function test_filter_log_berdasarkan_modul_objek(): void
    {
        LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => $this->superAdmin->name,
            'peran' => 'super_admin',
            'jenis_aksi' => 'verifikasi_pembayaran',
            'objek' => 'Pembayaran PPDB',
            'keterangan' => 'Verifikasi transfer bayar loket #TRX-998',
            'ip_address' => '127.0.0.1',
        ]);

        LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => $this->superAdmin->name,
            'peran' => 'super_admin',
            'jenis_aksi' => 'tambah_prestasi',
            'objek' => 'Prestasi Siswa',
            'keterangan' => 'Juara 1 LKS Otomotif',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.log.index', ['objek' => 'Pembayaran PPDB']));

        $response->assertOk();
        $response->assertSee('TRX-998');
        $response->assertDontSee('Juara 1 LKS Otomotif');
    }

    public function test_filter_log_berdasarkan_kategori_aksi(): void
    {
        LogAktivitas::create([
            'user_id' => null,
            'nama_pelaku' => 'Sistem',
            'peran' => 'sistem',
            'jenis_aksi' => 'login_gagal',
            'objek' => 'Autentikasi Staf',
            'keterangan' => 'Percobaan login salah password untuk akun admin_piket',
            'ip_address' => '180.252.10.5',
        ]);

        LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => $this->superAdmin->name,
            'peran' => 'super_admin',
            'jenis_aksi' => 'tambah_jurusan',
            'objek' => 'Konsentrasi Keahlian',
            'keterangan' => 'Menambahkan jurusan TKRO',
            'ip_address' => '127.0.0.1',
        ]);

        // Filter kategori keamanan
        $response = $this->actingAs($this->superAdmin)->get(route('admin.log.index', ['kategori_aksi' => 'keamanan']));

        $response->assertOk();
        $response->assertSee('Gagal Login');
        $response->assertSee('Percobaan login salah password');
        $response->assertDontSee('Menambahkan jurusan TKRO');
    }

    public function test_filter_log_berdasarkan_rentang_tanggal(): void
    {
        $logKemarin = LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => $this->superAdmin->name,
            'peran' => 'super_admin',
            'jenis_aksi' => 'login',
            'objek' => 'Autentikasi Staf',
            'keterangan' => 'Log kemarin',
            'ip_address' => '127.0.0.1',
        ]);
        $logKemarin->timestamps = false;
        $logKemarin->created_at = now()->subDays(5);
        $logKemarin->save();

        $logHariIni = LogAktivitas::create([
            'user_id' => $this->superAdmin->id,
            'nama_pelaku' => $this->superAdmin->name,
            'peran' => 'super_admin',
            'jenis_aksi' => 'login',
            'objek' => 'Autentikasi Staf',
            'keterangan' => 'Log hari ini khusus',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.log.index', [
            'tanggal_mulai' => now()->subDay()->format('Y-m-d'),
            'tanggal_selesai' => now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertSee('Log hari ini khusus');
        $response->assertDontSee('Log kemarin');
    }
}
