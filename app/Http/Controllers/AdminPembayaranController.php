<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\LogAktivitas;
use App\Models\Pembayaran;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminPembayaranController extends Controller
{
    /**
     * Halaman Utama Rekapitulasi & Verifikasi Pembayaran PPDB
     */
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $pengaturan = PengaturanSistem::instance();

        $query = Pembayaran::with(['calonSiswa.jurusan', 'calonSiswa.gelombang', 'verifikator'])
            ->whereHas('calonSiswa', function ($q) use ($tahunAjaran) {
                if ($tahunAjaran) {
                    $q->where('tahun_ajaran_id', $tahunAjaran->id);
                }
            });

        // Pencarian Nama / Nomor Pendaftaran / Kode Transaksi
        $cari = trim($request->input('q', ''));
        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode_transaksi', 'like', "%{$cari}%")
                  ->orWhereHas('calonSiswa', function ($sub) use ($cari) {
                      $sub->where('nama_lengkap', 'like', "%{$cari}%")
                          ->orWhere('nomor_pendaftaran', 'like', "%{$cari}%")
                          ->orWhere('nisn', 'like', "%{$cari}%");
                  });
            });
        }

        // Filter Status Pembayaran
        $status = $request->input('status');
        if ($status && in_array($status, ['menunggu_pembayaran', 'menunggu_verifikasi', 'lunas', 'ditolak'])) {
            $query->where('status', $status);
        }

        // Filter Gelombang
        $gelombangId = $request->input('gelombang_id');
        if ($gelombangId) {
            $query->whereHas('calonSiswa', fn ($q) => $q->where('gelombang_id', $gelombangId));
        }

        // Filter Metode
        $metode = $request->input('metode');
        if ($metode && in_array($metode, ['transfer', 'tunai'])) {
            $query->where('metode_pembayaran', $metode);
        }

        // Hitung Statistik Ringkasan Keuangan
        $baseStatQuery = Pembayaran::whereHas('calonSiswa', function ($q) use ($tahunAjaran) {
            if ($tahunAjaran) {
                $q->where('tahun_ajaran_id', $tahunAjaran->id);
            }
        });

        $totalPendaftar = (clone $baseStatQuery)->count();
        $totalLunas = (clone $baseStatQuery)->where('status', 'lunas')->count();
        $totalMenunggu = (clone $baseStatQuery)->where('status', 'menunggu_verifikasi')->count();
        $totalBelumBayar = (clone $baseStatQuery)->where('status', 'menunggu_pembayaran')->count();
        $totalDitolak = (clone $baseStatQuery)->where('status', 'ditolak')->count();
        $totalNominalMasuk = (clone $baseStatQuery)->where('status', 'lunas')->sum('nominal');

        $pembayaranList = $query->latest('updated_at')->paginate(15)->withQueryString();
        $gelombangs = $tahunAjaran ? Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->get() : collect();

        // Ambil daftar siswa yang belum lunas untuk modal pembayaran tunai langsung
        $siswaBelumLunas = CalonSiswa::where('tahun_ajaran_id', $tahunAjaran->id ?? 0)
            ->where(function ($q) {
                $q->whereDoesntHave('pembayaran')
                  ->orWhereHas('pembayaran', fn ($p) => $p->where('status', '!=', 'lunas'));
            })
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nomor_pendaftaran']);

        return view('pages.admin.pembayaran.index', compact(
            'pembayaranList',
            'gelombangs',
            'tahunAjaran',
            'pengaturan',
            'cari',
            'status',
            'gelombangId',
            'metode',
            'totalPendaftar',
            'totalLunas',
            'totalMenunggu',
            'totalBelumBayar',
            'totalDitolak',
            'totalNominalMasuk',
            'siswaBelumLunas'
        ));
    }

    /**
     * Verifikasi Pembayaran Transfer (Setujui / Tolak)
     */
    public function verifikasi(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $pembayaran = Pembayaran::with('calonSiswa')->findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:lunas,ditolak'],
            'catatan_admin' => [
                Rule::requiredIf($request->status === 'ditolak'),
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'status.required' => 'Status keputusan verifikasi wajib dipilih.',
            'catatan_admin.required' => 'Penolakan pembayaran wajib disertai alasan yang jelas bagi siswa.',
        ]);

        $statusLama = $pembayaran->status;
        $statusBaru = $request->input('status');

        $pembayaran->update([
            'status' => $statusBaru,
            'catatan_admin' => $request->input('catatan_admin'),
            'diverifikasi_oleh' => $user->id,
            'diverifikasi_pada' => Carbon::now(),
        ]);

        $namaSiswa = $pembayaran->calonSiswa->nama_lengkap ?? 'Siswa';
        $noDaftar = $pembayaran->calonSiswa->nomor_pendaftaran ?? '-';

        LogAktivitas::catat(
            $user,
            'verifikasi_pembayaran',
            'Pembayaran PPDB',
            "Verifikasi pembayaran siswa {$namaSiswa} ({$noDaftar}) dari [{$statusLama}] menjadi [{$statusBaru}]."
        );

        $pesan = $statusBaru === 'lunas'
            ? "Pembayaran untuk {$namaSiswa} ({$noDaftar}) berhasil diverifikasi LUNAS."
            : "Pembayaran untuk {$namaSiswa} ({$noDaftar}) telah DITOLAK dengan catatan.";

        return back()->with('sukses', $pesan);
    }

    /**
     * Catat Pembayaran Tunai Langsung di Loket Sekolah (Offline Cash)
     */
    public function bayarTunai(Request $request)
    {
        $user = Auth::guard('web')->user();
        $pengaturan = PengaturanSistem::instance();

        $request->validate([
            'calon_siswa_id' => ['required', 'exists:calon_siswas,id'],
            'tanggal_bayar' => ['required', 'date', 'before_or_equal:today'],
            'nominal' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'calon_siswa_id.required' => 'Siswa calon pembayar wajib ditentukan.',
            'tanggal_bayar.required' => 'Tanggal penerimaan tunai wajib diisi.',
            'tanggal_bayar.before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
        ]);

        $siswa = CalonSiswa::with('pembayaran')->findOrFail($request->calon_siswa_id);
        $pembayaran = $siswa->getOrCreatePembayaran();

        $nominalBayar = $request->input('nominal') ?: ($pembayaran->nominal ?: ($pengaturan->biaya_pendaftaran ?? 100000));

        $pembayaran->update([
            'metode_pembayaran' => 'tunai',
            'status' => 'lunas',
            'nominal' => $nominalBayar,
            'tanggal_bayar' => $request->input('tanggal_bayar'),
            'catatan_admin' => $request->input('catatan') ?: 'Pembayaran tunai di loket bendahara PPDB sekolah',
            'diverifikasi_oleh' => $user->id,
            'diverifikasi_pada' => Carbon::now(),
        ]);

        LogAktivitas::catat(
            $user,
            'bayar_tunai',
            'Pembayaran PPDB',
            "Pencatatan pembayaran tunai di loket sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " untuk {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran})."
        );

        return back()->with('sukses', "Pembayaran tunai sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " untuk {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran}) berhasil dicatat LUNAS.");
    }

    /**
     * Pratinjau Bukti Transfer Pembayaran secara Aman (NFR-07)
     */
    public function lihatBukti(int $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if (!$pembayaran->bukti_transfer || !Storage::disk('local')->exists($pembayaran->bukti_transfer)) {
            abort(404, 'Bukti transfer tidak ditemukan pada penyimpanan server.');
        }

        return Storage::disk('local')->response($pembayaran->bukti_transfer);
    }

    /**
     * Cetak Kuitansi Pembayaran Resmi dari Sisi Admin (PDF / Print View)
     */
    public function cetakKuitansi(int $id)
    {
        $pembayaran = Pembayaran::with(['calonSiswa.jurusan', 'calonSiswa.gelombang', 'calonSiswa.tahunAjaran', 'verifikator'])->findOrFail($id);
        $siswa = $pembayaran->calonSiswa;

        if (!$pembayaran->isLunas()) {
            return back()->with('error', 'Kuitansi pembayaran hanya dapat dicetak setelah status pembayaran diverifikasi Lunas.');
        }

        $pengaturan = PengaturanSistem::instance();

        return view('pages.siswa.kuitansi-cetak', compact('siswa', 'pembayaran', 'pengaturan'));
    }
}
