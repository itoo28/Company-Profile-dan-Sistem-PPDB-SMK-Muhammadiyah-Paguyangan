<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\Pembayaran;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembayaranPpdbTest extends TestCase
{
    use RefreshDatabase;

    protected CalonSiswa $siswa;
    protected User $adminPiket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $jurusan = Jurusan::first();
        $gelombang = Gelombang::where('is_aktif', true)->first();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $this->siswa = CalonSiswa::create([
            'nomor_pendaftaran' => 'PPDB/' . date('Y') . '/0099',
            'nomor_urut' => 99,
            'nama_lengkap' => 'Ahmad Faiz',
            'nik' => '3329012304080099',
            'nisn' => '0081234599',
            'tempat_lahir' => 'Brebes',
            'tanggal_lahir' => '2008-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Paguyangan, Brebes',
            'asal_sekolah' => 'SMP Muhammadiyah Paguyangan',
            'nama_orang_tua' => 'Suryadi',
            'no_hp_orang_tua' => '081234567888',
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jalur_pendaftaran' => 'umum',
            'jalur_registrasi' => 'online',
            'password' => Hash::make('15052008'),
        ]);

        $this->adminPiket = User::where('role', 'admin_piket')->first();
    }

    public function test_tagihan_pembayaran_otomatis_terbuat_saat_diakses(): void
    {
        $pembayaran = $this->siswa->getOrCreatePembayaran();

        $this->assertNotNull($pembayaran);
        $this->assertEquals('menunggu_pembayaran', $pembayaran->status);
        $this->assertEquals(100000, $pembayaran->nominal);
        $this->assertDatabaseHas('pembayarans', [
            'calon_siswa_id' => $this->siswa->id,
            'status' => 'menunggu_pembayaran',
        ]);
    }

    public function test_calon_siswa_dapat_mengunggah_bukti_transfer_pembayaran(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('struk_transfer.jpg', 200, 'image/jpeg');

        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.pembayaran.konfirmasi'), [
                'nama_pengirim' => 'Suryadi',
                'bank_pengirim' => 'Bank Mandiri',
                'tanggal_bayar' => date('Y-m-d'),
                'bukti_transfer' => $file,
            ]);

        $response->assertRedirect(route('siswa.dashboard'));
        $response->assertSessionHas('sukses');

        $pembayaran = $this->siswa->fresh()->pembayaran;
        $this->assertEquals('menunggu_verifikasi', $pembayaran->status);
        $this->assertEquals('transfer', $pembayaran->metode_pembayaran);
        $this->assertEquals('Suryadi', $pembayaran->nama_pengirim);
        $this->assertNotNull($pembayaran->bukti_transfer);
        Storage::disk('local')->assertExists($pembayaran->bukti_transfer);
    }

    public function test_admin_piket_dapat_menyetujui_pembayaran_transfer(): void
    {
        $pembayaran = $this->siswa->getOrCreatePembayaran();
        $pembayaran->update([
            'status' => 'menunggu_verifikasi',
            'metode_pembayaran' => 'transfer',
            'nama_pengirim' => 'Suryadi',
            'tanggal_bayar' => date('Y-m-d'),
        ]);

        $response = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pembayaran.verifikasi', $pembayaran->id), [
                'status' => 'lunas',
            ]);

        $response->assertSessionHas('sukses');
        $this->assertEquals('lunas', $pembayaran->fresh()->status);
        $this->assertEquals($this->adminPiket->id, $pembayaran->fresh()->diverifikasi_oleh);
    }

    public function test_admin_piket_dapat_menolak_pembayaran_dengan_catatan(): void
    {
        $pembayaran = $this->siswa->getOrCreatePembayaran();
        $pembayaran->update(['status' => 'menunggu_verifikasi']);

        $response = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pembayaran.verifikasi', $pembayaran->id), [
                'status' => 'ditolak',
                'catatan_admin' => 'Nominal transfer tidak sesuai dengan tagihan.',
            ]);

        $response->assertSessionHas('sukses');
        $pembayaran = $pembayaran->fresh();
        $this->assertEquals('ditolak', $pembayaran->status);
        $this->assertEquals('Nominal transfer tidak sesuai dengan tagihan.', $pembayaran->catatan_admin);
    }

    public function test_admin_piket_dapat_mencatat_pembayaran_tunai_langsung_di_loket(): void
    {
        $response = $this->actingAs($this->adminPiket, 'web')
            ->post(route('admin.pembayaran.tunai'), [
                'calon_siswa_id' => $this->siswa->id,
                'tanggal_bayar' => date('Y-m-d'),
                'nominal' => 100000,
                'catatan' => 'Diterima tunai di loket kasir PPDB',
            ]);

        $response->assertSessionHas('sukses');

        $pembayaran = $this->siswa->fresh()->pembayaran;
        $this->assertEquals('lunas', $pembayaran->status);
        $this->assertEquals('tunai', $pembayaran->metode_pembayaran);
        $this->assertEquals(100000, $pembayaran->nominal);
    }

    public function test_siswa_hanya_dapat_mengunduh_kuitansi_jika_sudah_lunas(): void
    {
        // 1. Saat belum lunas -> Ditolak / diarahkan kembali
        $pembayaran = $this->siswa->getOrCreatePembayaran();
        $responseBelum = $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.pembayaran.kuitansi'));

        $responseBelum->assertRedirect(route('siswa.dashboard'));
        $responseBelum->assertSessionHas('error');

        // 2. Saat sudah lunas -> Sukses 200 OK
        $pembayaran->update(['status' => 'lunas']);
        $responseLunas = $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.pembayaran.kuitansi'));

        $responseLunas->assertStatus(200);
        $responseLunas->assertSee('TANDA BUKTI PEMBAYARAN');
        $responseLunas->assertSee($this->siswa->nama_lengkap);
    }
}
