<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBerkas extends Model
{
    use HasFactory;

    protected $table = 'jenis_berkas';

    protected $fillable = [
        'nama',
        'is_wajib',
        'aturan_wajib', // 'semua', 'khusus_prestasi', 'opsional'
        'deskripsi',
        'urutan',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_wajib' => 'boolean',
            'is_aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->orderBy('urutan');
    }

    /**
     * Mengecek apakah jenis berkas ini wajib untuk jalur pendaftaran tertentu (BR-13)
     */
    public function isWajibUntukJalur(string $jalur): bool
    {
        if ($this->aturan_wajib === 'semua') {
            return true;
        }

        if ($this->aturan_wajib === 'khusus_prestasi' && $jalur === 'prestasi') {
            return true;
        }

        return false;
    }
}
