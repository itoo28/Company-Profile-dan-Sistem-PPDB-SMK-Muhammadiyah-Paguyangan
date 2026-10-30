<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminTahunAjaranController extends Controller
{
    /**
     * Tampilkan daftar seluruh tahun ajaran dan status aktif/arsip
     */
    public function index()
    {
        $tahunAjarans = TahunAjaran::withCount(['calonSiswa', 'gelombang'])
            ->orderByDesc('id')
            ->get();

        $tahunAktif = TahunAjaran::aktif() ?? $tahunAjarans->first();

        return view('pages.admin.tahun-ajaran.index', compact('tahunAjarans', 'tahunAktif'));
    }

    /**
     * Tambah Tahun Ajaran Baru (Super Admin)
     */
    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:tahun_ajarans,nama', 'regex:/^[0-9]{4}\/[0-9]{4}$/'],
            'is_aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama tahun ajaran wajib diisi.',
            'nama.unique' => 'Tahun ajaran ini sudah terdaftar di sistem.',
            'nama.regex' => 'Format tahun ajaran harus berupa format TAHUN/TAHUN (contoh: 2027/2028).',
        ]);

        $jadikanAktif = $request->boolean('is_aktif');

        if ($jadikanAktif) {
            TahunAjaran::query()->update(['is_aktif' => false]);
            $validated['is_aktif'] = true;
        } else {
            $validated['is_aktif'] = TahunAjaran::count() === 0;
        }

        $tahun = TahunAjaran::create($validated);

        if ($tahun->is_aktif) {
            $pengaturan = PengaturanSistem::instance();
            if ($pengaturan) {
                $pengaturan->tahun_ajaran_id = $tahun->id;
                $pengaturan->nomor_urut_terakhir = 0;
                $pengaturan->save();
            }
        }

        LogAktivitas::catat(
            $user, 
            'tambah_tahun_ajaran', 
            'Tahun Ajaran', 
            "Menambahkan tahun ajaran baru: {$tahun->nama}" . ($tahun->is_aktif ? ' (Langsung dijadikan aktif)' : '')
        );

        return back()->with('sukses', "Tahun ajaran {$tahun->nama} berhasil ditambahkan" . ($tahun->is_aktif ? ' dan langsung aktif sebagai periode berjalan.' : '.'));
    }

    /**
     * Aktifkan Tahun Ajaran (Switch Active Year)
     */
    public function setAktif(int $id)
    {
        $user = Auth::guard('web')->user();
        $tahun = TahunAjaran::setAktif($id);

        LogAktivitas::catat(
            $user, 
            'aktifkan_tahun_ajaran', 
            'Tahun Ajaran', 
            "Mengaktifkan tahun ajaran: {$tahun->nama}. Periode PPDB tahun sebelumnya beralih menjadi arsip."
        );

        return back()->with('sukses', "Tahun ajaran {$tahun->nama} kini aktif sebagai periode PPDB berjalan. Data pendaftar periode sebelumnya tersimpan aman sebagai arsip.");
    }

    /**
     * Perbarui nama tahun ajaran
     */
    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $tahun = TahunAjaran::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50', Rule::unique('tahun_ajarans', 'nama')->ignore($tahun->id), 'regex:/^[0-9]{4}\/[0-9]{4}$/'],
        ], [
            'nama.required' => 'Nama tahun ajaran wajib diisi.',
            'nama.unique' => 'Tahun ajaran ini sudah digunakan.',
            'nama.regex' => 'Format tahun ajaran harus berupa format TAHUN/TAHUN (contoh: 2027/2028).',
        ]);

        $tahun->update($validated);

        LogAktivitas::catat($user, 'ubah_tahun_ajaran', 'Tahun Ajaran', "Memperbarui nama tahun ajaran ID {$tahun->id} menjadi {$tahun->nama}");

        return back()->with('sukses', "Tahun ajaran berhasil diperbarui menjadi {$tahun->nama}.");
    }

    /**
     * Hapus tahun ajaran (Hanya jika belum memiliki data pendaftar/gelombang dan bukan aktif)
     */
    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $tahun = TahunAjaran::withCount(['calonSiswa', 'gelombang'])->findOrFail($id);

        if ($tahun->is_aktif) {
            return back()->with('error', 'Tahun ajaran yang sedang Aktif tidak dapat dihapus. Silakan aktifkan tahun ajaran lain terlebih dahulu.');
        }

        if ($tahun->calon_siswa_count > 0) {
            return back()->with('error', "Tahun ajaran {$tahun->nama} tidak dapat dihapus karena memiliki {$tahun->calon_siswa_count} data siswa pendaftar. Data ini tersimpan aman sebagai arsip.");
        }

        if ($tahun->gelombang_count > 0) {
            return back()->with('error', "Tahun ajaran {$tahun->nama} tidak dapat dihapus karena memiliki {$tahun->gelombang_count} gelombang pendaftaran terkait.");
        }

        $nama = $tahun->nama;
        $tahun->delete();

        LogAktivitas::catat($user, 'hapus_tahun_ajaran', 'Tahun Ajaran', "Menghapus tahun ajaran kosong: {$nama}");

        return back()->with('sukses', "Tahun ajaran {$nama} berhasil dihapus.");
    }
}
