<?php

namespace App\Http\Controllers;

use App\Models\InformasiSekolah;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminInformasiSekolahController extends Controller
{
    public function index()
    {
        $visi = InformasiSekolah::getByKey('visi');
        $misi = InformasiSekolah::getByKey('misi');
        $tujuan = InformasiSekolah::getByKey('tujuan');
        $sambutan = InformasiSekolah::getByKey('sambutan_kepala_sekolah');

        return view('pages.admin.informasi-sekolah.index', compact('visi', 'misi', 'tujuan', 'sambutan'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'visi' => ['required', 'string'],
            'misi' => ['required', 'string'],
            'tujuan' => ['required', 'string'],
            'nama_kepala_sekolah' => ['required', 'string', 'max:150'],
            'sambutan' => ['required', 'string'],
            'foto_kepala_sekolah' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // 1. Simpan Visi
        InformasiSekolah::updateOrCreate(
            ['key' => 'visi'],
            ['judul' => 'Visi Sekolah', 'konten' => $validated['visi']]
        );

        // 2. Simpan Misi
        InformasiSekolah::updateOrCreate(
            ['key' => 'misi'],
            ['judul' => 'Misi Sekolah', 'konten' => $validated['misi']]
        );

        // 3. Simpan Tujuan
        InformasiSekolah::updateOrCreate(
            ['key' => 'tujuan'],
            ['judul' => 'Tujuan Pendidikan Sekolah', 'konten' => $validated['tujuan']]
        );

        // 4. Simpan Sambutan Kepala Sekolah
        $sambutanRecord = InformasiSekolah::getByKey('sambutan_kepala_sekolah');
        $fotoPath = $sambutanRecord?->gambar ?? 'images/kepala-sekolah.jpg';

        if ($request->hasFile('foto_kepala_sekolah')) {
            if ($sambutanRecord && !empty($sambutanRecord->gambar) && !str_starts_with($sambutanRecord->gambar, 'images/')) {
                $rawStoragePath = str_replace('storage/', '', $sambutanRecord->gambar);
                if (Storage::disk('public')->exists($rawStoragePath)) {
                    Storage::disk('public')->delete($rawStoragePath);
                }
            }
            $uploaded = $request->file('foto_kepala_sekolah')->store('kepala_sekolah', 'public');
            $fotoPath = 'storage/' . $uploaded;
        }

        InformasiSekolah::updateOrCreate(
            ['key' => 'sambutan_kepala_sekolah'],
            [
                'judul' => 'Sambutan Kepala Sekolah',
                'konten' => $validated['sambutan'],
                'gambar' => $fotoPath,
                'extra_data' => [
                    'nama_kepala_sekolah' => $validated['nama_kepala_sekolah'],
                ],
            ]
        );

        LogAktivitas::catat($user, 'ubah_informasi_sekolah', 'Profil Sekolah', 'Super Admin memperbarui informasi Visi, Misi, Tujuan, dan Sambutan Kepala Sekolah.');

        return back()->with('sukses', 'Profil sekolah, Visi Misi, dan Sambutan Kepala Sekolah berhasil disimpan.');
    }
}

