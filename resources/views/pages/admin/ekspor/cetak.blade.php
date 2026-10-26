<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Pendaftar PPDB — SMK Muhammadiyah Paguyangan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
            font-size: 11pt;
        }
        .page-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .no-print-bar {
            max-width: 1100px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #092347;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-family: sans-serif;
            font-size: 13px;
        }
        .btn-print {
            background: #f59e0b;
            color: #092347;
            font-weight: 700;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-close {
            background: transparent;
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.4);
            padding: 7px 14px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-title {
            text-align: center;
            line-height: 1.25;
        }
        .kop-title h4 { margin: 0; font-size: 13pt; font-weight: normal; text-transform: uppercase; }
        .kop-title h2 { margin: 2px 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-title p { margin: 0; font-size: 9.5pt; color: #333; }

        .report-header {
            text-align: center;
            margin-bottom: 16px;
        }
        .report-header h3 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .report-header p {
            margin: 4px 0 0;
            font-size: 10pt;
        }

        /* Tabel Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 24px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .ttd-table {
            width: 100%;
            border: none;
            font-size: 10pt;
        }
        .ttd-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 45%;
        }

        @media print {
            body { background: white; padding: 0; }
            .no-print-bar { display: none; }
            .page-container { box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div>
        <strong>Mode Pratinjau Cetak Rekapitulasi PPDB</strong> &mdash; Jumlah Data: {{ $pendaftarList->count() }} Pendaftar
    </div>
    <div style="display: flex; gap: 8px;">
        <button onclick="window.print()" class="btn-print">Cetak Sekarang</button>
        <button onclick="window.close()" class="btn-close">Tutup</button>
    </div>
</div>

<div class="page-container">
    <!-- Kop Surat Resmi -->
    <table class="kop-table">
        <tr>
            <td style="width: 80px; text-align: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK" style="width: 70px; height: auto;" onerror="this.style.display='none'">
            </td>
            <td class="kop-title">
                <h4>MAJELIS PENDIDIKAN DASAR MENENGAH DAN PENDIDIKAN NONFORMAL</h4>
                <h4>PIMPINAN DAERAH MUHAMMADIYAH KABUPATEN BREBES</h4>
                <h2>{{ $pengaturan->nama_sekolah ?? 'SMK MUHAMMADIYAH PAGUYANGAN' }}</h2>
                <p>{{ $pengaturan->alamat ?? 'P24P+32J, Sawah, Ladang, Paguyangan, Kec. Paguyangan, Kabupaten Brebes, Jawa Tengah 52276' }} &bull; Telp: {{ $pengaturan->telepon ?? '-' }} &bull; Email: {{ $pengaturan->email ?? '-' }}</p>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="report-header">
        <h3>REKAPITULASI DATA PENDAFTARAN PESERTA DIDIK BARU (PPDB)</h3>
        <p>Tahun Ajaran {{ $tahunAjaran->tahun ?? date('Y') . '/' . (date('Y') + 1) }} &bull; Dicetak Pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Tabel Rekap Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 90px;">No. Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th style="width: 35px;">JK</th>
                <th style="width: 100px;">NISN</th>
                <th>Asal Sekolah</th>
                <th>Konsentrasi Keahlian</th>
                <th style="width: 80px;">Gelombang</th>
                <th style="width: 85px;">Benefit Promo</th>
                <th style="width: 80px;">Status Berkas</th>
                <th style="width: 80px;">Seragam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendaftarList as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $item->nomor_urut ?? ($index + 1) }}</td>
                    <td style="text-align: center; font-weight: bold; font-family: monospace;">{{ $item->nomor_pendaftaran }}</td>
                    <td style="font-weight: bold;">{{ $item->nama_lengkap }}</td>
                    <td style="text-align: center;">{{ $item->jenis_kelamin }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $item->nisn }}</td>
                    <td>{{ $item->asal_sekolah }}</td>
                    <td>{{ $item->jurusan->nama_jurusan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item->gelombang->nama_gelombang ?? '-' }}</td>
                    <td style="text-align: center; font-size: 8pt;">
                        @if($item->status_benefit === 'klaim_aktif')
                            Klaim Aktif (SPP 3 Bln)
                        @elseif($item->status_benefit === 'kandidat_100_pertama')
                            Kandidat 100 Pertama
                        @else
                            Reguler
                        @endif
                    </td>
                    <td style="text-align: center; font-size: 8pt;">
                        @if($item->status_berkas_ringkas === 'disetujui')
                            Lengkap & Disetujui
                        @elseif($item->status_berkas_ringkas === 'menunggu')
                            Menunggu Verifikasi
                        @elseif($item->status_berkas_ringkas === 'ditolak')
                            Ada Berkas Ditolak
                        @else
                            Belum Lengkap
                        @endif
                    </td>
                    <td style="text-align: center; font-size: 8pt;">
                        {{ $item->pengambilanSeragam ? 'SUDAH' : 'BELUM' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 20px;">Tidak ada data pendaftar yang sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pengesahan Tanda Tangan -->
    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td>
                    <br>
                    Mengetahui,<br>
                    Kepala Sekolah<br><br><br><br><br>
                    <strong><u>{{ $pengaturan->nama_kepala_sekolah ?? 'Mohammad Ardianshah, S.Kom' }}</u></strong><br>
                    NBM. .........................................
                </td>
                <td>
                    Paguyangan, {{ now()->translatedFormat('d F Y') }}<br>
                    Ketua Panitia PPDB,<br><br><br><br><br>
                    <strong><u>{{ auth('web')->user()->name ?? 'Panitia Penerimaan Murid Baru' }}</u></strong><br>
                    NBM. .........................................
                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
