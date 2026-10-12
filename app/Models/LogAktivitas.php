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
}
