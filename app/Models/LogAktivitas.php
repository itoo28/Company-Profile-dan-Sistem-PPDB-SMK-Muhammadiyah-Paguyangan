<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'nama_pelaku',
        'peran',
        'jenis_aksi',
        'objek',
        'keterangan',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function catat($user, string $jenisAksi, ?string $objek = null, ?string $keterangan = null): self
    {
        return static::create([
            'user_id' => $user?->id,
            'nama_pelaku' => $user?->name ?? 'Sistem',
            'peran' => $user?->role ?? 'sistem',
            'jenis_aksi' => $jenisAksi,
            'objek' => $objek,
            'keterangan' => $keterangan,
            'ip_address' => request()->ip(),
        ]);
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['q'])) {
            $cari = trim($filters['q']);
            $query->where(function ($q) use ($cari) {
                $q->where('nama_pelaku', 'like', "%{$cari}%")
                  ->orWhere('jenis_aksi', 'like', "%{$cari}%")
                  ->orWhere('objek', 'like', "%{$cari}%")
                  ->orWhere('keterangan', 'like', "%{$cari}%")
                  ->orWhere('ip_address', 'like', "%{$cari}%");
            });
        }

        if (!empty($filters['objek'])) {
            $query->where('objek', $filters['objek']);
        }

        if (!empty($filters['peran'])) {
            $query->where('peran', $filters['peran']);
        }

        if (!empty($filters['kategori_aksi'])) {
            $kategori = $filters['kategori_aksi'];
            if ($kategori === 'autentikasi') {
                $query->whereIn('jenis_aksi', ['login', 'logout', 'login_gagal', 'login_ditolak']);
            } elseif ($kategori === 'tambah') {
                $query->where(function ($q) {
                    $q->where('jenis_aksi', 'like', 'tambah_%')
                      ->orWhere('jenis_aksi', 'daftar_offline');
                });
            } elseif ($kategori === 'ubah') {
                $query->where(function ($q) {
                    $q->where('jenis_aksi', 'like', 'ubah_%')
                      ->orWhere('jenis_aksi', 'like', 'update_%')
                      ->orWhere('jenis_aksi', 'koreksi_data')
                      ->orWhere('jenis_aksi', 'toggle_staf')
                      ->orWhere('jenis_aksi', 'reset_password_staf')
                      ->orWhere('jenis_aksi', 'reset_password_pendaftar');
                });
            } elseif ($kategori === 'hapus') {
                $query->where('jenis_aksi', 'like', 'hapus_%');
            } elseif ($kategori === 'verifikasi') {
                $query->where(function ($q) {
                    $q->where('jenis_aksi', 'like', 'verifikasi_%')
                      ->orWhere('jenis_aksi', 'like', 'tolak_%')
                      ->orWhere('jenis_aksi', 'bayar_tunai')
                      ->orWhere('jenis_aksi', 'unlock_berkas');
                });
            } elseif ($kategori === 'seragam') {
                $query->whereIn('jenis_aksi', ['ambil_seragam', 'batal_seragam']);
            } elseif ($kategori === 'ekspor') {
                $query->where('jenis_aksi', 'ekspor_data');
            } elseif ($kategori === 'keamanan') {
                $query->whereIn('jenis_aksi', ['login_gagal', 'login_ditolak']);
            }
        }

        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('created_at', '>=', $filters['tanggal_mulai']);
        }

        if (!empty($filters['tanggal_selesai'])) {
            $query->whereDate('created_at', '<=', $filters['tanggal_selesai']);
        }

        return $query;
    }

    public function getBadgeColorAttribute(): string
    {
        $aksi = $this->jenis_aksi;

        if (in_array($aksi, ['login_gagal', 'login_ditolak', 'tolak_pembayaran', 'batal_seragam']) || str_starts_with($aksi, 'hapus_')) {
            return 'rose';
        }

        if (in_array($aksi, ['login', 'verifikasi_pembayaran', 'bayar_tunai', 'ambil_seragam', 'daftar_offline']) || str_starts_with($aksi, 'tambah_') || str_starts_with($aksi, 'verifikasi_')) {
            return 'emerald';
        }

        if (in_array($aksi, ['unlock_berkas', 'toggle_staf', 'reset_password_staf', 'reset_password_pendaftar', 'koreksi_data', 'ubah_pengaturan']) || str_starts_with($aksi, 'ubah_') || str_starts_with($aksi, 'update_') || str_starts_with($aksi, 'aktifkan_')) {
            return 'amber';
        }

        if (in_array($aksi, ['ekspor_data'])) {
            return 'blue';
        }

        return 'zinc';
    }

    public function getNamaAksiFormattedAttribute(): string
    {
        $mapping = [
            'login' => 'Login Berhasil',
            'logout' => 'Keluar Sistem',
            'login_gagal' => 'Gagal Login',
            'login_ditolak' => 'Login Ditolak',
            'verifikasi_berkas' => 'Verifikasi Berkas',
            'unlock_berkas' => 'Buka Kunci Berkas',
            'koreksi_data' => 'Koreksi Data Siswa',
            'daftar_offline' => 'Pendaftaran Loket',
            'hapus_pendaftar' => 'Hapus Pendaftar',
            'verifikasi_pembayaran' => 'Verifikasi Bayar',
            'tolak_pembayaran' => 'Tolak Bayar',
            'bayar_tunai' => 'Bayar Tunai Loket',
            'ambil_seragam' => 'Ambil Seragam',
            'batal_seragam' => 'Batal Seragam',
            'ekspor_data' => 'Ekspor Laporan',
            'tambah_berita' => 'Tambah Berita',
            'ubah_berita' => 'Ubah Berita',
            'hapus_berita' => 'Hapus Berita',
            'tambah_prestasi' => 'Tambah Prestasi',
            'ubah_prestasi' => 'Ubah Prestasi',
            'hapus_prestasi' => 'Hapus Prestasi',
            'tambah_pengumuman' => 'Tambah Pengumuman',
            'ubah_pengumuman' => 'Ubah Pengumuman',
            'hapus_pengumuman' => 'Hapus Pengumuman',
            'tambah_fasilitas' => 'Tambah Fasilitas',
            'ubah_fasilitas' => 'Ubah Fasilitas',
            'hapus_fasilitas' => 'Hapus Fasilitas',
            'tambah_ekskul' => 'Tambah Ekskul',
            'ubah_ekskul' => 'Ubah Ekskul',
            'hapus_ekskul' => 'Hapus Ekskul',
            'tambah_galeri' => 'Tambah Galeri',
            'ubah_galeri' => 'Ubah Galeri',
            'hapus_galeri' => 'Hapus Galeri',
            'ubah_informasi_sekolah' => 'Ubah Profil Sekolah',
            'update_pesan_masuk' => 'Update Pesan Kontak',
            'hapus_pesan_masuk' => 'Hapus Pesan Kontak',
            'tambah_gelombang' => 'Tambah Gelombang',
            'ubah_gelombang' => 'Ubah Gelombang',
            'hapus_gelombang' => 'Hapus Gelombang',
            'tambah_jurusan' => 'Tambah Jurusan',
            'ubah_jurusan' => 'Ubah Jurusan',
            'hapus_jurusan' => 'Hapus Jurusan',
            'tambah_jenis_berkas' => 'Tambah Jenis Berkas',
            'ubah_jenis_berkas' => 'Ubah Jenis Berkas',
            'hapus_jenis_berkas' => 'Hapus Jenis Berkas',
            'tambah_tahun_ajaran' => 'Tambah Tahun Ajaran',
            'ubah_tahun_ajaran' => 'Ubah Tahun Ajaran',
            'hapus_tahun_ajaran' => 'Hapus Tahun Ajaran',
            'aktifkan_tahun_ajaran' => 'Set Tahun Ajaran Aktif',
            'tambah_staf' => 'Tambah Akun Staf',
            'ubah_staf' => 'Ubah Akun Staf',
            'hapus_staf' => 'Hapus Akun Staf',
            'toggle_staf' => 'Ubah Status Staf',
            'reset_password_staf' => 'Reset Sandi Staf',
            'ubah_pengaturan' => 'Ubah Pengaturan PPDB',
        ];

        return $mapping[$this->jenis_aksi] ?? ucwords(str_replace('_', ' ', $this->jenis_aksi));
    }

    public function getBadgePeranColorAttribute(): string
    {
        return match($this->peran) {
            'super_admin' => 'purple',
            'admin_piket' => 'teal',
            default => 'zinc',
        };
    }

    public function getPeranFormattedAttribute(): string
    {
        return match($this->peran) {
            'super_admin' => 'Super Admin',
            'admin_piket' => 'Admin Piket',
            'sistem' => 'Sistem Otomatis',
            default => ucwords(str_replace('_', ' ', (string) $this->peran)),
        };
    }
}
