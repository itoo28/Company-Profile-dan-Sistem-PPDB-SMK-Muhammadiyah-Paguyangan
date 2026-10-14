<?php

namespace App\Http\Controllers;

use App\Models\BerkasSiswa;
use App\Models\JenisBerkas;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminJenisBerkasController extends Controller
{
    public function index()
    {
        $jenisBerkasList = JenisBerkas::orderBy('urutan')->get();

        return view('pages.admin.jenis-berkas.index', compact('jenisBerkasList'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'aturan_wajib' => ['required', 'in:semua,khusus_prestasi,opsional'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        $validated['is_wajib'] = in_array($validated['aturan_wajib'], ['semua', 'khusus_prestasi']);
        $validated['is_aktif'] = true;

        $berkas = JenisBerkas::create($validated);

        LogAktivitas::catat($user, 'tambah_jenis_berkas', 'Master Berkas', "Menambahkan persyaratan berkas baru: {$berkas->nama}");

        return back()->with('sukses', "Jenis berkas {$berkas->nama} berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $berkas = JenisBerkas::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'aturan_wajib' => ['required', 'in:semua,khusus_prestasi,opsional'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['is_wajib'] = in_array($validated['aturan_wajib'], ['semua', 'khusus_prestasi']);
        $validated['is_aktif'] = $request->boolean('is_aktif');

        $berkas->update($validated);

        LogAktivitas::catat($user, 'ubah_jenis_berkas', 'Master Berkas', "Memperbarui persyaratan berkas: {$berkas->nama}");

        return back()->with('sukses', "Data jenis berkas {$berkas->nama} berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $berkas = JenisBerkas::findOrFail($id);

        $fileUploadedCount = BerkasSiswa::where('jenis_berkas_id', $berkas->id)->count();
        if ($fileUploadedCount > 0) {
            return back()->with('error', "Jenis berkas ini telah memiliki {$fileUploadedCount} file berkas yang diunggah oleh pendaftar sehingga tidak boleh dihapus. Anda dapat menonaktifkan statusnya.");
        }

        $nama = $berkas->nama;
        $berkas->delete();

        LogAktivitas::catat($user, 'hapus_jenis_berkas', 'Master Berkas', "Menghapus jenis berkas: {$nama}");

        return back()->with('sukses', "Jenis berkas {$nama} berhasil dihapus.");
    }
}
