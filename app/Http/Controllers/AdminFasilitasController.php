<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminFasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = Fasilitas::orderBy('urutan')->get();
        $kategoriList = ['Bengkel & Laboratorium', 'Ruang Belajar', 'Fasilitas Penunjang', 'Ibadah & Olahraga'];

        return view('pages.admin.fasilitas.index', compact('fasilitas', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('fasilitas', 'public');
        }

        $item = Fasilitas::create($validated);

        LogAktivitas::catat($user, 'tambah_fasilitas', 'Fasilitas Sekolah', "Menambahkan fasilitas sarana: {$item->nama} (Kategori: {$item->kategori})");

        return back()->with('sukses', "Fasilitas '{$item->nama}' berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $item = Fasilitas::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($item->foto && !str_starts_with($item->foto, 'images/') && Storage::disk('public')->exists($item->foto)) {
                Storage::disk('public')->delete($item->foto);
            }
            $validated['foto'] = $request->file('foto')->store('fasilitas', 'public');
        }

        $item->update($validated);

        LogAktivitas::catat($user, 'ubah_fasilitas', 'Fasilitas Sekolah', "Memperbarui fasilitas sarana ID #{$item->id}: {$item->nama}");

        return back()->with('sukses', "Fasilitas '{$item->nama}' berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $item = Fasilitas::findOrFail($id);
        $nama = $item->nama;

        if ($item->foto && !str_starts_with($item->foto, 'images/') && Storage::disk('public')->exists($item->foto)) {
            Storage::disk('public')->delete($item->foto);
        }

        $item->delete();

        LogAktivitas::catat($user, 'hapus_fasilitas', 'Fasilitas Sekolah', "Menghapus fasilitas sarana ID #{$id}: {$nama}");

        return back()->with('sukses', "Fasilitas '{$nama}' berhasil dihapus.");
    }
}

