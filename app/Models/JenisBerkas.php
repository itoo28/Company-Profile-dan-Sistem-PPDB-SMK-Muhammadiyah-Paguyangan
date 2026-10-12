<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBerkas extends Model
{
    use HasFactory;

    protected $table = 'jenis_berkas';

    protected $fillable = [
        'nama',
        'is_wajib',
        'deskripsi',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_wajib' => 'boolean',
            'urutan' => 'integer',
        ];
    }
}
