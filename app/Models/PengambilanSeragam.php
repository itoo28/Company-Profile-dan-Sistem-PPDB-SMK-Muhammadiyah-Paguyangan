<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanSeragam extends Model
{
    use HasFactory;

    protected $table = 'pengambilan_seragams';

    protected $fillable = [
        'calon_siswa_id',
        'petugas_id',
        'waktu_ambil',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_ambil' => 'datetime',
        ];
    }

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
