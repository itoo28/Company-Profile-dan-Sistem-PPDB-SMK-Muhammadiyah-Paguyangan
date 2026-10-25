<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTahunAjaranTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminPiket;
    protected TahunAjaran $tahunAjaranAktif;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->superAdmin = User::where('role', 'super_admin')->first();
        $this->adminPiket = User::where('role', 'admin_piket')->first();
        $this->tahunAjaranAktif = TahunAjaran::aktif() ?? TahunAjaran::first();
    }

    public function test_super_admin_dapat_melihat_daftar_tahun_ajaran(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.tahun-ajaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Tahun Ajaran PPDB');
        $response->assertSee($this->tahunAjaranAktif->nama);
    }

    public function test_admin_piket_tidak_berhak_mengakses_manajemen_tahun_ajaran(): void
    {
        $response = $this->actingAs($this->adminPiket, 'web')
            ->get(route('admin.tahun-ajaran.index'));

        $response->assertStatus(403);
    }

    public function test_super_admin_dapat_menambah_tahun_ajaran_baru(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.tahun-ajaran.store'), [
                'nama' => '2027/2028',
                'is_aktif' => 0,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('tahun_ajarans', [
            'nama' => '2027/2028',
            'is_aktif' => false,
        ]);
    }

    public function test_super_admin_dapat_mengaktifkan_tahun_ajaran_baru_dan_mengarsipkan_tahun_lama(): void
    {
        $tahunBaru = TahunAjaran::create([
            'nama' => '2027/2028',
            'is_aktif' => false,
        ]);

        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.tahun-ajaran.aktifkan', $tahunBaru->id));

        $response->assertRedirect();
        $response->assertSessionHas('sukses');

        $this->tahunAjaranAktif->refresh();
        $tahunBaru->refresh();

        $this->assertFalse($this->tahunAjaranAktif->is_aktif, 'Tahun ajaran lama harus beralih menjadi nonaktif/arsip');
        $this->assertTrue($tahunBaru->is_aktif, 'Tahun ajaran baru harus aktif');
    }

    public function test_data_pendaftar_tahun_lama_tetap_tersimpan_dan_dapat_dilihat_via_filter_arsip(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('tahun_ajaran_id', $this->tahunAjaranAktif->id)->first();

        // Buat data pendaftar pada tahun 2026/2027
        $siswaLama = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/2026/0088',
            'nomor_urut' => 88,
            'nama_lengkap' => 'Siswa Periode Lama',
            'nik' => '3329012304080088',
            'nisn' => '0081234088',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1 Paguyangan',
            'nama_orang_tua' => 'Orang Tua',
            'no_hp_orang_tua' => '081234567888',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('01012008'),
        ]);

        // Aktifkan tahun ajaran baru 2027/2028
        $tahunBaru = TahunAjaran::create([
            'nama' => '2027/2028',
            'is_aktif' => true,
        ]);
        $this->tahunAjaranAktif->update(['is_aktif' => false]);

        // Pada dashboard tahun baru (default), pendaftar tahun lama tidak dihitung di periode berjalan
        $responseDashboardBaru = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.dashboard'));
        $responseDashboardBaru->assertStatus(200);
        $responseDashboardBaru->assertViewHas('totalPendaftar', 0);

        // Namun saat memilih filter arsip tahun lama, data siswa lama muncul lengkap
        $responseDashboardArsip = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.dashboard', ['tahun_ajaran_id' => $this->tahunAjaranAktif->id]));
        $responseDashboardArsip->assertStatus(200);
        $responseDashboardArsip->assertViewHas('totalPendaftar', 1);

        // Cek juga di halaman pendaftar index
        $responsePendaftarArsip = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.pendaftar.index', ['tahun_ajaran_id' => $this->tahunAjaranAktif->id]));
        $responsePendaftarArsip->assertStatus(200);
        $responsePendaftarArsip->assertSee('Siswa Periode Lama');
        $responsePendaftarArsip->assertSee('Arsip Data Calon Siswa Tahun Ajaran');
    }

    public function test_tahun_ajaran_yang_memiliki_data_pendaftar_tidak_dapat_dihapus(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('tahun_ajaran_id', $this->tahunAjaranAktif->id)->first();

        CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/2026/0077',
            'nomor_urut' => 77,
            'nama_lengkap' => 'Siswa Penting',
            'nik' => '3329012304080077',
            'nisn' => '0081234077',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Orang Tua',
            'no_hp_orang_tua' => '081234567877',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('01012008'),
        ]);

        $response = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.tahun-ajaran.destroy', $this->tahunAjaranAktif->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('tahun_ajarans', [
            'id' => $this->tahunAjaranAktif->id,
        ]);
    }
}
