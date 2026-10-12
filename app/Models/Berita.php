<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'isi',
        'gambar_sampul',
        'tanggal_terbit',
        'status',
        'penulis',
        'dibuat_oleh',
        'diubah_oleh',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'views_count' => 'integer',
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

    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', 'terbit');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul) . '-' . Str::random(5);
            }
        });
    }
}
