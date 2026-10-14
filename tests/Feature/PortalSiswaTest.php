<?php

namespace Tests\Feature;

use App\Models\BerkasSiswa;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\JenisBerkas;
use App\Models\Jurusan;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortalSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected CalonSiswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $this->siswa = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0010',
            'nomor_urut' => 10,
            'nama_lengkap' => 'Budi Setiawan',
            'nik' => '3329012304080010',
            'nisn' => '0081234510',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-07-21',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1 Paguyangan',
            'nama_orang_tua' => 'Kuswanto',
            'no_hp_orang_tua' => '081234567899',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('21072008'),
        ]);
    }

    public function test_halaman_login_siswa_dapat_diakses(): void
    {
        $response = $this->get('/siswa/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk Portal Siswa');
    }

    public function test_siswa_dapat_login_menggunakan_nomor_pendaftaran_dan_tanggal_lahir(): void
    {
        $response = $this->post('/siswa/login', [
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0010',
            'tanggal_lahir' => '2008-07-21',
        ]);

        $response->assertRedirect(route('siswa.dashboard'));
        $this->assertTrue(Auth::guard('siswa')->check());
        $this->assertEquals($this->siswa->id, Auth::guard('siswa')->id());
    }

    public function test_login_siswa_gagal_jika_tanggal_lahir_salah(): void
    {
        $response = $this->from('/siswa/login')->post('/siswa/login', [
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0010',
            'tanggal_lahir' => '2008-01-01', // Salah
        ]);

        $response->assertRedirect('/siswa/login');
        $response->assertSessionHasErrors(['auth']);
        $this->assertFalse(Auth::guard('siswa')->check());
    }

    public function test_siswa_dapat_mengunggah_berkas_ke_storage_privat(): void
    {
        Storage::fake('local');

        $jenisBerkas = JenisBerkas::where('aturan_wajib', 'semua')->first();
        $file = UploadedFile::fake()->create('dokumen_skl.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.berkas.upload'), [
                'jenis_berkas_id' => $jenisBerkas->id,
                'berkas' => $file,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('siswa.dashboard'));

        $this->assertDatabaseHas('berkas_siswas', [
            'calon_siswa_id' => $this->siswa->id,
            'jenis_berkas_id' => $jenisBerkas->id,
            'status' => 'menunggu',
        ]);

        $berkas = BerkasSiswa::where('calon_siswa_id', $this->siswa->id)
            ->where('jenis_berkas_id', $jenisBerkas->id)
            ->first();

        $this->assertNotNull($berkas);
        Storage::disk('local')->assertExists($berkas->file_path);
    }

    public function test_unggah_berkas_ditolak_jika_lockdown_aktif_tanpa_bypass(): void
    {
        Storage::fake('local');

        // Set batas waktu di masa lalu (lockdown)
        $pengaturan = PengaturanSistem::instance();
        $pengaturan->update([
            'batas_waktu_berkas' => now()->subDay(),
        ]);

        $jenisBerkas = JenisBerkas::first();
        $file = UploadedFile::fake()->create('kartu_keluarga.jpg', 300, 'image/jpeg');

        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.berkas.upload'), [
                'jenis_berkas_id' => $jenisBerkas->id,
                'berkas' => $file,
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('berkas_siswas', [
            'calon_siswa_id' => $this->siswa->id,
            'jenis_berkas_id' => $jenisBerkas->id,
        ]);
    }

    public function test_unggah_berkas_diizinkan_jika_admin_mengaktifkan_bypass_lockdown(): void
    {
        Storage::fake('local');

        // Batas waktu sudah lewat
        $pengaturan = PengaturanSistem::instance();
        $pengaturan->update([
            'batas_waktu_berkas' => now()->subDay(),
        ]);

        // Berikan bypass unlock kepada siswa ini
        $this->siswa->update(['is_unlocked' => true]);

        $jenisBerkas = JenisBerkas::first();
        $file = UploadedFile::fake()->create('foto_rapor.png', 400, 'image/png');

        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.berkas.upload'), [
                'jenis_berkas_id' => $jenisBerkas->id,
                'berkas' => $file,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('berkas_siswas', [
            'calon_siswa_id' => $this->siswa->id,
            'jenis_berkas_id' => $jenisBerkas->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_siswa_tidak_dapat_melihat_berkas_milik_siswa_lain(): void
    {
        Storage::fake('local');

        // Buat siswa kedua
        $siswaLain = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0099',
            'nomor_urut' => 99,
            'nama_lengkap' => 'Siswa Lain',
            'nik' => '3329012304080099',
            'nisn' => '0081234099',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Orang Tua',
            'no_hp_orang_tua' => '081234567800',
            'jurusan_id' => $this->siswa->jurusan_id,
            'gelombang_id' => $this->siswa->gelombang_id,
            'tahun_ajaran_id' => $this->siswa->tahun_ajaran_id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('01012008'),
        ]);

        $jenisBerkas = JenisBerkas::first();
        $path = UploadedFile::fake()->create('rahasia.pdf', 100, 'application/pdf')->store('berkas_siswa/' . $siswaLain->id, 'local');

        $berkasLain = BerkasSiswa::create([
            'calon_siswa_id' => $siswaLain->id,
            'jenis_berkas_id' => $jenisBerkas->id,
            'file_path' => $path,
            'nama_file_asli' => 'rahasia.pdf',
            'mime_type' => 'application/pdf',
            'file_size_kb' => 100,
            'status' => 'menunggu',
        ]);

        // Siswa pertama mencoba melihat berkas siswa kedua -> harus 404 Not Found (diisolasi)
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.berkas.lihat', $berkasLain->id));

        $response->assertStatus(404);
    }
}
