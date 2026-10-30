<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'kategori',
        'foto',
        'keterangan',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
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
