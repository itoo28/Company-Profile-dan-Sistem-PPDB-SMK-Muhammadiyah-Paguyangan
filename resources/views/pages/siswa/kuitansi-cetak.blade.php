<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $pembayaran->kode_transaksi }} | SMK Muhammadiyah Paguyangan</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 2rem 0;
        }

        .sheet-container {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto;
            padding: 15mm 20mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat Resmi */
        .kop-header {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding-bottom: 0.75rem;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            flex: 1;
        }

        .kop-instansi {
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #0b57d0;
        }

        .kop-nama-sekolah {
            font-size: 1.35rem;
            font-weight: 800;
            color: #092347;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 0.2rem 0;
        }

        .kop-alamat {
            font-size: 0.8125rem;
            color: #334155;
            line-height: 1.4;
        }

        .kop-border {
            border-top: 3px solid #092347;
            border-bottom: 1px solid #092347;
            height: 3px;
            margin-bottom: 1.25rem;
        }

        .sheet-title {
            text-align: center;
            font-size: 1.15rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #092347;
            margin-bottom: 0.25rem;
        }

        .sheet-subtitle {
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.25rem;
        }

        /* Receipt Box Top */
        .receipt-badge-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.875rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .receipt-code {
            font-size: 0.9375rem;
            color: #334155;
        }

        .receipt-code strong {
            color: #092347;
            font-size: 1.05rem;
            letter-spacing: 0.05em;
        }

        .badge-lunas {
            background-color: #ecfdf5;
            color: #059669;
            border: 2px solid #10b981;
            font-size: 0.875rem;
            font-weight: 800;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* Section */
        .section-header {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #092347;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 0.35rem;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .data-table td {
            padding: 0.45rem 0.5rem;
            vertical-align: top;
        }

        .data-table td.label-col {
            width: 32%;
            color: #475569;
            font-weight: 600;
        }

        .data-table td.colon-col {
            width: 3%;
            color: #64748b;
        }

        .data-table td.val-col {
            width: 65%;
            color: #0f172a;
            font-weight: 500;
        }

        /* Detail Rincian Table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin: 0.75rem 0 1.25rem 0;
            border: 1px solid #cbd5e1;
        }

        .detail-table th {
            background-color: #f1f5f9;
            color: #092347;
            font-weight: 700;
            text-align: left;
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid #cbd5e1;
            font-size: 0.8125rem;
            text-transform: uppercase;
        }

        .detail-table td {
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }

        .detail-table tr.total-row td {
            background-color: #f8fafc;
            font-weight: 800;
            font-size: 1rem;
            color: #092347;
            border-top: 2px solid #cbd5e1;
        }

        /* Terbilang Box */
        .terbilang-box {
            background-color: #eff6ff;
            border-left: 4px solid #0b57d0;
            padding: 0.65rem 1rem;
            font-size: 0.875rem;
            color: #1e3a8a;
            font-style: italic;
            margin-bottom: 1.5rem;
        }

        /* Tanda Tangan */
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            font-size: 0.875rem;
        }

        .sig-box {
            text-align: center;
            width: 45%;
        }

        .sig-date {
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .sig-role {
            font-weight: 600;
            color: #092347;
            margin-bottom: 3.5rem;
        }

        .sig-name {
            font-weight: 700;
            color: #0f172a;
            text-decoration: underline;
        }

        .sig-nip {
            font-size: 0.8125rem;
            color: #64748b;
        }

        /* Print Bar Controls */
        .action-bar {
            max-width: 210mm;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #0b57d0;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #0842a0;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #334155;
        }
        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .sheet-container {
                box-shadow: none;
                margin: 0;
                padding: 10mm 15mm;
                width: 100%;
                min-height: auto;
            }
            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi Cetak -->
    <div class="action-bar">
        <div>
            @if(auth('siswa')->check())
                <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">
                    &larr; Kembali ke Dashboard Siswa
                </a>
            @else
                <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary">
                    &larr; Kembali ke Daftar Pembayaran
                </a>
            @endif
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Cetak / Simpan Kuitansi (PDF)
            </button>
        </div>
    </div>

    <!-- Lembar Kuitansi Fisik A4 -->
    <div class="sheet-container">
        <!-- Kop Surat Resmi -->
        <div class="kop-header">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo SMK" class="kop-logo">
            <div class="kop-text">
                <div class="kop-instansi">MAJELIS PENDIDIKAN DASAR DAN MENENGAH PDM BREBES</div>
                <div class="kop-nama-sekolah">{{ $pengaturan->nama_sekolah ?? 'SMK MUHAMMADIYAH PAGUYANGAN' }}</div>
                <div class="kop-alamat">
                    {{ $pengaturan->alamat ?? 'Jl. Raya Paguyangan, Brebes, Jawa Tengah' }}<br>
                    Telepon: {{ $pengaturan->telepon ?? '-' }} | WhatsApp: {{ $pengaturan->whatsapp ?? '-' }} | Email: {{ $pengaturan->email ?? '-' }}
                </div>
            </div>
        </div>
        <div class="kop-border"></div>

        <!-- Judul Kuitansi -->
        <div class="sheet-title">TANDA BUKTI PEMBAYARAN BIAYA PENDAFTARAN</div>
        <div class="sheet-subtitle">Penerimaan Peserta Didik Baru (PPDB) Tahun Ajaran {{ $siswa->tahunAjaran->nama ?? '2026/2027' }}</div>

        <!-- Box Status Lunas -->
        <div class="receipt-badge-box">
            <div class="receipt-code">
                No. Transaksi: <strong>{{ $pembayaran->kode_transaksi }}</strong>
            </div>
            <div class="badge-lunas">
                ✓ LUNAS &amp; SAH
            </div>
        </div>

        <!-- Data Calon Siswa -->
        <div class="section-header">A. Data Calon Peserta Didik</div>
        <table class="data-table">
            <tr>
                <td class="label-col">Nomor Pendaftaran</td>
                <td class="colon-col">:</td>
                <td class="val-col"><strong style="color: #0b57d0;">{{ $siswa->nomor_pendaftaran }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">Nama Lengkap Siswa</td>
                <td class="colon-col">:</td>
                <td class="val-col"><strong>{{ $siswa->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">NISN / NIK</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ $siswa->nisn }} / {{ $siswa->nik }}</td>
            </tr>
            <tr>
                <td class="label-col">Pilihan Konsentrasi Keahlian</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ $siswa->jurusan->nama_jurusan ?? '-' }} ({{ $siswa->jurusan->kode ?? '-' }})</td>
            </tr>
            <tr>
                <td class="label-col">Gelombang &amp; Jalur Pendaftaran</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ $siswa->gelombang->nama_gelombang ?? 'Gelombang Reguler' }} | {{ ucfirst($siswa->jalur_pendaftaran) }} (Registrasi: {{ ucfirst($siswa->jalur_registrasi) }})</td>
            </tr>
            <tr>
                <td class="label-col">Asal Sekolah</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ $siswa->asal_sekolah }}</td>
            </tr>
        </table>

        <!-- Rincian Pembayaran -->
        <div class="section-header">B. Rincian Penerimaan Pembayaran</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 52%;">Uraian Pembayaran</th>
                    <th style="width: 20%;">Metode Bayar</th>
                    <th style="width: 20%; text-align: right;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>Biaya Formulir Registrasi &amp; Administrasi PPDB</strong><br>
                        <small style="color: #64748b;">
                            @if($pembayaran->metode_pembayaran === 'transfer')
                                Transfer a.n. {{ $pembayaran->nama_pengirim ?? '-' }} ({{ $pembayaran->bank_pengirim ?? 'Bank' }})
                            @else
                                Pembayaran tunai langsung di Loket Keuangan PPDB Sekolah
                            @endif
                        </small>
                    </td>
                    <td>
                        <span style="text-transform: uppercase; font-weight: 600; font-size: 0.8125rem;">
                            {{ $pembayaran->metode_pembayaran === 'tunai' ? 'Tunai (Kasir)' : 'Transfer Bank' }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 600;">
                        {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; text-transform: uppercase; letter-spacing: 0.05em;">Total Pembayaran Diterima :</td>
                    <td style="text-align: right; color: #059669;">{{ $pembayaran->formatted_nominal }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Terbilang Box -->
        <div class="terbilang-box">
            Status: <strong>LUNAS</strong>. Dokumen ini merupakan bukti sah bahwa calon siswa telah menuntaskan kewajiban pembayaran biaya pendaftaran PPDB {{ $pengaturan->nama_sekolah ?? 'SMK Muhammadiyah Paguyangan' }}.
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-area">
            <div class="sig-box">
                <div class="sig-date">&nbsp;</div>
                <div class="sig-role">Calon Peserta Didik / Wali Murid,</div>
                <div class="sig-name">{{ $siswa->nama_lengkap }}</div>
                <div class="sig-nip">Pendaftar</div>
            </div>

            <div class="sig-box">
                <div class="sig-date">
                    Paguyangan, {{ $pembayaran->diverifikasi_pada ? $pembayaran->diverifikasi_pada->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                </div>
                <div class="sig-role">Panitia / Bendahara PPDB,</div>
                <div class="sig-name">{{ $pembayaran->verifikator->name ?? 'Panitia Penerimaan Siswa' }}</div>
                <div class="sig-nip">Panitia PPDB SMK Muhammadiyah Paguyangan</div>
            </div>
        </div>

        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px dashed #cbd5e1; font-size: 0.75rem; color: #94a3b8; display: flex; justify-content: space-between;">
            <div>Dicetak otomatis pada: {{ now()->translatedFormat('d F Y H:i:s') }} WIB</div>
            <div>Dokumen Resmi PPDB SMK Muhammadiyah Paguyangan</div>
        </div>
    </div>

</body>
</html>
