<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminBeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with(['pembuat', 'pengubah'])->latest('tanggal_terbit');

        if ($cari = trim($request->input('q', ''))) {
            $query->where(function ($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                  ->orWhere('isi', 'like', "%{$cari}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $beritaList = $query->paginate(10)->withQueryString();

        return view('pages.admin.berita.index', compact('beritaList', 'cari'));
    }

    public function create()
    {
        return view('pages.admin.berita.create');
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'ringkasan' => ['nullable', 'string', 'max:300'],
            'isi' => ['required', 'string'],
            'tanggal_terbit' => ['required', 'date'],
            'status' => ['required', 'in:draf,terbit'],
            'gambar_sampul' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $slug = Str::slug($validated['judul']);
        $originalSlug = $slug;
        $counter = 1;
        while (Berita::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        $validated['judul'] = strip_tags($validated['judul']);
        if (!empty($validated['ringkasan'])) {
            $validated['ringkasan'] = strip_tags($validated['ringkasan']);
        }

        // Sanitasi teks isi berita dari potensi Stored XSS (NFR-05)
        $validated['isi'] = $this->sanitizeHtml($validated['isi']);

        if ($request->hasFile('gambar_sampul')) {
            $validated['gambar_sampul'] = $request->file('gambar_sampul')->store('berita', 'public');
        }

        $validated['penulis'] = $user->name;
        $validated['dibuat_oleh'] = $user->id;

        $berita = Berita::create($validated);

        LogAktivitas::catat($user, 'tambah_berita', 'Warta & Berita', "Menambahkan berita: {$berita->judul} [Status: {$berita->status}]");

        return redirect()->route('admin.berita.index')->with('sukses', "Berita \"{$berita->judul}\" berhasil ditambahkan.");
    }

    public function edit(int $id)
    {
        $berita = Berita::findOrFail($id);

        return view('pages.admin.berita.edit', compact('berita'));
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $berita = Berita::findOrFail($id);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'ringkasan' => ['nullable', 'string', 'max:300'],
            'isi' => ['required', 'string'],
            'tanggal_terbit' => ['required', 'date'],
            'status' => ['required', 'in:draf,terbit'],
            'gambar_sampul' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($validated['judul'] !== $berita->judul) {
            $slug = Str::slug($validated['judul']);
            $originalSlug = $slug;
            $counter = 1;
            while (Berita::where('slug', $slug)->where('id', '!=', $berita->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['judul'] = strip_tags($validated['judul']);
        if (!empty($validated['ringkasan'])) {
            $validated['ringkasan'] = strip_tags($validated['ringkasan']);
        }

        // Sanitasi teks isi berita dari potensi Stored XSS (NFR-05)
        $validated['isi'] = $this->sanitizeHtml($validated['isi']);

        if ($request->hasFile('gambar_sampul')) {
            if ($berita->gambar_sampul && Storage::disk('public')->exists($berita->gambar_sampul)) {
                Storage::disk('public')->delete($berita->gambar_sampul);
            }
            $validated['gambar_sampul'] = $request->file('gambar_sampul')->store('berita', 'public');
        }

        $validated['diubah_oleh'] = $user->id;

        $berita->update($validated);

        LogAktivitas::catat($user, 'ubah_berita', 'Warta & Berita', "Memperbarui berita: {$berita->judul}");

        return redirect()->route('admin.berita.index')->with('sukses', "Berita \"{$berita->judul}\" berhasil diperbarui.");
    }

    /**
     * Sanitasi konten HTML berita dari potensi serangan Stored XSS (NFR-05)
     */
    protected function sanitizeHtml(string $html): string
    {
        // 1. Izinkan hanya tag HTML dasar yang aman untuk artikel berita
        $clean = strip_tags($html, '<p><br><strong><em><b><i><u><ul><ol><li><a><blockquote><h2><h3><h4>');

        // 2. Bersihkan event handler inline (seperti onload, onerror, onclick, onmouseover, dsb.)
        $clean = preg_replace('/(\son[a-zA-Z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+))/i', '', $clean);

        // 3. Netralkan protokol berbahaya pada atribut href (seperti javascript:, vbscript:, data:)
        $clean = preg_replace('/href\s*=\s*(["\'])\s*(?:java\s*script|vb\s*script|data):.*?\1/i', 'href="#"', $clean);

        return $clean;
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $berita = Berita::findOrFail($id);

        $judul = $berita->judul;

        if ($berita->gambar_sampul && Storage::disk('public')->exists($berita->gambar_sampul)) {
            Storage::disk('public')->delete($berita->gambar_sampul);
        }

        $berita->delete();

        LogAktivitas::catat($user, 'hapus_berita', 'Warta & Berita', "Menghapus berita: {$judul}");

        return back()->with('sukses', "Berita \"{$judul}\" berhasil dihapus.");
    }
}
