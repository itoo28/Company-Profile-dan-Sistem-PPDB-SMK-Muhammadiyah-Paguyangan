<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminJurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::withCount('calonSiswa')->orderBy('urutan')->get();

        return view('pages.admin.jurusan.index', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:jurusans,kode'],
            'nama_jurusan' => ['required', 'string', 'max:150'],
            'bidang_keahlian' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'prospek_kerja' => ['nullable', 'string', 'max:1000'],
            'kuota' => ['required', 'integer', 'min:1', 'max:500'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        $validated['is_aktif'] = true;

        $jurusan = Jurusan::create($validated);

        LogAktivitas::catat($user, 'tambah_jurusan', 'Konsentrasi Keahlian', "Menambahkan jurusan baru: {$jurusan->nama_jurusan} ({$jurusan->kode})");

        return back()->with('sukses', "Jurusan {$jurusan->nama_jurusan} berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $jurusan = Jurusan::findOrFail($id);

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', Rule::unique('jurusans', 'kode')->ignore($jurusan->id)],
            'nama_jurusan' => ['required', 'string', 'max:150'],
            'bidang_keahlian' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'prospek_kerja' => ['nullable', 'string', 'max:1000'],
            'kuota' => ['required', 'integer', 'min:1', 'max:500'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif');

        $jurusan->update($validated);

        LogAktivitas::catat($user, 'ubah_jurusan', 'Konsentrasi Keahlian', "Memperbarui data jurusan: {$jurusan->nama_jurusan}");

        return back()->with('sukses', "Data jurusan {$jurusan->nama_jurusan} berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $jurusan = Jurusan::withCount('calonSiswa')->findOrFail($id);

        // FR-46: Delete hanya jika belum ada pendaftar terkait
        if ($jurusan->calon_siswa_count > 0) {
            return back()->with('error', "Jurusan ini telah dipilih oleh {$jurusan->calon_siswa_count} calon siswa pendaftar sehingga tidak boleh dihapus demi integritas data relasional. Anda dapat menonaktifkan statusnya.");
        }

        $nama = $jurusan->nama_jurusan;
        $jurusan->delete();

        LogAktivitas::catat($user, 'hapus_jurusan', 'Konsentrasi Keahlian', "Menghapus jurusan: {$nama}");

        return back()->with('sukses', "Jurusan {$nama} berhasil dihapus.");
    }
}
