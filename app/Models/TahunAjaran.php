<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public static function aktif()
    {
        return static::where('is_aktif', true)->first();
    }

    public function calonSiswa()
    {
        return $this->hasMany(CalonSiswa::class, 'tahun_ajaran_id');
    }

    public function gelombang()
    {
        return $this->hasMany(Gelombang::class, 'tahun_ajaran_id');
    }

    public static function setAktif(int $id): self
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            static::query()->update(['is_aktif' => false]);
            $tahun = static::findOrFail($id);
            $tahun->is_aktif = true;
            $tahun->save();

            $pengaturan = PengaturanSistem::instance();
            if ($pengaturan) {
                $pengaturan->tahun_ajaran_id = $tahun->id;
                $pengaturan->nomor_urut_terakhir = CalonSiswa::where('tahun_ajaran_id', $tahun->id)->max('nomor_urut') ?? 0;
                $pengaturan->save();
            }

            return $tahun;
        });
    }
}
