<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\LogAktivitas;
use App\Models\PengaturanSistem;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminEksporController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $gelombangs = Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->get();

        return view('pages.admin.ekspor.index', compact('tahunAjaran', 'gelombangs'));
    }

    public function eksporCsv(Request $request): StreamedResponse
    {
        $user = Auth::guard('web')->user();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam'])
            ->where('tahun_ajaran_id', $tahunAjaran->id);

        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        if ($jalur = $request->input('jalur_pendaftaran')) {
            $query->where('jalur_pendaftaran', $jalur);
        }

        $pendaftarList = $query->orderBy('nomor_urut')->get();

        LogAktivitas::catat($user, 'ekspor_data', 'Laporan PPDB', "Ekspor data CSV calon siswa PPDB ({$pendaftarList->count()} data).");

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="data-pendaftar-ppdb-smk-muhammadiyah-paguyangan-' . date('Ymd-His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($pendaftarList) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk Excel Indonesia
            fputs($file, "\xEF\xBB\xBF");

            // Header Kolom CSV
            fputcsv($file, [
                'No Urut',
                'Nomor Pendaftaran',
                'Nama Lengkap',
                'NIK',
                'NISN',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Jenis Kelamin',
                'Alamat',
                'Asal Sekolah',
                'Nama Orang Tua / Wali',
                'No HP Orang Tua / Wali',
                'Konsentrasi Keahlian (Jurusan)',
                'Jalur Pendaftaran',
                'Gelombang',
                'Registrasi',
                'Status Benefit SPP 3 Bulan',
                'Status Berkas Ringkas',
                'Status Pengambilan Seragam',
                'Tanggal Mendaftar',
            ]);

            foreach ($pendaftarList as $index => $item) {
                fputcsv($file, [
                    $item->nomor_urut,
                    $item->nomor_pendaftaran,
                    $item->nama_lengkap,
                    "'" . $item->nik, // Kutip agar angka 16 digit tidak terpotong di Excel
                    "'" . $item->nisn,
                    $item->tempat_lahir,
                    $item->tanggal_lahir->format('d/m/Y'),
                    $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    $item->alamat,
                    $item->asal_sekolah,
                    $item->nama_orang_tua,
                    "'" . $item->no_hp_orang_tua,
                    $item->jurusan->nama_jurusan ?? '-',
                    ucfirst($item->jalur_pendaftaran),
                    $item->gelombang->nama_gelombang ?? '-',
                    ucfirst($item->jalur_registrasi),
                    strtoupper(str_replace('_', ' ', $item->status_benefit)),
                    strtoupper(str_replace('_', ' ', $item->status_berkas_ringkas)),
                    $item->pengambilanSeragam ? 'SUDAH DIAMBIL (' . $item->pengambilanSeragam->waktu_ambil->format('d/m/Y') . ')' : 'BELUM DIAMBIL',
                    $item->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function eksporCetak(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $pengaturan = PengaturanSistem::instance();

        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam'])
            ->where('tahun_ajaran_id', $tahunAjaran->id);

        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        $pendaftarList = $query->orderBy('nomor_urut')->get();

        return view('pages.admin.ekspor.cetak', compact('pendaftarList', 'tahunAjaran', 'pengaturan'));
    }
}
