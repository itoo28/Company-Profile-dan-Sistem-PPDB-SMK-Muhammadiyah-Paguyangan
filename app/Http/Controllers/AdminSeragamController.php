<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\LogAktivitas;
use App\Models\PengambilanSeragam;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSeragamController extends Controller
{
    /**
     * Halaman Loket Seragam (FR-14)
     */
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam.petugas'])
            ->where('tahun_ajaran_id', $tahunAjaran->id);

        $cari = trim($request->input('q', ''));
        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$cari}%");
            });
        }

        // Filter status pengambilan
        if ($statusSeragam = $request->input('status')) {
            if ($statusSeragam === 'sudah') {
                $query->whereHas('pengambilanSeragam');
            } elseif ($statusSeragam === 'belum') {
                $query->whereDoesntHave('pengambilanSeragam');
            }
        }

        $siswaList = $query->latest('id')->paginate(15)->withQueryString();

        return view('pages.admin.seragam.index', compact('siswaList', 'cari'));
    }

    /**
     * Pencatatan Pengambilan Seragam (FR-14, FR-27, BR-05, BR-06)
     */
    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $request->validate([
            'calon_siswa_id' => ['required', 'exists:calon_siswas,id'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $siswa = CalonSiswa::with('pengambilanSeragam')->findOrFail($request->calon_siswa_id);

        // Validasi Clearance di Sisi Server (BR-05): Tombol terkunci kecuali seluruh berkas wajib disetujui
        if (!$siswa->isSeragamClearanceApproved()) {
            return back()->with('error', 'Pencatatan seragam ditolak! Siswa ini belum memenuhi syarat clearance karena masih ada berkas wajib yang belum berstatus Disetujui (BR-05).');
        }

        // Validasi Tunggal (BR-06): Hanya boleh sekali per siswa, dijaga dengan transaksi & unique index
        if ($siswa->pengambilanSeragam) {
            return back()->with('error', 'Siswa ini sudah tercatat mengambil seragam pada ' . $siswa->pengambilanSeragam->waktu_ambil->translatedFormat('d F Y, H:i') . ' oleh ' . ($siswa->pengambilanSeragam->petugas->name ?? 'Petugas') . '. Pengambilan tidak dapat diduplikasi (BR-06).');
        }

        DB::transaction(function () use ($siswa, $user, $request) {
            PengambilanSeragam::create([
                'calon_siswa_id' => $siswa->id,
                'petugas_id' => $user->id,
                'waktu_ambil' => Carbon::now(),
                'catatan' => $request->input('catatan'),
            ]);

            LogAktivitas::catat(
                $user,
                'ambil_seragam',
                'Loket Seragam',
                "Pencatatan seragam untuk siswa {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran})."
            );
        });

        return back()->with('sukses', "Pengambilan seragam untuk {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran}) berhasil dicatat.");
    }

    /**
     * Batalkan Pencatatan Pengambilan Seragam (Super Admin Only)
     */
    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang berhak membatalkan pencatatan seragam.');
        }

        $pengambilan = PengambilanSeragam::with('calonSiswa')->findOrFail($id);
        $siswaNama = $pengambilan->calonSiswa->nama_lengkap ?? 'Siswa';
        $nomor = $pengambilan->calonSiswa->nomor_pendaftaran ?? '-';

        $pengambilan->delete();

        LogAktivitas::catat(
            $user,
            'batal_ambil_seragam',
            'Loket Seragam',
            "Membatalkan pencatatan pengambilan seragam untuk siswa {$siswaNama} ({$nomor})."
        );

        return back()->with('sukses', "Pencatatan pengambilan seragam untuk {$siswaNama} berhasil dibatalkan.");
    }
}
