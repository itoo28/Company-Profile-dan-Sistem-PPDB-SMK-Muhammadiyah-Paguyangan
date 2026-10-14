<?php

namespace App\Http\Controllers;

use App\Models\BerkasSiswa;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\PengambilanSeragam;
use App\Models\PengaturanSistem;
use App\Models\PesanMasuk;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard Panel Admin (FR-30 & FR-15)
     */
    public function index()
    {
        $user = Auth::guard('web')->user();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $pengaturan = PengaturanSistem::instance();

        // Query dasar pendaftar tahun ajaran aktif
        $pendaftarQuery = CalonSiswa::where('tahun_ajaran_id', $tahunAjaran->id);

        // Statistik Umum
        $totalPendaftar = (clone $pendaftarQuery)->count();
        $pendaftarOnline = (clone $pendaftarQuery)->where('jalur_registrasi', 'online')->count();
        $pendaftarOffline = (clone $pendaftarQuery)->where('jalur_registrasi', 'offline')->count();
        $pendaftarHariIni = (clone $pendaftarQuery)->whereDate('created_at', Carbon::today())->count();

        // Jalur Pendaftaran
        $jalurUmumCount = (clone $pendaftarQuery)->where('jalur_pendaftaran', 'umum')->count();
        $jalurPrestasiCount = (clone $pendaftarQuery)->where('jalur_pendaftaran', 'prestasi')->count();

        // Benefit Promo 100 Pendaftar Pertama (FR-43)
        $promoKandidatCount = (clone $pendaftarQuery)->where('status_benefit', 'kandidat_100_pertama')->count();
        $promoKlaimAktifCount = (clone $pendaftarQuery)->where('status_benefit', 'klaim_aktif')->count();
        $kuotaPromo = $pengaturan->kuota_promo_benefit ?? 100;

        // Berkas Menunggu Verifikasi
        $berkasMenungguCount = BerkasSiswa::whereHas('calonSiswa', function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })->where('status', 'menunggu')->count();

        $berkasDitolakCount = BerkasSiswa::whereHas('calonSiswa', function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })->where('status', 'ditolak')->count();

        // Pengambilan Seragam
        $totalSeragamDiambil = PengambilanSeragam::whereHas('calonSiswa', function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })->count();

        // Pesan Kontak Masuk Belum Dibaca
        $pesanBelumDibacaCount = PesanMasuk::belumDibaca()->count();

        // Rekap per Jurusan (Daya Tampung vs Terisi)
        $jurusans = Jurusan::aktif()->withCount(['calonSiswa' => function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        }])->get();

        // Rekap per Gelombang
        $gelombangs = Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->withCount('calonSiswa')->get();

        return view('pages.admin.dashboard', compact(
            'user',
            'tahunAjaran',
            'pengaturan',
            'totalPendaftar',
            'pendaftarOnline',
            'pendaftarOffline',
            'pendaftarHariIni',
            'jalurUmumCount',
            'jalurPrestasiCount',
            'promoKandidatCount',
            'promoKlaimAktifCount',
            'kuotaPromo',
            'berkasMenungguCount',
            'berkasDitolakCount',
            'totalSeragamDiambil',
            'pesanBelumDibacaCount',
            'jurusans',
            'gelombangs'
        ));
    }
}
