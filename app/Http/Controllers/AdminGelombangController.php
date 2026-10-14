<?php

namespace App\Http\Controllers;

use App\Models\Gelombang;
use App\Models\LogAktivitas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGelombangController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $gelombangs = Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)
            ->withCount('calonSiswa')
            ->orderBy('tanggal_mulai')
            ->get();

        return view('pages.admin.gelombang.index', compact('gelombangs', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $validated = $request->validate([
            'nama_gelombang' => ['required', 'string', 'max:100'],
            'kode_gelombang' => ['nullable', 'string', 'max:10'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'is_bidikmisi' => ['nullable', 'boolean'],
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai gelombang harus sama atau setelah tanggal mulai.',
        ]);

        $validated['tahun_ajaran_id'] = $tahunAjaran->id;
        $validated['is_bidikmisi'] = $request->boolean('is_bidikmisi');
        $validated['is_aktif'] = true;

        $gelombang = Gelombang::create($validated);

        LogAktivitas::catat($user, 'tambah_gelombang', 'Gelombang PPDB', "Menambahkan gelombang baru: {$gelombang->nama_gelombang}");

        return back()->with('sukses', "Gelombang {$gelombang->nama_gelombang} berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $gelombang = Gelombang::findOrFail($id);

        $validated = $request->validate([
            'nama_gelombang' => ['required', 'string', 'max:100'],
            'kode_gelombang' => ['nullable', 'string', 'max:10'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'is_bidikmisi' => ['nullable', 'boolean'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['is_bidikmisi'] = $request->boolean('is_bidikmisi');
        $validated['is_aktif'] = $request->boolean('is_aktif');

        $gelombang->update($validated);

        LogAktivitas::catat($user, 'ubah_gelombang', 'Gelombang PPDB', "Memperbarui gelombang: {$gelombang->nama_gelombang}");

        return back()->with('sukses', "Data gelombang {$gelombang->nama_gelombang} berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $gelombang = Gelombang::withCount('calonSiswa')->findOrFail($id);

        // FR-45: Delete hanya jika belum ada pendaftar terkait
        if ($gelombang->calon_siswa_count > 0) {
            return back()->with('error', "Gelombang ini telah memiliki {$gelombang->calon_siswa_count} pendaftar sehingga tidak boleh dihapus. Anda dapat menonaktifkan statusnya agar tidak lagi menerima pendaftar.");
        }

        $nama = $gelombang->nama_gelombang;
        $gelombang->delete();

        LogAktivitas::catat($user, 'hapus_gelombang', 'Gelombang PPDB', "Menghapus gelombang: {$nama}");

        return back()->with('sukses', "Gelombang {$nama} berhasil dihapus.");
    }
}
