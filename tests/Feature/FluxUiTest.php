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
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($superAdmin)->get(route($route));
            $response->assertStatus(200);
        }
    }
}

