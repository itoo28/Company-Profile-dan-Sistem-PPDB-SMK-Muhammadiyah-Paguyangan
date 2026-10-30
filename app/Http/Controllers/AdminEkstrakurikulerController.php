<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminEkstrakurikulerController extends Controller
{
    public function index()
    {
        $ekskul = Ekstrakurikuler::latest()->get();
        $kategoriList = ['Kepanduan', 'Beladiri', 'Kejuruan & IT', 'Keagamaan', 'Olahraga', 'Seni & Budaya'];

        return view('pages.admin.ekstrakurikuler.index', compact('ekskul', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'pembina' => ['nullable', 'string', 'max:150'],
            'jadwal' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('ekstrakurikuler', 'public');
        }

        $item = Ekstrakurikuler::create($validated);

        LogAktivitas::catat($user, 'tambah_ekskul', 'Ekstrakurikuler', "Menambahkan ekstrakurikuler: {$item->nama} (Kategori: {$item->kategori})");

        return back()->with('sukses', "Ekstrakurikuler '{$item->nama}' berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $item = Ekstrakurikuler::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'pembina' => ['nullable', 'string', 'max:150'],
            'jadwal' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($item->foto && !str_starts_with($item->foto, 'images/') && Storage::disk('public')->exists($item->foto)) {
                Storage::disk('public')->delete($item->foto);
            }
            $validated['foto'] = $request->file('foto')->store('ekstrakurikuler', 'public');
        }

        $item->update($validated);

        LogAktivitas::catat($user, 'ubah_ekskul', 'Ekstrakurikuler', "Memperbarui ekstrakurikuler ID #{$item->id}: {$item->nama}");

        return back()->with('sukses', "Ekstrakurikuler '{$item->nama}' berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $item = Ekstrakurikuler::findOrFail($id);
        $nama = $item->nama;

        if ($item->foto && !str_starts_with($item->foto, 'images/') && Storage::disk('public')->exists($item->foto)) {
            Storage::disk('public')->delete($item->foto);
        }

        $item->delete();

        LogAktivitas::catat($user, 'hapus_ekskul', 'Ekstrakurikuler', "Menghapus ekstrakurikuler ID #{$id}: {$nama}");

        return back()->with('sukses', "Ekstrakurikuler '{$nama}' berhasil dihapus.");
    }
}

