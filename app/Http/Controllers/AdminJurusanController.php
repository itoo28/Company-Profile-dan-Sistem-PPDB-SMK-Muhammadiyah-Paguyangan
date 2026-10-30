<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\LogAktivitas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminJurusanController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $jurusans = Jurusan::withCount([
            'calonSiswa' => function ($q) use ($tahunAjaran) {
                if ($tahunAjaran) {
                    $q->where('tahun_ajaran_id', $tahunAjaran->id);
                } else {
                    $q->whereRaw('0 = 1');
                }
            },
            'calonSiswa as total_pendaftar_count',
        ])->orderBy('urutan')->get();

        return view('pages.admin.jurusan.index', compact('jurusans', 'tahunAjaran'));
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
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('jurusan', 'public');
        }

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
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($jurusan->foto && Storage::disk('public')->exists($jurusan->foto)) {
                Storage::disk('public')->delete($jurusan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('jurusan', 'public');
        }

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
        if ($jurusan->foto && Storage::disk('public')->exists($jurusan->foto)) {
            Storage::disk('public')->delete($jurusan->foto);
        }
        $jurusan->delete();

        LogAktivitas::catat($user, 'hapus_jurusan', 'Konsentrasi Keahlian', "Menghapus jurusan: {$nama}");

        return back()->with('sukses', "Jurusan {$nama} berhasil dihapus.");
    }
}
