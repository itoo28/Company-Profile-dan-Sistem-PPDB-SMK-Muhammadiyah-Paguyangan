<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prestasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_prestasi',
        'slug',
        'nama_peraih',
        'tingkat',
        'peringkat',
        'tanggal_perolehan',
        'deskripsi',
        'foto',
        'status',
        'dibuat_oleh',
        'diubah_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perolehan' => 'date',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pengubah()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', 'terbit');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        $cleanPath = ltrim($this->foto, '/');

        if (str_starts_with($cleanPath, 'images/')) {
            return asset($cleanPath);
        }

        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($prestasi) {
            if (empty($prestasi->slug)) {
                $prestasi->slug = Str::slug($prestasi->nama_prestasi . '-' . $prestasi->nama_peraih) . '-' . Str::random(5);
            }
        });
    }
}
