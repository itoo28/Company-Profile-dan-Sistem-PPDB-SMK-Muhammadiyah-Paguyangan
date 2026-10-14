<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gelombang extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_gelombang',
        'kode_gelombang',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'is_bidikmisi',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_bidikmisi' => 'boolean',
            'is_aktif' => 'boolean',
        ];
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function calonSiswa()
    {
        return $this->hasMany(CalonSiswa::class, 'gelombang_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->orderBy('tanggal_mulai');
    }

    public function isSedangBerjalan(): bool
    {
        $today = Carbon::today();
        return $this->is_aktif && $today->betweenIncluded($this->tanggal_mulai, $this->tanggal_selesai);
    }

    /**
     * Mendapatkan gelombang yang sedang aktif saat ini berdasarkan tanggal kalender
     * atau gelombang aktif pertama sebagai fallback.
     */
    public static function gelombangAktifSaatIni(?int $tahunAjaranId = null)
    {
        $today = Carbon::today()->format('Y-m-d');
        $query = static::where('is_aktif', true);

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        // Cari yang rentang tanggalnya mencakup hari ini
        $gelombang = (clone $query)
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        if ($gelombang) {
            return $gelombang;
        }

        // Fallback: gelombang aktif terdekat
        return $query->orderBy('tanggal_mulai')->first();
    }
}
