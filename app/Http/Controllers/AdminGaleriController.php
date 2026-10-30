<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminGaleriController extends Controller
{
    public function index()
    {
        $galeris = Galeri::latest('tanggal')->paginate(18);
        $kategoriList = ['Praktik Bengkel', 'Kegiatan Sekolah', 'Kepanduan HW', 'Sarana & Lingkungan', 'Prestasi'];

        return view('pages.admin.galeri.index', compact('galeris', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ]);

        $validated['foto'] = $request->file('foto')->store('galeri', 'public');
        if (empty($validated['tanggal'])) {
            $validated['tanggal'] = now();
        }

        $item = Galeri::create($validated);

        LogAktivitas::catat($user, 'tambah_galeri', 'Galeri Sekolah', "Menambahkan dokumentasi foto: {$item->judul} (Kategori: {$item->kategori})");

        return back()->with('sukses', "Foto galeri '{$item->judul}' berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $item = Galeri::findOrFail($id);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ]);

        if ($request->hasFile('foto')) {
            if ($item->foto && !str_starts_with($item->foto, 'images/') && Storage::disk('public')->exists($item->foto)) {
                Storage::disk('public')->delete($item->foto);
            }
            $validated['foto'] = $request->file('foto')->store('galeri', 'public');
        }

        $item->update($validated);

        LogAktivitas::catat($user, 'ubah_galeri', 'Galeri Sekolah', "Memperbarui foto galeri ID #{$item->id}: {$item->judul}");

        return back()->with('sukses', "Foto galeri '{$item->judul}' berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $item = Galeri::findOrFail($id);
        $judul = $item->judul;

        if ($item->foto && !str_starts_with($item->foto, 'images/') && Storage::disk('public')->exists($item->foto)) {
            Storage::disk('public')->delete($item->foto);
        }

        $item->delete();

        LogAktivitas::catat($user, 'hapus_galeri', 'Galeri Sekolah', "Menghapus foto galeri ID #{$id}: {$judul}");

        return back()->with('sukses', "Foto galeri '{$judul}' berhasil dihapus.");
    }
}

