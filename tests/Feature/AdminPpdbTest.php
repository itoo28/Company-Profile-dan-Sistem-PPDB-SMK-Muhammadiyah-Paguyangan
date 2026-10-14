<?php

namespace Tests\Feature;

use App\Models\BerkasSiswa;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\JenisBerkas;
use App\Models\Jurusan;
use App\Models\PengambilanSeragam;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPpdbTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminPiket;
    protected CalonSiswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->superAdmin = User::where('role', 'super_admin')->first();
        $this->adminPiket = User::where('role', 'admin_piket')->first();

        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $this->siswa = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0020',
            'nomor_urut' => 20,
            'nama_lengkap' => 'Dimas Aditya',
            'nik' => '3329012304080020',
            'nisn' => '0081234520',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-11-10',
            'jenis_kelamin' => 'L',
            'alamat' => 'Desa Pagojengan',
            'asal_sekolah' => 'SMP Muhammadiyah Paguyangan',
            'nama_orang_tua' => 'Teguh',
            'no_hp_orang_tua' => '081234567888',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'status_benefit' => 'kandidat_100_pertama',
            'password' => Hash::make('10112008'),
        ]);
    }

    public function test_halaman_login_admin_dapat_diakses(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Panel Administrasi');
    }

    public function test_staf_dapat_login_ke_portal_admin(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'superadmin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals($this->superAdmin->id, Auth::guard('web')->id());
    }

    public function test_admin_dashboard_memuat_ringkasan_operasional(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Operasional PPDB');
        $response->assertSee('Total Calon Siswa');
        $response->assertSee('Promo Bebas SPP 3 Bulan');

        // Pastikan nama pendaftar muncul di daftar pendaftar
        $resPendaftar = $this->actingAs($this->superAdmin, 'web')->get(route('admin.pendaftar.index'));
        $resPendaftar->assertStatus(200);
        $resPendaftar->assertSee('Dimas Aditya');
    }

    public function test_admin_piket_dapat_melakukan_pendaftaran_offline_di_loket(): void
    {
        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();

        $payload = [
            'nama_lengkap' => 'Citra Lestari',
            'nik' => '3329012304080033',
            'nisn' => '0081234533',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-04-12',
            'jenis_kelamin' => 'P',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1 Paguyangan',
            'nama_orang_tua' => 'Haryanto',
            'no_hp_orang_tua' => '081234567111',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'jalur_pendaftaran' => 'umum',
            'persetujuan_data' => '1',
        ];

        $response = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pendaftar.baru.simpan'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('calon_siswas', [
            'nik' => '3329012304080033',
            'nama_lengkap' => 'Citra Lestari',
            'jalur_registrasi' => 'offline',
        ]);
    }

    public function test_admin_dapat_menyetujui_berkas_dan_alasan_wajib_diisi_jika_menolak(): void
    {
        $jenisBerkas = JenisBerkas::where('aturan_wajib', 'semua')->first();

        $berkas = BerkasSiswa::create([
            'calon_siswa_id' => $this->siswa->id,
            'jenis_berkas_id' => $jenisBerkas->id,
            'file_path' => 'berkas_siswa/test.pdf',
            'nama_file_asli' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'file_size_kb' => 50,
            'status' => 'menunggu',
        ]);

        // Verifikasi Approve
        $responseApprove = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pendaftar.berkas.verifikasi', $berkas->id), [
                'status' => 'disetujui',
            ]);

        $responseApprove->assertSessionHas('sukses');
        $this->assertDatabaseHas('berkas_siswas', [
            'id' => $berkas->id,
            'status' => 'disetujui',
        ]);

        // Verifikasi Reject tanpa catatan penolakan -> Harus gagal validasi (BR-04)
        $responseRejectFail = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pendaftar.berkas.verifikasi', $berkas->id), [
                'status' => 'ditolak',
                'catatan_penolakan' => '', // Kosong
            ]);

        $responseRejectFail->assertSessionHasErrors(['catatan_penolakan']);

        // Verifikasi Reject dengan catatan valid
        $responseRejectSuccess = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pendaftar.berkas.verifikasi', $berkas->id), [
                'status' => 'ditolak',
                'catatan_penolakan' => 'Dokumen terpotong dan tidak terbaca jelas.',
            ]);

        $responseRejectSuccess->assertSessionHas('sukses');
        $this->assertDatabaseHas('berkas_siswas', [
            'id' => $berkas->id,
            'status' => 'ditolak',
            'catatan_penolakan' => 'Dokumen terpotong dan tidak terbaca jelas.',
        ]);
    }

    public function test_pengambilan_seragam_hanya_dapat_dilakukan_setelah_clearance_berkas_disetujui(): void
    {
        // 1. Siswa belum melengkapi seluruh berkas wajib -> Coba catat seragam -> Harus ditolak
        $responseTolak = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
                'catatan' => 'Pengambilan awal',
            ]);

        $responseTolak->assertSessionHas('error');
        $this->assertDatabaseMissing('pengambilan_seragams', ['calon_siswa_id' => $this->siswa->id]);

        // 2. Sekarang lengkapi dan approve seluruh berkas wajib untuk jalur umum
        $berkasWajibList = JenisBerkas::where('aturan_wajib', 'semua')->get();
        foreach ($berkasWajibList as $jenis) {
            BerkasSiswa::create([
                'calon_siswa_id' => $this->siswa->id,
                'jenis_berkas_id' => $jenis->id,
                'file_path' => "berkas_siswa/{$jenis->id}.pdf",
                'nama_file_asli' => 'doc.pdf',
                'mime_type' => 'application/pdf',
                'file_size_kb' => 50,
                'status' => 'disetujui',
            ]);
        }

        $this->siswa->refresh();
        $this->assertTrue($this->siswa->isSeragamClearanceApproved());

        // 3. Catat pengambilan seragam -> Sukses
        $responseSukses = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
                'catatan' => 'Paket lengkap seragam kejuruan diserahkan',
            ]);

        $responseSukses->assertSessionHas('sukses');
        $this->assertDatabaseHas('pengambilan_seragams', [
            'calon_siswa_id' => $this->siswa->id,
        ]);

        // 4. Coba catat seragam untuk kedua kalinya -> Harus ditolak (BR-06)
        $responseDuplikat = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
            ]);

        $responseDuplikat->assertSessionHas('error');
    }

    public function test_admin_piket_dilarang_mengakses_halaman_konfigurasi_super_admin(): void
    {
        // Admin piket mencoba akses manajemen staf
        $responseStaf = $this->actingAs($this->adminPiket, 'web')->get(route('admin.staf.index'));
        $responseStaf->assertStatus(403);

        // Admin piket mencoba akses pengaturan sistem
        $responsePengaturan = $this->actingAs($this->adminPiket, 'web')->get(route('admin.pengaturan.index'));
        $responsePengaturan->assertStatus(403);

        // Super Admin dapat mengakses keduanya
        $responseSuperStaf = $this->actingAs($this->superAdmin, 'web')->get(route('admin.staf.index'));
        $responseSuperStaf->assertStatus(200);

        $responseSuperPengaturan = $this->actingAs($this->superAdmin, 'web')->get(route('admin.pengaturan.index'));
        $responseSuperPengaturan->assertStatus(200);
    }

    public function test_superadmin_dan_admin_piket_dapat_membuat_berita_baru(): void
    {
        $payload = [
            'judul' => 'Pengumuman Hasil Seleksi Administrasi PPDB 2026',
            'ringkasan' => 'Daftar nama calon siswa yang lolos verifikasi tahap pertama.',
            'isi' => '<p>Selamat kepada seluruh peserta didik yang telah melengkapi berkas tepat waktu.</p>',
            'tanggal_terbit' => '2026-10-14',
            'status' => 'terbit',
        ];

        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.berita.store'), $payload);

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('beritas', [
            'judul' => 'Pengumuman Hasil Seleksi Administrasi PPDB 2026',
            'dibuat_oleh' => $this->superAdmin->id,
            'status' => 'terbit',
        ]);

        // Verifikasi bahwa halaman daftar berita dapat dimuat tanpa error RelationNotFoundException
        $indexResponse = $this->actingAs($this->superAdmin, 'web')->get(route('admin.berita.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Pengumuman Hasil Seleksi Administrasi PPDB 2026');
    }

    public function test_superadmin_dan_admin_piket_dapat_membuat_prestasi_baru(): void
    {
        $payload = [
            'nama_prestasi' => 'Juara 1 Lomba Web Design Tingkat Provinsi',
            'nama_peraih' => 'Zidan & Fikri',
            'tingkat' => 'Provinsi',
            'peringkat' => 'Juara 1',
            'tanggal_perolehan' => '2026-09-20',
            'deskripsi' => 'Meraih medali emas dalam kompetisi desain web kejuruan se-Jawa Tengah.',
            'status' => 'terbit',
        ];

        $response = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.prestasi.store'), $payload);

        $response->assertRedirect(route('admin.prestasi.index'));
        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('prestasis', [
            'nama_prestasi' => 'Juara 1 Lomba Web Design Tingkat Provinsi',
            'dibuat_oleh' => $this->adminPiket->id,
        ]);

        $indexResponse = $this->actingAs($this->adminPiket, 'web')->get(route('admin.prestasi.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Juara 1 Lomba Web Design Tingkat Provinsi');
    }

    public function test_superadmin_dapat_mengubah_dan_menghapus_gelombang(): void
    {
        $gelombang = Gelombang::create([
            'tahun_ajaran_id' => $this->siswa->tahun_ajaran_id,
            'nama_gelombang' => 'Gelombang Uji Coba',
            'kode_gelombang' => 'G-TEST',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-31',
            'keterangan' => 'Keterangan awal',
            'is_bidikmisi' => false,
            'is_aktif' => true,
        ]);

        // Uji Update (Edit)
        $updateResponse = $this->actingAs($this->superAdmin, 'web')
            ->put(route('admin.gelombang.update', $gelombang->id), [
                'nama_gelombang' => 'Gelombang Uji Coba Direvisi',
                'kode_gelombang' => 'G-REV',
                'tanggal_mulai' => '2026-08-05',
                'tanggal_selesai' => '2026-09-05',
                'keterangan' => 'Keterangan baru revisi',
                'is_bidikmisi' => true,
                'is_aktif' => true,
            ]);

        $updateResponse->assertSessionHas('sukses');
        $this->assertDatabaseHas('gelombangs', [
            'id' => $gelombang->id,
            'nama_gelombang' => 'Gelombang Uji Coba Direvisi',
            'kode_gelombang' => 'G-REV',
            'is_bidikmisi' => true,
        ]);

        // Uji Delete (Hapus)
        $deleteResponse = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.gelombang.destroy', $gelombang->id));

        $deleteResponse->assertSessionHas('sukses');
        $this->assertDatabaseMissing('gelombangs', ['id' => $gelombang->id]);
    }

    public function test_superadmin_dapat_mengubah_dan_menghapus_jurusan(): void
    {
        $jurusan = Jurusan::create([
            'kode' => 'ANIM',
            'nama_jurusan' => 'Animasi dan Media Kreatif',
            'bidang_keahlian' => 'Seni dan Ekonomi Kreatif',
            'deskripsi' => 'Jurusan animasi 2D dan 3D',
            'prospek_kerja' => 'Animator, Designer',
            'kuota' => 36,
            'urutan' => 99,
            'is_aktif' => true,
        ]);

        // Uji Update (Edit)
        $updateResponse = $this->actingAs($this->superAdmin, 'web')
            ->put(route('admin.jurusan.update', $jurusan->id), [
                'kode' => 'ANIM',
                'nama_jurusan' => 'Animasi Digital 3D',
                'bidang_keahlian' => 'Seni dan Ekonomi Kreatif',
                'deskripsi' => 'Jurusan animasi 3D profesional',
                'prospek_kerja' => '3D Artist',
                'kuota' => 40,
                'urutan' => 10,
                'is_aktif' => true,
            ]);

        $updateResponse->assertSessionHas('sukses');
        $this->assertDatabaseHas('jurusans', [
            'id' => $jurusan->id,
            'nama_jurusan' => 'Animasi Digital 3D',
            'kuota' => 40,
        ]);

        // Uji Delete (Hapus)
        $deleteResponse = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.jurusan.destroy', $jurusan->id));

        $deleteResponse->assertSessionHas('sukses');
        $this->assertDatabaseMissing('jurusans', ['id' => $jurusan->id]);
    }

    public function test_superadmin_dapat_mengubah_dan_menghapus_jenis_berkas(): void
    {
        $berkas = JenisBerkas::create([
            'nama' => 'Surat Bebas Narkoba',
            'aturan_wajib' => 'opsional',
            'is_wajib' => false,
            'deskripsi' => 'Dari klinik atau rumah sakit',
            'urutan' => 88,
            'is_aktif' => true,
        ]);

        // Uji Update (Edit)
        $updateResponse = $this->actingAs($this->superAdmin, 'web')
            ->put(route('admin.jenis-berkas.update', $berkas->id), [
                'nama' => 'Surat Keterangan Sehat & Bebas Narkoba',
                'aturan_wajib' => 'semua',
                'deskripsi' => 'Dokumen resmi dari Puskesmas atau RS',
                'urutan' => 88,
                'is_aktif' => true,
            ]);

        $updateResponse->assertSessionHas('sukses');
        $this->assertDatabaseHas('jenis_berkas', [
            'id' => $berkas->id,
            'nama' => 'Surat Keterangan Sehat & Bebas Narkoba',
            'aturan_wajib' => 'semua',
            'is_wajib' => true,
        ]);

        // Uji Delete (Hapus)
        $deleteResponse = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.jenis-berkas.destroy', $berkas->id));

        $deleteResponse->assertSessionHas('sukses');
        $this->assertDatabaseMissing('jenis_berkas', ['id' => $berkas->id]);
    }

    public function test_superadmin_dapat_mengubah_dan_menghapus_akun_staf(): void
    {
        $staf = User::create([
            'name' => 'Staf Baru Piket',
            'username' => 'stafpiket_baru',
            'email' => 'stafbaru@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_piket',
            'is_aktif' => true,
        ]);

        // Uji Update Profil Staf
        $updateResponse = $this->actingAs($this->superAdmin, 'web')
            ->put(route('admin.staf.update', $staf->id), [
                'name' => 'Staf Baru Direvisi',
                'username' => 'stafpiket_rev',
                'email' => 'stafrev@example.com',
                'role' => 'admin_piket',
                'is_aktif' => true,
            ]);

        $updateResponse->assertSessionHas('sukses');
        $this->assertDatabaseHas('users', [
            'id' => $staf->id,
            'name' => 'Staf Baru Direvisi',
            'username' => 'stafpiket_rev',
        ]);

        // Uji Delete Staf
        $deleteResponse = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.staf.destroy', $staf->id));

        $deleteResponse->assertSessionHas('sukses');
        $this->assertDatabaseMissing('users', ['id' => $staf->id]);
    }

    public function test_superadmin_dapat_menghapus_pendaftar_beserta_berkas(): void
    {
        $idSiswa = $this->siswa->id;

        $deleteResponse = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.pendaftar.destroy', $idSiswa));

        $deleteResponse->assertRedirect(route('admin.pendaftar.index'));
        $deleteResponse->assertSessionHas('sukses');

        $this->assertDatabaseMissing('calon_siswas', ['id' => $idSiswa]);
    }

    public function test_superadmin_dapat_membatalkan_pencatatan_seragam(): void
    {
        // Approve semua berkas wajib agar clearance terpenuhi
        $jenisBerkasList = JenisBerkas::where('is_wajib', true)->get();
        foreach ($jenisBerkasList as $jb) {
            BerkasSiswa::create([
                'calon_siswa_id' => $this->siswa->id,
                'jenis_berkas_id' => $jb->id,
                'nama_file_asli' => 'dokumen.pdf',
                'file_path' => 'berkas/dummy.pdf',
                'file_size_kb' => 100,
                'file_mime' => 'application/pdf',
                'status' => 'disetujui',
                'diverifikasi_oleh' => $this->superAdmin->id,
            ]);
        }

        // Catat pengambilan seragam
        $ambil = PengambilanSeragam::create([
            'calon_siswa_id' => $this->siswa->id,
            'petugas_id' => $this->superAdmin->id,
            'waktu_ambil' => now(),
            'catatan' => 'Diambil lengkap',
        ]);

        $this->assertDatabaseHas('pengambilan_seragams', ['id' => $ambil->id]);

        // Super Admin batalkan pengambilan seragam
        $batalResponse = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.seragam.destroy', $ambil->id));

        $batalResponse->assertSessionHas('sukses');
        $this->assertDatabaseMissing('pengambilan_seragams', ['id' => $ambil->id]);
    }
}
