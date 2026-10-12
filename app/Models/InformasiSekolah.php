<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiSekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'judul',
        'konten',
        'gambar',
        'extra_data',
    ];

    protected function casts(): array
    {
        return [
            'extra_data' => 'array',
        ];
    }

    public static function getByKey(string $key, $default = null)
    {
        return static::where('key', $key)->first() ?? $default;
    }
}
