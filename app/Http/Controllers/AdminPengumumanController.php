<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::latest('tanggal')->paginate(15);

        return view('pages.admin.pengumuman.index', compact('pengumumans'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:terbit,draf'],
            'isi' => ['required', 'string'],
            'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,png,jpeg', 'max:5120'],
        ]);

        $validated['slug'] = Str::slug($validated['judul']) . '-' . Str::random(5);

        if ($request->hasFile('file_lampiran')) {
            $validated['file_lampiran'] = $request->file('file_lampiran')->store('lampiran_pengumuman', 'public');
        }

        $pengumuman = Pengumuman::create($validated);

        LogAktivitas::catat($user, 'tambah_pengumuman', 'Pengumuman Sekolah', "Menambahkan pengumuman: {$pengumuman->judul} (Status: {$pengumuman->status})");

        return back()->with('sukses', "Pengumuman '{$pengumuman->judul}' berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $pengumuman = Pengumuman::findOrFail($id);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:terbit,draf'],
            'isi' => ['required', 'string'],
            'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,png,jpeg', 'max:5120'],
        ]);

        if ($request->hasFile('file_lampiran')) {
            if ($pengumuman->file_lampiran && Storage::disk('public')->exists($pengumuman->file_lampiran)) {
                Storage::disk('public')->delete($pengumuman->file_lampiran);
            }
            $validated['file_lampiran'] = $request->file('file_lampiran')->store('lampiran_pengumuman', 'public');
        }

        $pengumuman->update($validated);

        LogAktivitas::catat($user, 'ubah_pengumuman', 'Pengumuman Sekolah', "Memperbarui pengumuman ID #{$pengumuman->id}: {$pengumuman->judul}");

        return back()->with('sukses', "Pengumuman '{$pengumuman->judul}' berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $pengumuman = Pengumuman::findOrFail($id);
        $judul = $pengumuman->judul;

        if ($pengumuman->file_lampiran && Storage::disk('public')->exists($pengumuman->file_lampiran)) {
            Storage::disk('public')->delete($pengumuman->file_lampiran);
        }

        $pengumuman->delete();

        LogAktivitas::catat($user, 'hapus_pengumuman', 'Pengumuman Sekolah', "Menghapus pengumuman ID #{$id}: {$judul}");

        return back()->with('sukses', "Pengumuman '{$judul}' berhasil dihapus.");
    }
}

