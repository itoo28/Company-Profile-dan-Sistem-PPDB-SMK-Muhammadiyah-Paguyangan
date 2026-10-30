<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'foto',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function getFotoUrlAttribute(): string
    {
        if (empty($this->foto)) {
            return asset('images/gambargedungbaru.jpg');
        }
        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }
        if (str_starts_with($this->foto, 'images/')) {
            return asset($this->foto);
        }
        return asset('storage/' . $this->foto);
    }
}
