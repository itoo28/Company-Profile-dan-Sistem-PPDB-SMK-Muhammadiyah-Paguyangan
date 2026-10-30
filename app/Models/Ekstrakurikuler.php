<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'ekstrakurikulers';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'pembina',
        'jadwal',
        'foto',
    ];

    public function getFotoUrlAttribute(): string
    {
        if (empty($this->foto)) {
            return asset('images/gambarutama2.jpeg');
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
