<?php

namespace App\Http\Controllers;

use App\Models\Gelombang;
use App\Models\LogAktivitas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGelombangController extends Controller
{
    public function index(Request $request)
    {
        $semuaTahunAjaran = TahunAjaran::orderByDesc('nama')->get();

        $tahunAjaranId = $request->query('tahun_ajaran_id');
        $tahunAjaran = null;
        if ($tahunAjaranId) {
            $tahunAjaran = $semuaTahunAjaran->firstWhere('id', (int) $tahunAjaranId);
        }

        if (! $tahunAjaran) {
            $tahunAjaran = TahunAjaran::aktif() ?? $semuaTahunAjaran->first();
        }

        $gelombangs = Gelombang::when($tahunAjaran, function ($q) use ($tahunAjaran) {
                return $q->where('tahun_ajaran_id', $tahunAjaran->id);
            })
            ->with(['tahunAjaran'])
            ->withCount('calonSiswa')
            ->orderBy('tanggal_mulai')
            ->get();

        return view('pages.admin.gelombang.index', compact('gelombangs', 'tahunAjaran', 'semuaTahunAjaran'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();
        $tahunAjaranAktif = TahunAjaran::aktif() ?? TahunAjaran::first();

        $validated = $request->validate([
            'tahun_ajaran_id' => ['nullable', 'exists:tahun_ajarans,id'],
            'nama_gelombang' => ['required', 'string', 'max:100'],
            'kode_gelombang' => ['nullable', 'string', 'max:10'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'is_bidikmisi' => ['nullable', 'boolean'],
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai gelombang harus sama atau setelah tanggal mulai.',
        ]);

        $targetTahunAjaranId = $request->input('tahun_ajaran_id') ?: ($tahunAjaranAktif ? $tahunAjaranAktif->id : null);
        if (!$targetTahunAjaranId) {
            return back()->withInput()->withErrors([
                'tahun_ajaran_id' => 'Tahun ajaran wajib dipilih atau diaktifkan terlebih dahulu.',
            ]);
        }
        $validated['tahun_ajaran_id'] = $targetTahunAjaranId;
        $validated['is_bidikmisi'] = $request->boolean('is_bidikmisi');
        $validated['is_aktif'] = true;

        // FR-45: Validasi rentang tanggal tidak saling tumpang tindih (overlap) antar gelombang aktif dalam tahun ajaran yang sama
        $overlapping = Gelombang::where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
            ->where('is_aktif', true)
            ->where(function ($q) use ($validated) {
                $q->where('tanggal_mulai', '<=', $validated['tanggal_selesai'])
                  ->where('tanggal_selesai', '>=', $validated['tanggal_mulai']);
            })
            ->first();

        if ($overlapping) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => "Jadwal gelombang bertabrakan (overlap) dengan gelombang {$overlapping->nama_gelombang} ({$overlapping->tanggal_mulai->format('d/m/Y')} - {$overlapping->tanggal_selesai->format('d/m/Y')}) pada tahun ajaran yang sama.",
            ]);
        }

        $gelombang = Gelombang::create($validated);

        LogAktivitas::catat($user, 'tambah_gelombang', 'Gelombang PPDB', "Menambahkan gelombang baru: {$gelombang->nama_gelombang}");

        return back()->with('sukses', "Gelombang {$gelombang->nama_gelombang} berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $gelombang = Gelombang::findOrFail($id);

        $validated = $request->validate([
            'tahun_ajaran_id' => ['nullable', 'exists:tahun_ajarans,id'],
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

        $targetTahunAjaranId = !empty($validated['tahun_ajaran_id']) ? $validated['tahun_ajaran_id'] : $gelombang->tahun_ajaran_id;

        // FR-45: Validasi rentang tanggal tidak saling tumpang tindih (overlap) saat update jika gelombang aktif
        if ($validated['is_aktif'] && $targetTahunAjaranId) {
            $overlapping = Gelombang::where('tahun_ajaran_id', $targetTahunAjaranId)
                ->where('id', '!=', $gelombang->id)
                ->where('is_aktif', true)
                ->where(function ($q) use ($validated) {
                    $q->where('tanggal_mulai', '<=', $validated['tanggal_selesai'])
                      ->where('tanggal_selesai', '>=', $validated['tanggal_mulai']);
                })
                ->first();

            if ($overlapping) {
                return back()->withInput()->withErrors([
                    'tanggal_mulai' => "Jadwal gelombang bertabrakan (overlap) dengan gelombang {$overlapping->nama_gelombang} ({$overlapping->tanggal_mulai->format('d/m/Y')} - {$overlapping->tanggal_selesai->format('d/m/Y')}) pada tahun ajaran yang sama.",
                ]);
            }
        }

        if (empty($validated['tahun_ajaran_id'])) {
            unset($validated['tahun_ajaran_id']);
        }

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
