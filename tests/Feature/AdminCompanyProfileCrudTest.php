<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\Fasilitas;
use App\Models\Galeri;
use App\Models\InformasiSekolah;
use App\Models\LogAktivitas;
use App\Models\Pengumuman;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCompanyProfileCrudTest extends TestCase
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
        Storage::fake('public');
    }

    public function test_super_admin_dapat_crud_pengumuman(): void
    {
        // 1. Create
        $responseStore = $this->actingAs($this->superAdmin)->post(route('admin.pengumuman.store'), [
            'judul' => 'Pengumuman Libur Awal Ramadhan 1448 H',
            'tanggal' => '2026-03-01',
            'status' => 'terbit',
            'isi' => 'Seluruh kegiatan KBM ditiadakan selama awal bulan suci Ramadhan.',
            'file_lampiran' => UploadedFile::fake()->create('surat-edaran.pdf', 200, 'application/pdf'),
        ]);

        $responseStore->assertRedirect();
        $responseStore->assertSessionHas('sukses');

        $this->assertDatabaseHas('pengumumans', [
            'judul' => 'Pengumuman Libur Awal Ramadhan 1448 H',
            'status' => 'terbit',
        ]);

        $pengumuman = Pengumuman::where('judul', 'Pengumuman Libur Awal Ramadhan 1448 H')->first();
        $this->assertNotNull($pengumuman->file_lampiran);
        Storage::disk('public')->assertExists($pengumuman->file_lampiran);

        // 2. Update
        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('admin.pengumuman.update', $pengumuman->id), [
            'judul' => 'Revisi Pengumuman Libur Ramadhan',
            'tanggal' => '2026-03-02',
            'status' => 'draf',
            'isi' => 'Perubahan tanggal libur sesuai ketetapan pimpinan pusat.',
        ]);

        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('pengumumans', [
            'id' => $pengumuman->id,
            'judul' => 'Revisi Pengumuman Libur Ramadhan',
            'status' => 'draf',
        ]);

        // 3. Delete
        $responseDelete = $this->actingAs($this->superAdmin)->delete(route('admin.pengumuman.destroy', $pengumuman->id));
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('pengumumans', ['id' => $pengumuman->id]);
    }

    public function test_super_admin_dapat_crud_fasilitas(): void
    {
        // 1. Create
        $responseStore = $this->actingAs($this->superAdmin)->post(route('admin.fasilitas.store'), [
            'nama' => 'Laboratorium Smart Simulator Ototronik',
            'kategori' => 'Bengkel & Laboratorium',
            'urutan' => 99,
            'deskripsi' => 'Dilengkapi simulator kelistrikan bodi mobil dan trainer EMS modern.',
            'foto' => UploadedFile::fake()->image('lab-ototronik.jpg', 800, 600),
        ]);

        $responseStore->assertRedirect();
        $this->assertDatabaseHas('fasilitas', [
            'nama' => 'Laboratorium Smart Simulator Ototronik',
            'urutan' => 99,
        ]);

        $item = Fasilitas::where('nama', 'Laboratorium Smart Simulator Ototronik')->first();
        $this->assertNotNull($item->foto);
        Storage::disk('public')->assertExists($item->foto);

        // 2. Update
        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('admin.fasilitas.update', $item->id), [
            'nama' => 'Lab Smart Simulator Ototronik Revisi',
            'kategori' => 'Bengkel & Laboratorium',
            'urutan' => 10,
            'deskripsi' => 'Update deskripsi fasilitas sarana.',
        ]);

        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('fasilitas', [
            'id' => $item->id,
            'nama' => 'Lab Smart Simulator Ototronik Revisi',
            'urutan' => 10,
        ]);

        // 3. Delete
        $responseDelete = $this->actingAs($this->superAdmin)->delete(route('admin.fasilitas.destroy', $item->id));
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('fasilitas', ['id' => $item->id]);
    }

    public function test_super_admin_dapat_crud_ekstrakurikuler(): void
    {
        // 1. Create
        $responseStore = $this->actingAs($this->superAdmin)->post(route('admin.ekstrakurikuler.store'), [
            'nama' => 'Klub Robotika & IoT Industri',
            'kategori' => 'Kejuruan & IT',
            'pembina' => 'Bpk. Hendra, S.T.',
            'jadwal' => 'Rabu, 15.00 - 17.00 WIB',
            'deskripsi' => 'Riset perakitan mikrokontroler Arduino dan mikroskopis komponen elektronika.',
            'foto' => UploadedFile::fake()->image('robotik.jpg'),
        ]);

        $responseStore->assertRedirect();
        $this->assertDatabaseHas('ekstrakurikulers', [
            'nama' => 'Klub Robotika & IoT Industri',
            'pembina' => 'Bpk. Hendra, S.T.',
        ]);

        $ekskul = Ekstrakurikuler::where('nama', 'Klub Robotika & IoT Industri')->first();

        // 2. Update
        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('admin.ekstrakurikuler.update', $ekskul->id), [
            'nama' => 'Klub Robotika & AI Vokasi',
            'kategori' => 'Kejuruan & IT',
            'pembina' => 'Bpk. Hendra Wijaya, S.T.',
            'jadwal' => 'Kamis, 15.00 - 17.00 WIB',
            'deskripsi' => 'Deskripsi kegiatan robotik terbaru.',
        ]);

        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('ekstrakurikulers', [
            'id' => $ekskul->id,
            'nama' => 'Klub Robotika & AI Vokasi',
        ]);

        // 3. Delete
        $responseDelete = $this->actingAs($this->superAdmin)->delete(route('admin.ekstrakurikuler.destroy', $ekskul->id));
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('ekstrakurikulers', ['id' => $ekskul->id]);
    }

    public function test_super_admin_dapat_crud_galeri(): void
    {
        // 1. Create
        $responseStore = $this->actingAs($this->superAdmin)->post(route('admin.galeri.store'), [
            'judul' => 'Uji Kompetensi Keahlian Mandiri 2026',
            'kategori' => 'Praktik Bengkel',
            'tanggal' => '2026-04-10',
            'keterangan' => 'Siswa menyelesaikan job sheet transmisi manual dan otomatis.',
            'foto' => UploadedFile::fake()->image('ukk-2026.jpg'),
        ]);

        $responseStore->assertRedirect();
        $this->assertDatabaseHas('galeris', [
            'judul' => 'Uji Kompetensi Keahlian Mandiri 2026',
            'kategori' => 'Praktik Bengkel',
        ]);

        $galeri = Galeri::where('judul', 'Uji Kompetensi Keahlian Mandiri 2026')->first();
        Storage::disk('public')->assertExists($galeri->foto);

        // 2. Update
        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('admin.galeri.update', $galeri->id), [
            'judul' => 'Dokumentasi UKK Nasional 2026',
            'kategori' => 'Praktik Bengkel',
            'tanggal' => '2026-04-12',
            'keterangan' => 'Keterangan revisi dokumentasi.',
        ]);

        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('galeris', [
            'id' => $galeri->id,
            'judul' => 'Dokumentasi UKK Nasional 2026',
        ]);

        // 3. Delete
        $responseDelete = $this->actingAs($this->superAdmin)->delete(route('admin.galeri.destroy', $galeri->id));
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('galeris', ['id' => $galeri->id]);
    }

    public function test_super_admin_dapat_mengubah_informasi_sekolah_visi_misi_dan_sambutan(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('admin.informasi-sekolah.update'), [
            'visi' => 'Menjadi SMK Muhammadiyah rujukan teknologi terdepan dan akhlak mulia.',
            'misi' => '<ol><li>Membangun karakter islami.</li><li>Menguasai teknologi industri.</li></ol>',
            'tujuan' => '<ul><li>Lulusan langsung bekerja dan siap berwirausaha.</li></ul>',
            'nama_kepala_sekolah' => 'Mohammad Ardianshah, S.Kom, M.T.',
            'sambutan' => 'Pesan kepala sekolah terbaru untuk segenap civitas akademika dan calon siswa baru.',
            'foto_kepala_sekolah' => UploadedFile::fake()->image('kepsek-baru.jpg'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('informasi_sekolahs', [
            'key' => 'visi',
            'konten' => 'Menjadi SMK Muhammadiyah rujukan teknologi terdepan dan akhlak mulia.',
        ]);

        $sambutan = InformasiSekolah::getByKey('sambutan_kepala_sekolah');
        $this->assertEquals('Mohammad Ardianshah, S.Kom, M.T.', $sambutan->extra_data['nama_kepala_sekolah']);
        $this->assertStringContainsString('Pesan kepala sekolah terbaru', $sambutan->konten);

        $this->assertDatabaseHas('log_aktivitas', [
            'jenis_aksi' => 'ubah_informasi_sekolah',
        ]);
    }

    public function test_admin_piket_dilarang_mengakses_crud_konten_company_profile(): void
    {
        $routes = [
            'admin.pengumuman.index',
            'admin.fasilitas.index',
            'admin.ekstrakurikuler.index',
            'admin.galeri.index',
            'admin.informasi-sekolah.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->adminPiket)->get(route($route));
            $response->assertStatus(403);
        }
    }
}

