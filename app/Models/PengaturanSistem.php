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
        'biaya_pendaftaran',
        'nama_bank',
        'nomor_rekening',
        'atas_nama_rekening',
        'instruksi_pembayaran',
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
            'biaya_pendaftaran' => 'integer',
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
            'biaya_pendaftaran' => 100000,
            'nama_bank' => 'Bank Syariah Indonesia (BSI)',
            'nomor_rekening' => '7123456789',
            'atas_nama_rekening' => 'SMK Muhammadiyah Paguyangan',
            'instruksi_pembayaran' => 'Silakan lakukan transfer ke rekening resmi SMK Muhammadiyah Paguyangan atau bayar langsung secara tunai di loket bendahara sekolah.',
            'max_file_size_mb' => 2,
        ]);
    }
}
