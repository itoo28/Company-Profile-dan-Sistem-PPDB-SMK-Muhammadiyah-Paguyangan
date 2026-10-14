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
        'kuota_promo_benefit',
        'is_promo_aktif',
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
            'kuota_promo_benefit' => 'integer',
            'is_promo_aktif' => 'boolean',
        ];
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public static function instance()
    {
        return static::first() ?? new static([
            'nama_sekolah' => 'SMK Muhammadiyah Paguyangan',
            'slogan' => 'Pusat Keunggulan Vokasi Islami Berkarakter dan Siap Kerja',
            'alamat' => 'Jl. Raya Paguyangan, Brebes, Jawa Tengah',
            'telepon' => '(0289) 430123',
            'whatsapp' => '0857-2345-6789',
            'email' => 'info@smkmuhpaguyangan.sch.id',
            'jam_operasional' => 'Senin - Jumat: 07.00 - 15.30 WIB',
            'kuota_promo_benefit' => 100,
            'is_promo_aktif' => true,
            'max_file_size_mb' => 2,
        ]);
    }
}
