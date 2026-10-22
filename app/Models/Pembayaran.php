<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'calon_siswa_id',
        'kode_transaksi',
        'nominal',
        'metode_pembayaran',
        'status',
        'nama_pengirim',
        'bank_pengirim',
        'tanggal_bayar',
        'bukti_transfer',
        'catatan_admin',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'tanggal_bayar' => 'date',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status === 'menunggu_verifikasi';
    }

    public function isMenungguPembayaran(): bool
    {
        return $this->status === 'menunggu_pembayaran';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'lunas' => 'Lunas',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'menunggu_pembayaran' => 'Belum Dibayar',
            'ditolak' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
