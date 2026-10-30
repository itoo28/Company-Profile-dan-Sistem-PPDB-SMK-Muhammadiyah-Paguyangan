<?php

namespace Tests\Feature;

use App\Models\BerkasSiswa;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\JenisBerkas;
use App\Models\Jurusan;
use App\Models\Pembayaran;
use App\Models\PengambilanSeragam;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $gelombang?->update([
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
        ]);
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
        // 1. Siswa sudah bayar lunas tapi belum melengkapi seluruh berkas wajib -> Coba catat seragam -> Harus ditolak
        $pembayaran = $this->siswa->getOrCreatePembayaran();
        $pembayaran->update([
            'status' => 'lunas',
            'metode_pembayaran' => 'tunai',
            'tanggal_bayar' => now(),
        ]);
        $this->siswa->refresh();
        $this->assertTrue($this->siswa->isPembayaranLunas());
        $this->assertFalse($this->siswa->isBerkasWajibDisetujui());
        $this->assertFalse($this->siswa->isSeragamClearanceApproved());

        $responseTolak = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
                'catatan' => 'Pengambilan awal tanpa berkas',
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

        // 3. Berkas sudah lengkap tapi pembayaran belum lunas -> Clearance harus false & ditolak
        $pembayaran->update(['status' => 'menunggu_pembayaran']);
        $this->siswa->refresh();
        $this->assertTrue($this->siswa->isBerkasWajibDisetujui());
        $this->assertFalse($this->siswa->isPembayaranLunas());
        $this->assertFalse($this->siswa->isSeragamClearanceApproved());

        $responseBelumBayar = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
                'catatan' => 'Coba ambil sebelum lunas',
            ]);

        $responseBelumBayar->assertSessionHas('error');
        $this->assertDatabaseMissing('pengambilan_seragams', ['calon_siswa_id' => $this->siswa->id]);

        // 4. Set pembayaran lunas kembali -> Keduanya terpenuhi
        $pembayaran->update([
            'status' => 'lunas',
            'metode_pembayaran' => 'tunai',
            'tanggal_bayar' => now(),
        ]);

        $this->siswa->refresh();
        $this->assertTrue($this->siswa->isPembayaranLunas());
        $this->assertTrue($this->siswa->isBerkasWajibDisetujui());
        $this->assertTrue($this->siswa->isSeragamClearanceApproved());

        // 5. Catat pengambilan seragam -> Sukses
        $responseSukses = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
                'catatan' => 'Paket lengkap seragam kejuruan diserahkan',
            ]);

        $responseSukses->assertSessionHas('sukses');
        $this->assertDatabaseHas('pengambilan_seragams', [
            'calon_siswa_id' => $this->siswa->id,
        ]);

        // Alokasi benefit promo otomatis terbit menjadi klaim_aktif saat seragam diambil
        $this->siswa->refresh();
        $this->assertEquals('klaim_aktif', $this->siswa->status_benefit);

        // 6. Coba catat seragam untuk kedua kalinya -> Harus ditolak (BR-06)
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

    public function test_superadmin_dapat_membuat_gelombang_dengan_tahun_ajaran_dan_memfilter(): void
    {
        $tahunLain = \App\Models\TahunAjaran::create([
            'nama' => '2027/2028',
            'is_aktif' => false,
        ]);

        // Tambah gelombang untuk tahun ajaran baru
        $storeResponse = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.gelombang.store'), [
                'tahun_ajaran_id' => $tahunLain->id,
                'nama_gelombang' => 'Gelombang Khusus 2027',
                'kode_gelombang' => 'G27',
                'tanggal_mulai' => '2027-01-01',
                'tanggal_selesai' => '2027-02-28',
                'keterangan' => 'Uji coba tahun depan',
                'is_bidikmisi' => false,
            ]);

        $storeResponse->assertSessionHas('sukses');
        $this->assertDatabaseHas('gelombangs', [
            'tahun_ajaran_id' => $tahunLain->id,
            'nama_gelombang' => 'Gelombang Khusus 2027',
        ]);

        // Akses index dengan filter tahun ajaran lain
        $filterResponse = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.gelombang.index', ['tahun_ajaran_id' => $tahunLain->id]));

        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Gelombang Khusus 2027');
        $filterResponse->assertSee('2027/2028');
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
        // Berikan status benefit awal klaim_aktif
        $this->siswa->update(['status_benefit' => 'klaim_aktif']);

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

        // Status benefit harus kembali menjadi kandidat_100_pertama
        $this->siswa->refresh();
        $this->assertEquals('kandidat_100_pertama', $this->siswa->status_benefit);
    }

    public function test_alokasi_promo_100_pertama_berhenti_ketika_kuota_seragam_terpenuhi(): void
    {
        // Set kuota promo hanya 1 siswa untuk pengujian batas kuota
        $pengaturan = PengaturanSistem::where('id', 1)->first() ?? PengaturanSistem::instance();
        $pengaturan->update([
            'is_promo_aktif' => true,
            'kuota_promo_benefit' => 1,
        ]);

        // Siswa 1 lengkapi berkas & bayar lunas
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
        $pembayaran1 = $this->siswa->getOrCreatePembayaran();
        $pembayaran1->update(['status' => 'lunas']);

        // Siswa 1 ambil seragam -> mendapatkan kuota promo klaim_aktif
        $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
            ]);

        $this->siswa->refresh();
        $this->assertEquals('klaim_aktif', $this->siswa->status_benefit);

        // Siswa 2 dibuat sebagai kandidat awal
        $siswa2 = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/2026/0999',
            'tahun_ajaran_id' => $this->siswa->tahun_ajaran_id,
            'gelombang_id' => $this->siswa->gelombang_id,
            'jurusan_id' => $this->siswa->jurusan_id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'nomor_urut' => 999,
            'status_benefit' => 'kandidat_100_pertama',
            'nama_lengkap' => 'Siswa Kedua',
            'nik' => '3329012304089998',
            'nisn' => '0081239998',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 2',
            'nama_orang_tua' => 'Ortu 2',
            'no_hp_orang_tua' => '081234567888',
            'password' => Hash::make('01012008'),
        ]);

        foreach ($berkasWajibList as $jenis) {
            BerkasSiswa::create([
                'calon_siswa_id' => $siswa2->id,
                'jenis_berkas_id' => $jenis->id,
                'file_path' => "berkas_siswa/{$jenis->id}.pdf",
                'nama_file_asli' => 'doc.pdf',
                'mime_type' => 'application/pdf',
                'file_size_kb' => 50,
                'status' => 'disetujui',
            ]);
        }
        $pembayaran2 = $siswa2->getOrCreatePembayaran();
        $pembayaran2->update(['status' => 'lunas']);

        // Siswa 2 ambil seragam -> karena kuota promo (1) sudah habis, siswa 2 mendapat tidak_dapat
        $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $siswa2->id,
            ]);

        $siswa2->refresh();
        $this->assertEquals('tidak_dapat', $siswa2->status_benefit);

        // Pendaftaran online baru setelah kuota seragam penuh -> langsung mendapat status tidak_dapat
        $this->post('/ppdb/daftar', [
            'nama_lengkap' => 'Siswa Ketiga Pendaftar Baru',
            'nik' => '3329012304089997',
            'nisn' => '0081239997',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 3',
            'nama_orang_tua' => 'Ortu 3',
            'no_hp_orang_tua' => '081234567877',
            'jurusan_id' => $this->siswa->jurusan_id,
            'jalur_pendaftaran' => 'umum',
            'persetujuan_data' => '1',
        ]);

        $siswa3 = CalonSiswa::where('nik', '3329012304089997')->first();
        $this->assertNotNull($siswa3);
        $this->assertEquals('tidak_dapat', $siswa3->status_benefit);
    }

    public function test_pengambilan_seragam_menetapkan_status_tidak_dapat_jika_promo_dinonaktifkan(): void
    {
        $pengaturan = PengaturanSistem::where('id', 1)->first() ?? PengaturanSistem::instance();
        $pengaturan->update(['is_promo_aktif' => false]);

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
        $pembayaran = $this->siswa->getOrCreatePembayaran();
        $pembayaran->update(['status' => 'lunas']);

        $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
            ]);

        $this->siswa->refresh();
        $this->assertEquals('tidak_dapat', $this->siswa->status_benefit);
    }

    public function test_pengambilan_seragam_ditolak_jika_pembayaran_belum_lunas_meski_berkas_lengkap(): void
    {
        // 1. Lengkapi berkas wajib
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

        // 2. Pastikan tagihan belum lunas
        $pembayaran = $this->siswa->getOrCreatePembayaran();
        $pembayaran->update(['status' => 'menunggu_pembayaran']);

        $response = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.seragam.store'), [
                'calon_siswa_id' => $this->siswa->id,
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('pengambilan_seragams', ['calon_siswa_id' => $this->siswa->id]);
    }

    public function test_hapus_staf_yang_memiliki_riwayat_seragam_dicegah_dan_tidak_crash(): void
    {
        // 1. Buat staf admin piket baru
        $stafBaru = User::create([
            'name' => 'Petugas Seragam Khusus',
            'username' => 'petugas_seragam',
            'email' => 'petugas@smkmuhpaguyangan.sch.id',
            'role' => 'admin_piket',
            'password' => Hash::make('password123'),
            'is_aktif' => true,
        ]);

        // 2. Beri riwayat melayani seragam
        PengambilanSeragam::create([
            'calon_siswa_id' => $this->siswa->id,
            'petugas_id' => $stafBaru->id,
            'waktu_ambil' => now(),
            'catatan' => 'Dilayani oleh Petugas Khusus',
        ]);

        // 3. Super Admin mencoba menghapus staf ini -> Harus dicegah dan tidak crash HTTP 500
        $response = $this->actingAs($this->superAdmin, 'web')
            ->delete(route('admin.staf.destroy', $stafBaru->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $stafBaru->id]);
    }

    public function test_hitungan_pendaftar_jurusan_hanya_menghitung_tahun_ajaran_aktif(): void
    {
        $tahunAktif = TahunAjaran::aktif();
        $tahunLain = TahunAjaran::create(['nama' => '2024/2025', 'is_aktif' => false]);

        $jurusan = Jurusan::first();
        $gelombangLain = Gelombang::create([
            'tahun_ajaran_id' => $tahunLain->id,
            'nama_gelombang' => 'Gelombang 2024',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-03-31',
            'is_aktif' => false,
        ]);

        // Calon siswa di tahun lalu pada jurusan yang sama
        CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/2024/9999',
            'tahun_ajaran_id' => $tahunLain->id,
            'gelombang_id' => $gelombangLain->id,
            'jurusan_id' => $jurusan->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'nomor_urut' => 9999,
            'status_benefit' => 'tidak_dapat',
            'nama_lengkap' => 'Alumni Masa Lalu',
            'nik' => '3329010101990001',
            'nisn' => '0099999991',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2005-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP Lama',
            'nama_orang_tua' => 'Ortu Lama',
            'no_hp_orang_tua' => '081234567801',
            'password' => Hash::make('01012005'),
        ]);

        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.jurusan.index'));

        $response->assertStatus(200);
        $jurusans = $response->viewData('jurusans');
        $jurusanTerhitung = $jurusans->firstWhere('id', $jurusan->id);

        // Hanya siswa pada tahun ajaran aktif yang dihitung (1 orang dari setUp, bukan 2 orang)
        $this->assertEquals(1, $jurusanTerhitung->calon_siswa_count);
    }

    public function test_validasi_overlapping_tanggal_gelombang_pada_tahun_ajaran_yang_sama(): void
    {
        $tahunAktif = TahunAjaran::aktif();

        // Gelombang 1 di seeder adalah 2026-01-01 s/d 2026-03-31
        // Mencoba menambah gelombang yang bertabrakan rentang tanggalnya pada tahun ajaran yang sama
        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.gelombang.store'), [
                'tahun_ajaran_id' => $tahunAktif->id,
                'nama_gelombang' => 'Gelombang Tumpang Tindih',
                'tanggal_mulai' => '2026-02-15',
                'tanggal_selesai' => '2026-04-15',
            ]);

        $response->assertSessionHasErrors('tanggal_mulai');
        $this->assertDatabaseMissing('gelombangs', ['nama_gelombang' => 'Gelombang Tumpang Tindih']);
    }

    public function test_validasi_regex_telepon_orang_tua_dan_tautan_whatsapp(): void
    {
        // 1. Validasi format regex Indonesia 08... ditolak jika bukan 08
        $response = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pendaftar.baru.simpan'), [
                'nama_lengkap' => 'Calon Siswa HP Salah',
                'nik' => '3329010101990002',
                'nisn' => '0099999992',
                'tempat_lahir' => 'Brebes',
                'tanggal_lahir' => '2008-05-10',
                'jenis_kelamin' => 'L',
                'alamat' => 'Paguyangan',
                'asal_sekolah' => 'SMP N 1 Paguyangan',
                'nama_orang_tua' => 'Bapak Budi',
                'no_hp_orang_tua' => '0281123456', // Salah: bukan 08...
                'jurusan_id' => $this->siswa->jurusan_id,
                'jalur_pendaftaran' => 'umum',
                'gelombang_id' => $this->siswa->gelombang_id,
            ]);

        $response->assertSessionHasErrors('no_hp_orang_tua');

        // 2. Format +628... dan 628... otomatis dinormalisasi ke 08...
        $responseSukses = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pendaftar.baru.simpan'), [
                'nama_lengkap' => 'Calon Siswa HP Plus 62',
                'nik' => '3329010101990003',
                'nisn' => '0099999993',
                'tempat_lahir' => 'Brebes',
                'tanggal_lahir' => '2008-05-10',
                'jenis_kelamin' => 'L',
                'alamat' => 'Paguyangan',
                'asal_sekolah' => 'SMP N 1 Paguyangan',
                'nama_orang_tua' => 'Bapak Budi',
                'no_hp_orang_tua' => '+62 812-3456-7890',
                'jurusan_id' => $this->siswa->jurusan_id,
                'jalur_pendaftaran' => 'umum',
                'gelombang_id' => $this->siswa->gelombang_id,
            ]);

        $responseSukses->assertSessionHas('sukses');
        $siswaBaru = CalonSiswa::where('nik', '3329010101990003')->first();
        $this->assertNotNull($siswaBaru);
        $this->assertEquals('081234567890', $siswaBaru->no_hp_orang_tua);
        $this->assertEquals('https://wa.me/6281234567890', $siswaBaru->wa_link_orang_tua);

        // 3. Tautan WhatsApp pada halaman detail pendaftar
        $detailResponse = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.pendaftar.show', $siswaBaru->id));

        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('https://wa.me/6281234567890', false);
    }

    public function test_optimasi_n_plus_satu_cache_jenis_berkas_wajib(): void
    {
        CalonSiswa::clearWajibJenisBerkasCache();

        $wajibUmum = CalonSiswa::getWajibJenisBerkasIds('umum');
        $wajibPrestasi = CalonSiswa::getWajibJenisBerkasIds('prestasi');

        $this->assertNotEmpty($wajibUmum);
        $this->assertNotEmpty($wajibPrestasi);
        $this->assertGreaterThanOrEqual(count($wajibUmum), count($wajibPrestasi));

        // Memanggil berkali-kali tidak me-reset cache
        $this->assertEquals($wajibUmum, CalonSiswa::getWajibJenisBerkasIds('umum'));
    }

    public function test_unggah_dan_edit_foto_jurusan_oleh_admin(): void
    {
        Storage::fake('public');

        $foto = UploadedFile::fake()->image('bengkel-rpl.jpg');

        $storeResponse = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.jurusan.store'), [
                'kode' => 'ANIM2',
                'nama_jurusan' => 'Animasi Digital 3D',
                'bidang_keahlian' => 'Seni dan Ekonomi Kreatif',
                'deskripsi' => 'Kompetensi animasi dan pemodelan objek 3D.',
                'kuota' => 72,
                'urutan' => 10,
                'foto' => $foto,
            ]);

        $storeResponse->assertSessionHas('sukses');
        $jurusan = Jurusan::where('kode', 'ANIM2')->first();
        $this->assertNotNull($jurusan);
        $this->assertNotNull($jurusan->foto);
        Storage::disk('public')->assertExists($jurusan->foto);

        // Edit foto baru
        $fotoBaru = UploadedFile::fake()->image('bengkel-rpl-baru.jpg');
        $oldPath = $jurusan->foto;

        $updateResponse = $this->actingAs($this->superAdmin, 'web')
            ->put(route('admin.jurusan.update', $jurusan->id), [
                'kode' => 'ANIM2',
                'nama_jurusan' => 'Animasi Digital 3D Updated',
                'bidang_keahlian' => 'Seni dan Ekonomi Kreatif',
                'deskripsi' => 'Kompetensi animasi dan pemodelan objek 3D terstandar.',
                'kuota' => 72,
                'urutan' => 10,
                'foto' => $fotoBaru,
            ]);

        $updateResponse->assertSessionHas('sukses');
        $jurusan->refresh();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($jurusan->foto);
    }

    public function test_gelombang_nonaktif_tidak_memicu_error_overlapping(): void
    {
        $tahunAktif = TahunAjaran::aktif();

        // 1. Buat gelombang yang dinonaktifkan (is_aktif = false)
        $gelombangNonaktif = Gelombang::create([
            'tahun_ajaran_id' => $tahunAktif->id,
            'nama_gelombang' => 'Gelombang Arsip Dibatalkan',
            'kode_gelombang' => 'G-BATAL',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-31',
            'is_aktif' => false,
        ]);

        // 2. Buat gelombang baru yang aktif pada rentang tanggal yang sama persis -> HARUS SUKSES karena yang lama nonaktif
        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.gelombang.store'), [
                'tahun_ajaran_id' => $tahunAktif->id,
                'nama_gelombang' => 'Gelombang Baru Pengganti',
                'tanggal_mulai' => '2026-08-01',
                'tanggal_selesai' => '2026-08-31',
            ]);

        $response->assertSessionHas('sukses');
        $this->assertDatabaseHas('gelombangs', ['nama_gelombang' => 'Gelombang Baru Pengganti']);
    }

    public function test_jurusan_dengan_pendaftar_lampau_tombol_hapus_disabled(): void
    {
        $tahunLampau = TahunAjaran::create(['nama' => '2024/2025', 'is_aktif' => false]);
        $tahunAktif = TahunAjaran::aktif();

        $jurusan = Jurusan::create([
            'kode' => 'TKJ-TEST',
            'nama_jurusan' => 'Teknik Komputer Jaringan',
            'bidang_keahlian' => 'TIK',
            'deskripsi' => 'Deskripsi',
            'kuota' => 50,
            'urutan' => 99,
            'is_aktif' => true,
        ]);

        $gelombangLampau = Gelombang::create([
            'tahun_ajaran_id' => $tahunLampau->id,
            'nama_gelombang' => 'G1 Lampau',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-03-31',
            'is_aktif' => false,
        ]);

        // Buat 1 pendaftar di tahun ajaran lampau
        CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/2024/0001',
            'tahun_ajaran_id' => $tahunLampau->id,
            'gelombang_id' => $gelombangLampau->id,
            'jurusan_id' => $jurusan->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'nama_lengkap' => 'Siswa Lampau',
            'nik' => '3329010101990099',
            'nisn' => '0099999099',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2007-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan',
            'asal_sekolah' => 'SMP 1',
            'nama_orang_tua' => 'Ortu',
            'no_hp_orang_tua' => '081234567899',
            'nomor_urut' => 1,
            'status_benefit' => 'tidak_dapat',
            'password' => Hash::make('01012007'),
        ]);

        // Pada tampilan admin jurusan tahun ajaran aktif:
        // Hitungan pendaftar tahun aktif adalah 0
        // Tapi tombol hapus harus disabled karena ada pendaftar historis (total_pendaftar_count = 1)
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.jurusan.index'));

        $response->assertStatus(200);
        $response->assertSee('TKJ-TEST');
        $response->assertSee('Tidak bisa dihapus karena sudah memiliki pendaftar (total 1 pendaftar)');
    }

    public function test_eager_loading_pembayaran_di_halaman_pendaftar_admin(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.pendaftar.index'));

        $response->assertStatus(200);
        // Pastikan view berhasil dimuat dan data relasi pembayaran ter-eager-load
        $pendaftars = $response->viewData('pendaftarList');
        $this->assertNotEmpty($pendaftars);
        $this->assertTrue($pendaftars->first()->relationLoaded('pembayaran'));
    }
}
