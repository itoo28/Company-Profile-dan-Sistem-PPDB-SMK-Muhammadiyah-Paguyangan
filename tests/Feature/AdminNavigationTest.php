<?php

namespace Tests\Feature;

use App\Models\BerkasSiswa;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\JenisBerkas;
use App\Models\Pembayaran;
use App\Models\PesanMasuk;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
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

    public function test_sidebar_super_admin_renders_streamlined_groups(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Header grup baru
        $response->assertSee('Operasional PPDB');
        $response->assertSee('Konten Website');
        $response->assertSee('Konfigurasi PPDB');
        $response->assertSee('Sistem & Laporan');

        // Item ringkas
        $response->assertSee('Data Pendaftar');
        $response->assertSee('Warta Sekolah');
        $response->assertSee('Profil Sekolah');
        $response->assertSee('Setup PPDB');
        $response->assertSee('Inbox Kontak');
    }

    public function test_sidebar_admin_piket_is_clean_and_only_shows_permitted_modules(): void
    {
        $response = $this->actingAs($this->adminPiket)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Operasional yang diizinkan
        $response->assertSee('Data Pendaftar');
        $response->assertSee('Pendaftaran Offline');
        $response->assertSee('Pembayaran PPDB');
        $response->assertSee('Loket Seragam');
        $response->assertSee('Warta Sekolah');

        // Modul terlarang bagi Admin Piket tidak boleh muncul di sidebar
        $response->assertDontSee('Profil Sekolah');
        $response->assertDontSee('Setup PPDB');
        $response->assertDontSee('Inbox Kontak');
        $response->assertDontSee('Ekspor Laporan');
        $response->assertDontSee('Akun Staf Piket');
        $response->assertDontSee('Log Aktivitas');
    }

    public function test_subnav_tabs_warta_sekolah_renders_correctly(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.berita.index'));
        $response->assertStatus(200);

        $response->assertSee('admin-subnav-wrapper');
        $response->assertSee(route('admin.berita.index'));
        $response->assertSee(route('admin.prestasi.index'));
        $response->assertSee(route('admin.pengumuman.index'));
        $response->assertSee('Kelola Berita');
        $response->assertSee('Kelola Prestasi');
        $response->assertSee('Pengumuman Resmi');

        // Untuk Admin Piket, tab Pengumuman Resmi disembunyikan
        $piketResponse = $this->actingAs($this->adminPiket)->get(route('admin.berita.index'));
        $piketResponse->assertStatus(200);
        $piketResponse->assertSee('Kelola Berita');
        $piketResponse->assertSee('Kelola Prestasi');
        $piketResponse->assertDontSee('Pengumuman Resmi');
    }

    public function test_subnav_tabs_profil_sekolah_renders_correctly(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.informasi-sekolah.index'));
        $response->assertStatus(200);

        $response->assertSee('admin-subnav-wrapper');
        $response->assertSee(route('admin.informasi-sekolah.index'));
        $response->assertSee(route('admin.fasilitas.index'));
        $response->assertSee(route('admin.ekstrakurikuler.index'));
        $response->assertSee(route('admin.galeri.index'));
        $response->assertSee('Visi, Misi', false);
        $response->assertSee('Sarana & Fasilitas', false);
        $response->assertSee('Ekstrakurikuler');
        $response->assertSee('Galeri Foto');
    }

    public function test_subnav_tabs_setup_ppdb_renders_correctly(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.tahun-ajaran.index'));
        $response->assertStatus(200);

        $response->assertSee('admin-subnav-wrapper');
        $response->assertSee(route('admin.tahun-ajaran.index'));
        $response->assertSee(route('admin.gelombang.index'));
        $response->assertSee(route('admin.jurusan.index'));
        $response->assertSee(route('admin.jenis-berkas.index'));
        $response->assertSee(route('admin.pengaturan.index'));
        $response->assertSee('Tahun Ajaran');
        $response->assertSee('Gelombang PPDB');
        $response->assertSee('Jurusan Kejuruan');
        $response->assertSee('Syarat Berkas');
        $response->assertSee('Pengaturan PPDB');
    }
}
