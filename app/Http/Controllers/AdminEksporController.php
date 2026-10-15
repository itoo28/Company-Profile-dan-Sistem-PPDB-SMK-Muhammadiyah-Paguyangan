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
        $gelombangs = $tahunAjaran ? Gelombang::where('tahun_ajaran_id', $tahunAjaran->id)->get() : collect();

        return view('pages.admin.ekspor.index', compact('tahunAjaran', 'gelombangs'));
    }

    public function eksporCsv(Request $request): StreamedResponse
    {
        $user = Auth::guard('web')->user();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam']);

        if ($tahunAjaran) {
            $query->where('tahun_ajaran_id', $tahunAjaran->id);
        }

        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        if ($jalur = $request->input('jalur_pendaftaran')) {
            if ($jalur === 'reguler') {
                $jalur = 'umum';
            }
            $query->where('jalur_pendaftaran', $jalur);
        }

        $pendaftarList = $query->orderBy('nomor_urut')->get();

        if ($statusBerkas = $request->input('status_berkas')) {
            $pendaftarList = $pendaftarList->filter(function ($item) use ($statusBerkas) {
                return $item->status_berkas_ringkas === $statusBerkas;
            });
        }

        if ($user) {
            LogAktivitas::catat($user, 'ekspor_data', 'Laporan PPDB', "Ekspor data CSV calon siswa PPDB ({$pendaftarList->count()} data).");
        }

        $delimiterParam = $request->input('delimiter', 'semicolon');
        $delimiter = match ($delimiterParam) {
            'comma', ',' => ',',
            'tab', '\t' => "\t",
            default => ';', // Standar Microsoft Excel regional Indonesia
        };

        $formatAngka = $request->input('format_angka', 'excel_formula');

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="data-pendaftar-ppdb-smk-muhammadiyah-paguyangan-' . date('Ymd-His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($pendaftarList, $delimiter, $formatAngka) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel membuka karakter dengan encoding UTF-8 yang benar
            fputs($file, "\xEF\xBB\xBF");

            // Header Kolom CSV
            $headers = [
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
            ];

            fputcsv($file, $headers, $delimiter, '"', "\\", "\r\n");

            foreach ($pendaftarList as $index => $item) {
                $tglLahir = $item->tanggal_lahir instanceof \Carbon\CarbonInterface
                    ? $item->tanggal_lahir->format('d/m/Y')
                    : ($item->tanggal_lahir ? date('d/m/Y', strtotime($item->tanggal_lahir)) : '-');

                $tglDaftar = $item->created_at instanceof \Carbon\CarbonInterface
                    ? $item->created_at->format('d/m/Y H:i')
                    : ($item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-');

                $seragam = $item->pengambilanSeragam;
                $statusSeragam = 'Belum Diambil';
                if ($seragam) {
                    $waktuAmbil = $seragam->waktu_ambil instanceof \Carbon\CarbonInterface
                        ? $seragam->waktu_ambil->format('d/m/Y')
                        : ($seragam->waktu_ambil ? date('d/m/Y', strtotime($seragam->waktu_ambil)) : null);
                    $statusSeragam = $waktuAmbil ? "Sudah Diambil ({$waktuAmbil})" : 'Sudah Diambil';
                }

                // Bersihkan baris baru pada alamat agar setiap pendaftar rapi dalam 1 baris tabel
                $alamatBersih = preg_replace('/\s+/', ' ', trim((string) $item->alamat));

                $row = [
                    $item->nomor_urut ?? ($index + 1),
                    $item->nomor_pendaftaran,
                    trim((string) $item->nama_lengkap),
                    $this->formatAngkaPanjang($item->nik, $formatAngka),
                    $this->formatAngkaPanjang($item->nisn, $formatAngka),
                    trim((string) $item->tempat_lahir),
                    $tglLahir,
                    $item->jenis_kelamin === 'L' ? 'Laki-laki' : ($item->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
                    $alamatBersih,
                    trim((string) $item->asal_sekolah),
                    trim((string) $item->nama_orang_tua),
                    $this->formatAngkaPanjang($item->no_hp_orang_tua, $formatAngka),
                    $item->jurusan->nama_jurusan ?? '-',
                    $item->jalur_pendaftaran === 'prestasi' ? 'Jalur Prestasi' : 'Jalur Umum / Reguler',
                    $item->gelombang->nama_gelombang ?? '-',
                    $item->jalur_registrasi === 'offline' ? 'Offline (Loket Sekolah)' : 'Online (Mandiri)',
                    $this->labelStatusBenefit($item->status_benefit),
                    $this->labelStatusBerkas($item->status_berkas_ringkas),
                    $statusSeragam,
                    $tglDaftar,
                ];

                fputcsv($file, $row, $delimiter, '"', "\\", "\r\n");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function eksporExcel(Request $request): StreamedResponse
    {
        $user = Auth::guard('web')->user();
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();

        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam']);

        if ($tahunAjaran) {
            $query->where('tahun_ajaran_id', $tahunAjaran->id);
        }

        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        if ($jalur = $request->input('jalur_pendaftaran')) {
            if ($jalur === 'reguler') {
                $jalur = 'umum';
            }
            $query->where('jalur_pendaftaran', $jalur);
        }

        $pendaftarList = $query->orderBy('nomor_urut')->get();

        if ($statusBerkas = $request->input('status_berkas')) {
            $pendaftarList = $pendaftarList->filter(function ($item) use ($statusBerkas) {
                return $item->status_berkas_ringkas === $statusBerkas;
            });
        }

        if ($user) {
            LogAktivitas::catat($user, 'ekspor_data', 'Laporan PPDB', "Ekspor data Excel calon siswa PPDB ({$pendaftarList->count()} data).");
        }

        $filename = 'data-pendaftar-ppdb-smk-muhammadiyah-paguyangan-' . date('Ymd-His') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($pendaftarList) {
            $file = fopen('php://output', 'w');

            // XML Spreadsheet 2003 / Excel HTML kompatibel
            $headerHtml = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' . "\r\n"
                . '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\r\n"
                . '<style>' . "\r\n"
                . 'th { background-color: #092347; color: #ffffff; font-weight: bold; text-align: center; border: 0.5pt solid #cccccc; padding: 6px; }' . "\r\n"
                . 'td { border: 0.5pt solid #cbd5e1; padding: 5px; vertical-align: top; }' . "\r\n"
                . '.text-cell { mso-number-format:"\\@"; }' . "\r\n"
                . '.center { text-align: center; }' . "\r\n"
                . '</style></head><body>' . "\r\n"
                . '<table border="1">' . "\r\n"
                . '<thead><tr>' . "\r\n"
                . '<th>No</th><th>No. Pendaftaran</th><th>Nama Lengkap</th><th>NIK</th><th>NISN</th>' . "\r\n"
                . '<th>Tempat Lahir</th><th>Tanggal Lahir</th><th>Jenis Kelamin</th><th>Alamat</th><th>Asal Sekolah</th>' . "\r\n"
                . '<th>Nama Orang Tua / Wali</th><th>No HP Orang Tua / Wali</th><th>Jurusan</th><th>Jalur</th>' . "\r\n"
                . '<th>Gelombang</th><th>Registrasi</th><th>Status Promo SPP</th><th>Status Berkas</th>' . "\r\n"
                . '<th>Status Seragam</th><th>Waktu Mendaftar</th>' . "\r\n"
                . '</tr></thead><tbody>' . "\r\n";

            fwrite($file, $headerHtml);

            foreach ($pendaftarList as $index => $item) {
                $tglLahir = $item->tanggal_lahir instanceof \Carbon\CarbonInterface
                    ? $item->tanggal_lahir->format('d/m/Y')
                    : ($item->tanggal_lahir ? date('d/m/Y', strtotime($item->tanggal_lahir)) : '-');

                $tglDaftar = $item->created_at instanceof \Carbon\CarbonInterface
                    ? $item->created_at->format('d/m/Y H:i')
                    : ($item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-');

                $seragam = $item->pengambilanSeragam;
                $statusSeragam = 'Belum Diambil';
                if ($seragam) {
                    $waktuAmbil = $seragam->waktu_ambil instanceof \Carbon\CarbonInterface
                        ? $seragam->waktu_ambil->format('d/m/Y')
                        : ($seragam->waktu_ambil ? date('d/m/Y', strtotime($seragam->waktu_ambil)) : null);
                    $statusSeragam = $waktuAmbil ? "Sudah Diambil ({$waktuAmbil})" : 'Sudah Diambil';
                }

                $alamatBersih = preg_replace('/\s+/', ' ', trim((string) $item->alamat));

                $rowHtml = '<tr>'
                    . '<td class="center">' . ($item->nomor_urut ?? ($index + 1)) . '</td>'
                    . '<td class="center text-cell">' . htmlspecialchars((string) $item->nomor_pendaftaran) . '</td>'
                    . '<td>' . htmlspecialchars((string) $item->nama_lengkap) . '</td>'
                    . '<td class="text-cell">' . htmlspecialchars((string) $item->nik) . '</td>'
                    . '<td class="text-cell">' . htmlspecialchars((string) $item->nisn) . '</td>'
                    . '<td>' . htmlspecialchars((string) $item->tempat_lahir) . '</td>'
                    . '<td class="center">' . htmlspecialchars($tglLahir) . '</td>'
                    . '<td class="center">' . ($item->jenis_kelamin === 'L' ? 'Laki-laki' : ($item->jenis_kelamin === 'P' ? 'Perempuan' : '-')) . '</td>'
                    . '<td>' . htmlspecialchars($alamatBersih) . '</td>'
                    . '<td>' . htmlspecialchars((string) $item->asal_sekolah) . '</td>'
                    . '<td>' . htmlspecialchars((string) $item->nama_orang_tua) . '</td>'
                    . '<td class="text-cell">' . htmlspecialchars((string) $item->no_hp_orang_tua) . '</td>'
                    . '<td>' . htmlspecialchars($item->jurusan->nama_jurusan ?? '-') . '</td>'
                    . '<td class="center">' . ($item->jalur_pendaftaran === 'prestasi' ? 'Prestasi' : 'Umum') . '</td>'
                    . '<td class="center">' . htmlspecialchars($item->gelombang->nama_gelombang ?? '-') . '</td>'
                    . '<td class="center">' . ($item->jalur_registrasi === 'offline' ? 'Offline' : 'Online') . '</td>'
                    . '<td class="center">' . htmlspecialchars($this->labelStatusBenefit($item->status_benefit)) . '</td>'
                    . '<td class="center">' . htmlspecialchars($this->labelStatusBerkas($item->status_berkas_ringkas)) . '</td>'
                    . '<td class="center">' . htmlspecialchars($statusSeragam) . '</td>'
                    . '<td class="center">' . htmlspecialchars($tglDaftar) . '</td>'
                    . '</tr>' . "\r\n";

                fwrite($file, $rowHtml);
            }

            fwrite($file, '</tbody></table></body></html>');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function eksporCetak(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::first();
        $pengaturan = PengaturanSistem::instance();

        $query = CalonSiswa::with(['jurusan', 'gelombang', 'berkas', 'pengambilanSeragam']);

        if ($tahunAjaran) {
            $query->where('tahun_ajaran_id', $tahunAjaran->id);
        }

        if ($gelombangId = $request->input('gelombang_id')) {
            $query->where('gelombang_id', $gelombangId);
        }

        if ($jalur = $request->input('jalur_pendaftaran')) {
            if ($jalur === 'reguler') {
                $jalur = 'umum';
            }
            $query->where('jalur_pendaftaran', $jalur);
        }

        $pendaftarList = $query->orderBy('nomor_urut')->get();

        if ($statusBerkas = $request->input('status_berkas')) {
            $pendaftarList = $pendaftarList->filter(function ($item) use ($statusBerkas) {
                return $item->status_berkas_ringkas === $statusBerkas;
            });
        }

        return view('pages.admin.ekspor.cetak', compact('pendaftarList', 'tahunAjaran', 'pengaturan'));
    }

    /**
     * Memformat angka panjang (NIK, NISN, No HP) agar tidak rusak saat dibuka di spreadsheet
     */
    protected function formatAngkaPanjang(?string $value, string $format): string
    {
        $clean = trim((string) $value);
        if ($clean === '') {
            return '-';
        }

        return match ($format) {
            'kutip' => "'" . $clean,
            'raw', 'polos' => $clean,
            default => '="' . $clean . '"', // Formula teks Excel: menghindari scientific notation dan mempreservasi angka nol di depan
        };
    }

    /**
     * Label manusiawi untuk status benefit promo SPP
     */
    protected function labelStatusBenefit(?string $status): string
    {
        return match ($status) {
            'klaim_aktif' => 'Klaim Aktif (Gratis SPP 3 Bulan)',
            'kandidat_100_pertama' => 'Kandidat 100 Pertama',
            'tidak_dapat' => 'Reguler (Tanpa Promo SPP)',
            default => $status ? ucwords(str_replace('_', ' ', $status)) : '-',
        };
    }

    /**
     * Label manusiawi untuk status berkas ringkas
     */
    protected function labelStatusBerkas(?string $status): string
    {
        return match ($status) {
            'disetujui' => 'Lengkap & Disetujui',
            'menunggu' => 'Menunggu Verifikasi',
            'ditolak' => 'Ada Berkas Ditolak',
            'belum_lengkap' => 'Belum Lengkap',
            default => $status ? ucwords(str_replace('_', ' ', $status)) : '-',
        };
    }
}
