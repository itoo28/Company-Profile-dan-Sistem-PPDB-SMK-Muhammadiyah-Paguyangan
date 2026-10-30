<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class FluxUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_flux_directives_and_components_render_correctly(): void
    {
        $rendered = Blade::render('<flux:badge color="zinc">Uji Flux</flux:badge>');
        $this->assertStringContainsString('Uji Flux', $rendered);

        $button = Blade::render('<flux:button variant="primary">Simpan</flux:button>');
        $this->assertStringContainsString('Simpan', $button);
    }

    public function test_all_admin_pages_render_successfully_with_flux_ui(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $superAdmin = User::where('role', 'super_admin')->first();

        $routes = [
            'admin.dashboard',
            'admin.pendaftar.index',
            'admin.pendaftar.baru',
            'admin.seragam.index',
            'admin.berita.index',
            'admin.berita.create',
            'admin.prestasi.index',
            'admin.prestasi.create',
            'admin.gelombang.index',
            'admin.jurusan.index',
            'admin.jenis-berkas.index',
            'admin.pesan-masuk.index',
            'admin.ekspor.index',
            'admin.pengaturan.index',
            'admin.staf.index',
            'admin.log.index',
            'admin.pengumuman.index',
            'admin.fasilitas.index',
            'admin.ekstrakurikuler.index',
            'admin.galeri.index',
            'admin.informasi-sekolah.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($superAdmin)->get(route($route));
            $response->assertStatus(200);
        }
    }

    public function test_admin_pesan_masuk_dapat_diakses_dan_diupdate_statusnya(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $superAdmin = User::where('role', 'super_admin')->first();

        $pesan = \App\Models\PesanMasuk::create([
            'nama' => 'Budi Santoso',
            'kontak' => '081234567890',
            'subjek' => 'Tanya Pendaftaran',
            'pesan' => 'Halo, apakah pendaftaran gelombang 1 masih buka?',
            'status' => 'belum_dibaca',
        ]);

        // Verifikasi halaman index dengan baris pesan ter-render tanpa error
        $response = $this->actingAs($superAdmin)->get(route('admin.pesan-masuk.index'));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee(route('admin.pesan-masuk.status', $pesan->id));

        // Verifikasi rute admin.pesan-masuk.status berfungsi
        $patchStatus = $this->actingAs($superAdmin)->patch(route('admin.pesan-masuk.status', $pesan->id), [
            'status' => 'sudah_dibaca',
        ]);
        $patchStatus->assertSessionHas('sukses');
        $this->assertDatabaseHas('pesan_masuks', ['id' => $pesan->id, 'status' => 'sudah_dibaca']);

        // Verifikasi alias rute admin.pesan-masuk.update-status juga berfungsi
        $patchUpdateStatus = $this->actingAs($superAdmin)->patch(route('admin.pesan-masuk.update-status', $pesan->id), [
            'status' => 'dibalas',
        ]);
        $patchUpdateStatus->assertSessionHas('sukses');
        $this->assertDatabaseHas('pesan_masuks', ['id' => $pesan->id, 'status' => 'dibalas']);
    }
}

