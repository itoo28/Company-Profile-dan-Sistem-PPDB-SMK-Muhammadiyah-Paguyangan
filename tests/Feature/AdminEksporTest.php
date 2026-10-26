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

class AdminEksporTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected CalonSiswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->superAdmin = User::where('role', 'super_admin')->first();

        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $this->siswa = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0099',
            'nomor_urut' => 99,
            'nama_lengkap' => 'Muhammad Rizqi',
            'nik' => '3329012304080099',
            'nisn' => '0081234599',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => "Jl. Raya Paguyangan No. 10\nRT 01 / RW 02",
            'asal_sekolah' => 'SMP Muhammadiyah Paguyangan',
            'nama_orang_tua' => 'Bapak Rizqi',
            'no_hp_orang_tua' => '081234567899',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'status_benefit' => 'klaim_aktif',
            'password' => Hash::make('15052008'),
        ]);
    }

    public function test_halaman_ekspor_dapat_diakses_oleh_super_admin(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ekspor.index'));

        $response->assertStatus(200);
        $response->assertSee('Ekspor Lembar Kerja Spreadsheet');
        $response->assertSee('Unduh Berkas Excel (.xls)');
        $response->assertSee('Cetak Rekapitulasi Resmi');
    }

    public function test_ekspor_csv_berhasil_dengan_pemisah_titik_koma_default_dan_rapi(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ekspor.csv'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
        $this->assertStringContainsString('attachment; filename="data-pendaftar-ppdb-smk-muhammadiyah-paguyangan-', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();

        // Verifikasi UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        // Verifikasi Header Kolom terpisah dengan titik koma (;)
        $this->assertStringContainsString('"No Urut";"Nomor Pendaftaran";"Nama Lengkap";NIK;NISN', $content);

        // Verifikasi data siswa dan formula teks Excel untuk NIK & NISN (mencegah scientific notation)
        $this->assertStringContainsString('Muhammad Rizqi', $content);
        $this->assertStringContainsString('3329012304080099', $content);
        $this->assertStringContainsString('0081234599', $content);
        $this->assertStringContainsString('081234567899', $content);

        // Verifikasi label manusiawi
        $this->assertStringContainsString('Klaim Aktif (Gratis SPP 3 Bulan)', $content);
        $this->assertStringContainsString('Jalur Umum / Reguler', $content);
        $this->assertStringContainsString('Online (Mandiri)', $content);

        // Verifikasi alamat multiline dibersihkan menjadi 1 baris
        $this->assertStringContainsString('Jl. Raya Paguyangan No. 10 RT 01 / RW 02', $content);
    }

    public function test_ekspor_csv_dengan_pemisah_koma_dan_filter_jalur(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ekspor.csv', [
                'delimiter' => 'comma',
                'jalur_pendaftaran' => 'umum',
                'format_angka' => 'raw',
            ]));

        $response->assertStatus(200);
        $content = $response->streamedContent();

        // Pemisah adalah koma
        $this->assertStringContainsString('"No Urut","Nomor Pendaftaran","Nama Lengkap",NIK,NISN', $content);
        $this->assertStringContainsString('Muhammad Rizqi', $content);
        $this->assertStringContainsString('3329012304080099', $content);
    }

    public function test_ekspor_excel_berhasil(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ekspor.excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->assertStringContainsString('.xls"', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('<table border="1">', $content);
        $this->assertStringContainsString('Muhammad Rizqi', $content);
        $this->assertStringContainsString('3329012304080099', $content);
    }

    public function test_ekspor_cetak_a4_berhasil(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ekspor.cetak'));

        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI DATA PENDAFTARAN PESERTA DIDIK BARU');
        $response->assertSee('Muhammad Rizqi');
    }

    public function test_ekspor_dengan_filter_tahun_ajaran(): void
    {
        $tahunLain = TahunAjaran::create(['nama' => '2025/2026', 'is_aktif' => false]);

        // Akses index dengan filter tahun lain
        $resIndex = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ekspor.index', ['tahun_ajaran_id' => $tahunLain->id]));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Arsip Tahun Ajaran 2025/2026');

        // Ekspor Excel untuk tahun lain tidak boleh memuat siswa tahun aktif
        $resExcel = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ekspor.excel', ['tahun_ajaran_id' => $tahunLain->id]));
        $resExcel->assertStatus(200);
        $content = $resExcel->streamedContent();
        $this->assertStringNotContainsString('Muhammad Rizqi', $content);
        $this->assertStringContainsString('2025/2026 (Arsip)', $content);
    }

    public function test_ekspor_membutuhkan_autentikasi_admin(): void
    {
        $response = $this->get(route('admin.ekspor.csv'));
        $response->assertRedirect(route('admin.login'));
    }
}
