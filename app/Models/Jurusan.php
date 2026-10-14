<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama_jurusan',
        'bidang_keahlian',
        'deskripsi',
        'prospek_kerja',
        'kuota',
        'foto',
        'is_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'kuota' => 'integer',
            'is_aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function calonSiswa()
    {
        return $this->hasMany(CalonSiswa::class, 'jurusan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->orderBy('urutan');
    }
}
