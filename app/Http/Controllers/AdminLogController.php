<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'q',
            'objek',
            'kategori_aksi',
            'peran',
            'tanggal_mulai',
            'tanggal_selesai',
        ]);

        $cari = trim($request->input('q', ''));

        // Query berantai menggunakan filter scope
        $query = LogAktivitas::filter($filters)->latest('id');
        $logs = $query->paginate(20)->withQueryString();

        // Metrik Ringkas KPI Audit
        $totalLogs = LogAktivitas::count();
        $todayLogs = LogAktivitas::whereDate('created_at', today())->count();
        $ppdbLogs = LogAktivitas::whereIn('objek', [
            'Calon Siswa',
            'Pembayaran PPDB',
            'Seragam PPDB',
            'Laporan PPDB',
        ])->count();
        $securityAlerts = LogAktivitas::whereIn('jenis_aksi', [
            'login_gagal',
            'login_ditolak',
        ])->count();

        // Opsi Dropdown Filter
        $daftarModul = LogAktivitas::distinct()
            ->whereNotNull('objek')
            ->orderBy('objek')
            ->pluck('objek');

        $kategoriAksiList = [
            'autentikasi' => 'Autentikasi & Akses Akun',
            'tambah' => 'Penambahan Data / Buat Baru',
            'ubah' => 'Perubahan, Koreksi & Reset',
            'hapus' => 'Penghapusan Data Permanen',
            'verifikasi' => 'Verifikasi Dokumen & Loket',
            'seragam' => 'Distribusi Seragam',
            'ekspor' => 'Ekspor Laporan & Data',
            'keamanan' => 'Peringatan Keamanan (Gagal Login)',
        ];

        $peranList = [
            'super_admin' => 'Super Admin',
            'admin_piket' => 'Admin Piket',
            'sistem' => 'Sistem Otomatis',
        ];

        return view('pages.admin.log.index', compact(
            'logs',
            'cari',
            'filters',
            'totalLogs',
            'todayLogs',
            'ppdbLogs',
            'securityAlerts',
            'daftarModul',
            'kategoriAksiList',
            'peranList'
        ));
    }
}
