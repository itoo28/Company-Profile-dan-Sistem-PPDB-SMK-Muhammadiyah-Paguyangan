<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\LogAktivitas;
use App\Models\PengaturanSistem;
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
        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam.petugas', 'pembayaran']);
        if ($tahunAjaran) {
            $query->where('tahun_ajaran_id', $tahunAjaran->id);
        }

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

        $siswa = CalonSiswa::with(['pengambilanSeragam', 'pembayaran'])->findOrFail($request->calon_siswa_id);

        // Validasi Pembayaran Lunas
        if (!$siswa->isPembayaranLunas()) {
            return back()->with('error', 'Pencatatan seragam ditolak! Siswa ini belum menyelesaikan pembayaran pendaftaran/seragam (status pembayaran belum LUNAS).');
        }

        // Validasi Clearance di Sisi Server (BR-05): Tombol terkunci kecuali seluruh berkas wajib disetujui
        if (!$siswa->isSeragamClearanceApproved()) {
            return back()->with('error', 'Pencatatan seragam ditolak! Siswa ini belum memenuhi syarat clearance karena masih ada berkas wajib yang belum berstatus Disetujui.');
        }

        // Validasi Tunggal (BR-06): Hanya boleh sekali per siswa, dijaga dengan transaksi & unique index
        if ($siswa->pengambilanSeragam) {
            return back()->with('error', 'Siswa ini sudah tercatat mengambil seragam pada ' . $siswa->pengambilanSeragam->waktu_ambil->translatedFormat('d F Y, H:i') . ' oleh ' . ($siswa->pengambilanSeragam->petugas->name ?? 'Petugas') . '. Pengambilan tidak dapat diduplikasi.');
        }

        DB::transaction(function () use ($siswa, $user, $request) {
            PengambilanSeragam::create([
                'calon_siswa_id' => $siswa->id,
                'petugas_id' => $user->id,
                'waktu_ambil' => Carbon::now(),
                'catatan' => $request->input('catatan'),
            ]);

            // Alokasi benefit promo 100 siswa pertama:
            // Diberikan kepada 100 siswa pertama yang menyelesaikan registrasi sampai dapat seragam
            $pengaturan = PengaturanSistem::where('id', 1)->lockForUpdate()->first() ?? PengaturanSistem::instance();
            if ($pengaturan->is_promo_aktif) {
                $klaimCount = CalonSiswa::where('tahun_ajaran_id', $siswa->tahun_ajaran_id)
                    ->where('status_benefit', 'klaim_aktif')
                    ->where('id', '!=', $siswa->id)
                    ->count();

                if ($klaimCount < $pengaturan->kuota_promo_benefit) {
                    $siswa->update(['status_benefit' => 'klaim_aktif']);
                } else {
                    $siswa->update(['status_benefit' => 'tidak_dapat']);
                }
            } else {
                $siswa->update(['status_benefit' => 'tidak_dapat']);
            }

            $namaSiswa = $siswa->nama_lengkap;
            LogAktivitas::catat(
                $user,
                'ambil_seragam',
                'Loket Seragam',
                "Pencatatan seragam untuk siswa {$namaSiswa} ({$siswa->nomor_pendaftaran})."
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
        $siswa = $pengambilan->calonSiswa;
        $siswaNama = $siswa->nama_lengkap ?? 'Siswa';
        $nomor = $siswa->nomor_pendaftaran ?? '-';

        DB::transaction(function () use ($pengambilan, $siswa, $user, $siswaNama, $nomor) {
            if ($siswa && $siswa->status_benefit === 'klaim_aktif') {
                $pengaturan = PengaturanSistem::where('id', 1)->first() ?? PengaturanSistem::instance();
                if ($pengaturan && $pengaturan->is_promo_aktif) {
                    $siswa->update(['status_benefit' => 'kandidat_100_pertama']);
                } else {
                    $siswa->update(['status_benefit' => 'tidak_dapat']);
                }
            }

            $pengambilan->delete();

            LogAktivitas::catat(
                $user,
                'batal_ambil_seragam',
                'Loket Seragam',
                "Membatalkan pencatatan pengambilan seragam untuk siswa {$siswaNama} ({$nomor})."
            );
        });

        return back()->with('sukses', "Pencatatan pengambilan seragam untuk {$siswaNama} berhasil dibatalkan.");
    }
}
