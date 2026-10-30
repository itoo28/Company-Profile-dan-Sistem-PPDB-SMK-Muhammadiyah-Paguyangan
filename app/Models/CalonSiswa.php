<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

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

    protected static ?array $cachedWajibIds = null;

    /**
     * Mendapatkan daftar ID jenis berkas wajib per jalur dengan cache in-memory untuk cegah N+1 query
     */
    public static function getWajibJenisBerkasIds(string $jalur): array
    {
        if (self::$cachedWajibIds === null) {
            $semuaWajib = JenisBerkas::where('is_aktif', true)->get();
            self::$cachedWajibIds = [
                'umum' => $semuaWajib->where('aturan_wajib', 'semua')->pluck('id')->values()->toArray(),
                'prestasi' => $semuaWajib->whereIn('aturan_wajib', ['semua', 'khusus_prestasi'])->pluck('id')->values()->toArray(),
            ];
        }

        return self::$cachedWajibIds[$jalur] ?? self::$cachedWajibIds['umum'];
    }

    public static function clearWajibJenisBerkasCache(): void
    {
        self::$cachedWajibIds = null;
    }

    public function isPembayaranLunas(): bool
    {
        return $this->pembayaran?->isLunas() ?? false;
    }

    /**
     * Mengecek apakah semua berkas wajib sesuai jalur pendaftarannya berstatus 'disetujui' (BR-05)
     */
    public function isBerkasWajibDisetujui(): bool
    {
        $wajibIds = self::getWajibJenisBerkasIds($this->jalur_pendaftaran);

        if (empty($wajibIds)) {
            return false;
        }

        if ($this->relationLoaded('berkas')) {
            $disetujuiCount = $this->berkas
                ->whereIn('jenis_berkas_id', $wajibIds)
                ->where('status', 'disetujui')
                ->count();
        } else {
            $disetujuiCount = $this->berkas()
                ->whereIn('jenis_berkas_id', $wajibIds)
                ->where('status', 'disetujui')
                ->count();
        }

        return $disetujuiCount === count($wajibIds);
    }

    /**
     * Mengecek apakah clearance seragam terpenuhi: pembayaran lunas DAN semua berkas wajib disetujui (BR-05)
     */
    public function isSeragamClearanceApproved(): bool
    {
        return $this->isPembayaranLunas() && $this->isBerkasWajibDisetujui();
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

        $wajibIds = self::getWajibJenisBerkasIds($this->jalur_pendaftaran);

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

    /**
     * Tautan WhatsApp resmi untuk menghubungi orang tua siswa (FR-21 & Bab 10)
     */
    public function getWaLinkOrangTuaAttribute(): ?string
    {
        if (empty($this->no_hp_orang_tua)) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', (string) $this->no_hp_orang_tua);
        if (str_starts_with($clean, '628')) {
            // Sudah diawali kode negara 62
        } elseif (str_starts_with($clean, '08')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return 'https://wa.me/' . $clean;
    }

    /**
     * Mengecek apakah siswa masih menggunakan kata sandi default (tanggal lahir format DDMMYYYY).
     */
    public function isMenggunakanPasswordDefault(): bool
    {
        if (!$this->tanggal_lahir || !$this->password) {
            return false;
        }

        $defaultPassword = Carbon::parse($this->tanggal_lahir)->format('dmY');
        return Hash::check($defaultPassword, $this->password);
    }

    /**
     * Helper normalisasi berbagai input tanggal lahir ke string 'DDMMYYYY'
     */
    public static function normalisasiTanggalLahirInput(?string $input): string
    {
        if (!$input) {
            return '';
        }

        $input = trim($input);
        $digitsOnly = preg_replace('/[^0-9]/', '', $input);
        if (strlen($digitsOnly) === 8) {
            if (str_starts_with($digitsOnly, '19') || str_starts_with($digitsOnly, '20')) {
                try {
                    return Carbon::createFromFormat('Ymd', $digitsOnly)->format('dmY');
                } catch (\Exception $e) {
                }
            }
            return $digitsOnly;
        }

        try {
            return Carbon::parse($input)->format('dmY');
        } catch (\Exception $e) {
            return $input;
        }
    }

    /**
     * Memeriksa apakah input sandi cocok dengan password yang tersimpan,
     * baik melalui kecocokan langsung maupun normalisasi tanggal lahir.
     */
    public function checkPassword(string $input): bool
    {
        if (empty($input) || empty($this->password)) {
            return false;
        }

        if (Hash::check($input, $this->password)) {
            return true;
        }

        $normalized = self::normalisasiTanggalLahirInput($input);
        if ($normalized !== $input && Hash::check($normalized, $this->password)) {
            return true;
        }

        return false;
    }
}
