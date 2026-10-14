<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasSiswa extends Model
{
    use HasFactory;

    protected $table = 'berkas_siswas';

    protected $fillable = [
        'calon_siswa_id',
        'jenis_berkas_id',
        'file_path',
        'nama_file_asli',
        'file_size_kb',
        'mime_type',
        'status',
        'catatan_penolakan',
        'diverifikasi_oleh',
        'waktu_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'file_size_kb' => 'integer',
            'waktu_verifikasi' => 'datetime',
        ];
    }

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function jenisBerkas()
    {
        return $this->belongsTo(JenisBerkas::class, 'jenis_berkas_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
