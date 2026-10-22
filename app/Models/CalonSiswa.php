<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CalonSiswa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'calon_siswas';

    protected $fillable = [
        'nomor_pendaftaran',
        'tahun_ajaran_id',
        'gelombang_id',
        'jurusan_id',
        'jalur_pendaftaran',
        'jalur_registrasi',
        'nomor_urut',
        'status_benefit',
        'nama_lengkap',
        'nik',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'asal_sekolah',
        'nama_orang_tua',
        'no_hp_orang_tua',
        'password',
        'is_unlocked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'nomor_urut' => 'integer',
            'is_unlocked' => 'boolean',
        ];
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function gelombang()
    {
        return $this->belongsTo(Gelombang::class, 'gelombang_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function berkas()
    {
        return $this->hasMany(BerkasSiswa::class, 'calon_siswa_id');
    }

    public function pengambilanSeragam()
    {
        return $this->hasOne(PengambilanSeragam::class, 'calon_siswa_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'calon_siswa_id');
    }

    public function getOrCreatePembayaran(): Pembayaran
    {
        if ($this->relationLoaded('pembayaran') && $this->pembayaran) {
            return $this->pembayaran;
        }

        $existing = $this->pembayaran()->first();
        if ($existing) {
            $this->setRelation('pembayaran', $existing);
            return $existing;
        }

        $pengaturan = PengaturanSistem::instance();
        $biaya = (int) ($pengaturan->biaya_pendaftaran ?? 100000);

        $baru = $this->pembayaran()->create([
            'kode_transaksi' => 'INV/' . date('Y') . '/' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT),
            'nominal' => $biaya,
            'status' => $biaya <= 0 ? 'lunas' : 'menunggu_pembayaran',
        ]);

        $this->setRelation('pembayaran', $baru);
        return $baru;
    }

    public function isPembayaranLunas(): bool
    {
        return $this->pembayaran?->isLunas() ?? false;
    }

    /**
     * Mengecek apakah semua berkas wajib sesuai jalur pendaftarannya berstatus 'disetujui' (BR-05)
     */
    public function isSeragamClearanceApproved(): bool
    {
        // Ambil jenis berkas wajib untuk jalur siswa ini
        $jenisBerkasWajibQuery = JenisBerkas::where('is_aktif', true);

        if ($this->jalur_pendaftaran === 'prestasi') {
            $jenisBerkasWajibQuery->whereIn('aturan_wajib', ['semua', 'khusus_prestasi']);
        } else {
            $jenisBerkasWajibQuery->where('aturan_wajib', 'semua');
        }

        $wajibIds = $jenisBerkasWajibQuery->pluck('id')->toArray();

        if (empty($wajibIds)) {
            return false;
        }

        // Ambil berkas siswa yang disetujui untuk berkas wajib
        $disetujuiCount = $this->berkas()
            ->whereIn('jenis_berkas_id', $wajibIds)
            ->where('status', 'disetujui')
            ->count();

        return $disetujuiCount === count($wajibIds);
    }

    /**
     * Mengecek apakah siswa sedang terkunci (Lockdown, FR-10 & BR-08)
     */
    public function isLockdownActive(): bool
    {
        if ($this->is_unlocked) {
            return false;
        }

        $pengaturan = PengaturanSistem::instance();
        if (!$pengaturan || !$pengaturan->batas_waktu_berkas) {
            return false;
        }

        return Carbon::now()->greaterThan($pengaturan->batas_waktu_berkas);
    }

    /**
     * Hitung sisa detik menuju batas waktu berkas (berbasis waktu server)
     */
    public function getSisaWaktuDetikAttribute(): int
    {
        $pengaturan = PengaturanSistem::instance();
        if (!$pengaturan || !$pengaturan->batas_waktu_berkas) {
            return 0;
        }

        $now = Carbon::now();
        if ($now->greaterThanOrEqualTo($pengaturan->batas_waktu_berkas)) {
            return 0;
        }

        return (int) $now->diffInSeconds($pengaturan->batas_waktu_berkas);
    }

    /**
     * Status berkas ringkas per siswa (default PRD FR-26)
     * Prioritas: Ada yang Ditolak -> Menunggu Verifikasi -> Belum Lengkap -> Disetujui Semua
     */
    public function getStatusBerkasRingkasAttribute(): string
    {
        $berkas = $this->berkas;

        // Ambil ID berkas wajib
        $jenisBerkasWajibQuery = JenisBerkas::where('is_aktif', true);
        if ($this->jalur_pendaftaran === 'prestasi') {
            $jenisBerkasWajibQuery->whereIn('aturan_wajib', ['semua', 'khusus_prestasi']);
        } else {
            $jenisBerkasWajibQuery->where('aturan_wajib', 'semua');
        }
        $wajibIds = $jenisBerkasWajibQuery->pluck('id')->toArray();

        if ($berkas->where('status', 'ditolak')->isNotEmpty()) {
            return 'ditolak'; // Ada yang Ditolak
        }

        if ($berkas->where('status', 'menunggu')->isNotEmpty()) {
            return 'menunggu'; // Menunggu Verifikasi
        }

        $disetujuiWajibCount = $berkas->whereIn('jenis_berkas_id', $wajibIds)
            ->where('status', 'disetujui')
            ->count();

        if ($disetujuiWajibCount === count($wajibIds) && count($wajibIds) > 0) {
            return 'disetujui'; // Disetujui Semua
        }

        return 'belum_lengkap'; // Belum Lengkap
    }
}
