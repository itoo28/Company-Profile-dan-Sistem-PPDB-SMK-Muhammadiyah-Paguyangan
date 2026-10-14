<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\Prestasi;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_halaman_beranda_dapat_diakses_dan_memuat_identitas_sekolah(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SMK Muhammadiyah');
        $response->assertSee('Paguyangan');
        $response->assertSee('Daftar Siswa Baru');
        $response->assertSee('Teknik Kendaraan Ringan');
        $response->assertSee('Teknik Sepeda Motor');
        $response->assertSee('Teknik Ototronik');
        $response->assertSee('Rekayasa Perangkat Lunak');
        $response->assertSee('Produksi Film (Broadcasting)');
    }

    public function test_halaman_visi_misi_dapat_diakses(): void
    {
        $response = $this->get('/visi-misi');

        $response->assertStatus(200);
        $response->assertSee('Visi SMK Muhammadiyah Paguyangan');
        $response->assertSee('Misi Sekolah');
    }

    public function test_halaman_fasilitas_dapat_diakses(): void
    {
        $response = $this->get('/fasilitas');

        $response->assertStatus(200);
        $response->assertSee('Fasilitas Sekolah');
        $response->assertSee('Gedung Pembelajaran Baru');
    }

    public function test_halaman_ekstrakurikuler_dapat_diakses(): void
    {
        $response = $this->get('/ekstrakurikuler');

        $response->assertStatus(200);
        $response->assertSee('Ekstrakurikuler');
        $response->assertSee('Hizbul Wathan');
        $response->assertSee('Tapak Suci');
    }

    public function test_halaman_daftar_berita_dapat_diakses(): void
    {
        $response = $this->get('/berita');

        $response->assertStatus(200);
        $response->assertSee('Kabar & Berita Terkini', false);
    }

    public function test_halaman_detail_berita_terbit_dapat_diakses(): void
    {
        $berita = Berita::terbit()->first();

        $response = $this->get('/berita/' . $berita->slug);

        $response->assertStatus(200);
        $response->assertSee($berita->judul);
    }

    public function test_berita_berstatus_draf_tidak_dapat_diakses_publik(): void
    {
        $draf = Berita::create([
            'judul' => 'Berita Rahasia Draf Sekolah',
            'slug' => 'berita-rahasia-draf-sekolah',
            'isi' => 'Konten internal belum boleh dipublikasikan.',
            'tanggal_terbit' => now(),
            'status' => 'draf',
        ]);

        $response = $this->get('/berita/' . $draf->slug);

        $response->assertStatus(404);
    }

    public function test_halaman_pengumuman_dapat_diakses(): void
    {
        $response = $this->get('/pengumuman');

        $response->assertStatus(200);
        $response->assertSee('Pengumuman');
    }

    public function test_halaman_prestasi_dapat_diakses(): void
    {
        $response = $this->get('/prestasi');

        $response->assertStatus(200);
        $response->assertSee('Prestasi & Penghargaan Siswa', false);
    }

    public function test_halaman_detail_prestasi_dapat_diakses(): void
    {
        $prestasi = Prestasi::terbit()->first();

        $response = $this->get('/prestasi/' . $prestasi->slug);

        $response->assertStatus(200);
        $response->assertSee($prestasi->nama_prestasi);
    }

    public function test_foto_prestasi_dan_berita_dari_storage_dirender_dengan_benar_di_halaman_publik(): void
    {
        $prestasi = Prestasi::terbit()->first();
        $prestasi->update(['foto' => 'prestasi/ujicoba-upload.jpg']);

        $responsePrestasi = $this->get('/prestasi');
        $responsePrestasi->assertStatus(200);
        $responsePrestasi->assertSee('storage/prestasi/ujicoba-upload.jpg');

        $responseDetail = $this->get('/prestasi/' . $prestasi->slug);
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('storage/prestasi/ujicoba-upload.jpg');

        $berita = Berita::terbit()->first();
        $berita->update(['gambar_sampul' => 'berita/sampul-upload.jpg']);

        $responseBerita = $this->get('/berita');
        $responseBerita->assertStatus(200);
        $responseBerita->assertSee('storage/berita/sampul-upload.jpg');

        $responseBeritaDetail = $this->get('/berita/' . $berita->slug);
        $responseBeritaDetail->assertStatus(200);
        $responseBeritaDetail->assertSee('storage/berita/sampul-upload.jpg');
    }

    public function test_halaman_galeri_dapat_diakses(): void
    {
        $response = $this->get('/galeri');

        $response->assertStatus(200);
        $response->assertSee('Galeri Aktivitas Sekolah');
    }

    public function test_halaman_info_ppdb_memuat_persyaratan_dan_alur(): void
    {
        $response = $this->get('/ppdb');

        $response->assertStatus(200);
        $response->assertSee('Alur & Persyaratan PPDB', false);
        $response->assertSee('Kartu Keluarga (KK)');
        $response->assertSee('Akta Kelahiran');
        $response->assertSee('Ijazah / Surat Keterangan Lulus (SKL)');
    }

    public function test_halaman_kontak_dapat_diakses(): void
    {
        $response = $this->get('/kontak');

        $response->assertStatus(200);
        $response->assertSee('Kontak & Lokasi Sekolah', false);
        $response->assertSee('Paguyangan');
        $response->assertSee('P24P+32J');
        $response->assertSee('-7.2948315297146245');
    }

    public function test_formulir_kontak_dapat_mengirim_pesan_dengan_validasi(): void
    {
        $response = $this->post('/kontak', [
            'nama' => 'Ahmad Fauzi',
            'kontak' => '081234567890',
            'subjek' => 'Informasi Biaya PPDB',
            'pesan' => 'Mohon info lengkap mengenai pendaftaran dan seragam sekolah.',
        ]);

        $response->assertRedirect('/kontak');
        $response->assertSessionHas('sukses');
    }
}
