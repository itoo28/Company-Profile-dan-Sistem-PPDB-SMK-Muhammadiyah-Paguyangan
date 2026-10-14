<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPrestasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestasi::latest('tanggal_perolehan');

        if ($cari = trim($request->input('q', ''))) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_prestasi', 'like', "%{$cari}%")
                  ->orWhere('nama_peraih', 'like', "%{$cari}%")
                  ->orWhere('deskripsi', 'like', "%{$cari}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $prestasiList = $query->paginate(10)->withQueryString();

        return view('pages.admin.prestasi.index', compact('prestasiList', 'cari'));
    }

    public function create()
    {
        return view('pages.admin.prestasi.create');
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'nama_prestasi' => ['required', 'string', 'max:200'],
            'nama_peraih' => ['required', 'string', 'max:150'],
            'tingkat' => ['required', 'string', 'max:100'],
            'peringkat' => ['required', 'string', 'max:100'],
            'tanggal_perolehan' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'status' => ['required', 'in:draf,terbit'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $slug = Str::slug($validated['nama_prestasi']);
        $originalSlug = $slug;
        $counter = 1;
        while (Prestasi::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('prestasi', 'public');
        }

        $validated['dibuat_oleh'] = $user->id;

        $prestasi = Prestasi::create($validated);

        LogAktivitas::catat($user, 'tambah_prestasi', 'Prestasi Siswa', "Menambahkan prestasi: {$prestasi->nama_prestasi} [Peraih: {$prestasi->nama_peraih}]");

        return redirect()->route('admin.prestasi.index')->with('sukses', "Prestasi \"{$prestasi->nama_prestasi}\" berhasil ditambahkan.");
    }

    public function edit(int $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        return view('pages.admin.prestasi.edit', compact('prestasi'));
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $prestasi = Prestasi::findOrFail($id);

        $validated = $request->validate([
            'nama_prestasi' => ['required', 'string', 'max:200'],
            'nama_peraih' => ['required', 'string', 'max:150'],
            'tingkat' => ['required', 'string', 'max:100'],
            'peringkat' => ['required', 'string', 'max:100'],
            'tanggal_perolehan' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'status' => ['required', 'in:draf,terbit'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($validated['nama_prestasi'] !== $prestasi->nama_prestasi) {
            $slug = Str::slug($validated['nama_prestasi']);
            $originalSlug = $slug;
            $counter = 1;
            while (Prestasi::where('slug', $slug)->where('id', '!=', $prestasi->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('foto')) {
            if ($prestasi->foto && Storage::disk('public')->exists($prestasi->foto)) {
                Storage::disk('public')->delete($prestasi->foto);
            }
            $validated['foto'] = $request->file('foto')->store('prestasi', 'public');
        }

        $validated['diubah_oleh'] = $user->id;

        $prestasi->update($validated);

        LogAktivitas::catat($user, 'ubah_prestasi', 'Prestasi Siswa', "Memperbarui data prestasi: {$prestasi->nama_prestasi}");

        return redirect()->route('admin.prestasi.index')->with('sukses', "Prestasi \"{$prestasi->nama_prestasi}\" berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $prestasi = Prestasi::findOrFail($id);
        $nama = $prestasi->nama_prestasi;

        if ($prestasi->foto && Storage::disk('public')->exists($prestasi->foto)) {
            Storage::disk('public')->delete($prestasi->foto);
        }

        $prestasi->delete();

        LogAktivitas::catat($user, 'hapus_prestasi', 'Prestasi Siswa', "Menghapus prestasi: {$nama}");

        return back()->with('sukses', "Prestasi \"{$nama}\" berhasil dihapus.");
    }
}
