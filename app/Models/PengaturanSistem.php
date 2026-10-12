<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran_id',
        'batas_waktu_berkas',
        'max_file_size_mb',
        'nomor_urut_terakhir',
        'nama_sekolah',
        'slogan',
        'alamat',
        'telepon',
        'whatsapp',
        'email',
        'jam_operasional',
        'maps_embed_url',
    ];

    protected function casts(): array
    {
        return [
            'batas_waktu_berkas' => 'datetime',
            'max_file_size_mb' => 'integer',
            'nomor_urut_terakhir' => 'integer',
        ];
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public static function instance()
    {
        return static::first() ?? new static();
    }
}
