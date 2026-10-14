<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran - {{ $siswa->nomor_pendaftaran }} | SMK Muhammadiyah Paguyangan</title>
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

        /* Kop Surat Resmi Sekolah */
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
            margin-bottom: 0.35rem;
        }

        .sheet-subtitle {
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.25rem;
        }

        /* Nomor Pendaftaran Box */
        .nomor-box {
            border: 2px solid #0b57d0;
            background-color: #eff6ff;
            border-radius: 6px;
            padding: 0.75rem;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .nomor-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #0b57d0;
        }

        .nomor-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #092347;
            letter-spacing: 0.04em;
        }

        /* Tabel Biodata */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .data-table td {
            padding: 0.45rem 0.6rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .data-table td.label-col {
            width: 32%;
            font-weight: 600;
            color: #475569;
        }

        .data-table td.colon-col {
            width: 2%;
            text-align: center;
        }

        .data-table td.val-col {
            font-weight: 700;
            color: #0f172a;
        }

        /* Box Instruksi Login */
        .instruction-box {
            border: 1px solid #fde68a;
            background-color: #fefce8;
            border-radius: 6px;
            padding: 0.875rem;
            font-size: 0.8125rem;
            margin-bottom: 1.5rem;
            color: #78350f;
        }

        .instruction-title {
            font-weight: 800;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
        }

        /* Tanda Tangan */
        .ttd-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            font-size: 0.8125rem;
        }

        .ttd-box {
            text-align: center;
            width: 220px;
        }

        .ttd-space {
            height: 60px;
        }

        .no-print-bar {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .btn-print {
            background-color: #0b57d0;
            color: #ffffff;
            border: none;
            padding: 0.625rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print-bar {
                display: none;
            }
            .sheet-container {
                box-shadow: none;
                padding: 10mm 15mm;
                width: 100%;
                min-height: auto;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <button class="btn-print" onclick="window.print()">
            Cetak Dokumen Bukti Pendaftaran (A4)
        </button>
    </div>

    <div class="sheet-container">
        {{-- Kop Surat Resmi --}}
        <div class="kop-header">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo SMK" class="kop-logo">
            <div class="kop-text">
                <div class="kop-instansi">Pimpinan Cabang Muhammadiyah Paguyangan &bull; Majelis Dikdasmen</div>
                <div class="kop-nama-sekolah">SMK Muhammadiyah Paguyangan</div>
                <div class="kop-alamat">
                    {{ $pengaturan->alamat }}<br>
                    Telepon: {{ $pengaturan->telepon }} | CS WhatsApp: {{ $pengaturan->whatsapp }} | Email: {{ $pengaturan->email }}
                </div>
            </div>
        </div>
        <div class="kop-border"></div>

        <div class="sheet-title">LEMBAR BUKTI PENDAFTARAN PESERTA DIDIK BARU</div>
        <div class="sheet-subtitle">Tahun Ajaran {{ $siswa->tahunAjaran->nama }} &bull; Jalur {{ ucfirst($siswa->jalur_pendaftaran) }}</div>

        <div class="nomor-box">
            <div class="nomor-title">Nomor Pendaftaran</div>
            <div class="nomor-value">{{ $siswa->nomor_pendaftaran }}</div>
        </div>

        <table class="data-table">
            <tbody>
                <tr>
                    <td class="label-col">Nomor Urut Pendaftaran</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">#{{ $siswa->nomor_urut }}</td>
                </tr>
                <tr>
                    <td class="label-col">Nama Lengkap Siswa</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td class="label-col">NIK / NISN</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->nik }} / {{ $siswa->nisn }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tempat, Tanggal Lahir</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Jenis Kelamin</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Asal Sekolah</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->asal_sekolah }}</td>
                </tr>
                <tr>
                    <td class="label-col">Pilihan Konsentrasi Keahlian</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->jurusan->nama_jurusan }} ({{ $siswa->jurusan->kode }})</td>
                </tr>
                <tr>
                    <td class="label-col">Gelombang Pendaftaran</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->gelombang->nama_gelombang ?? '-' }} (Registrasi: {{ ucfirst($siswa->jalur_registrasi) }})</td>
                </tr>
                <tr>
                    <td class="label-col">Nama Orang Tua / Wali</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->nama_orang_tua }} (HP: {{ $siswa->no_hp_orang_tua }})</td>
                </tr>
                <tr>
                    <td class="label-col">Alokasi Benefit Promo SPP</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">
                        @if($siswa->status_benefit === 'kandidat_100_pertama')
                            KANDIDAT 100 PENDAFTAR PERTAMA (GRATIS SPP 3 BULAN)
                        @elseif($siswa->status_benefit === 'klaim_aktif')
                            RESMI BERHAK BEBAS BIAYA SPP 3 BULAN
                        @else
                            TIDAK MASUK KUOTA PROMO (REGULER)
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-col">Waktu Pendaftaran</td>
                    <td class="colon-col">:</td>
                    <td class="val-col">{{ $siswa->created_at->translatedFormat('l, d F Y - H:i') }} WIB</td>
                </tr>
            </tbody>
        </table>

        {{-- Instruksi Akses Portal Siswa --}}
        <div class="instruction-box">
            <div class="instruction-title">PANDUAN UNGGAH BERKAS & TAHAP SELANJUTNYA:</div>
            <ol style="padding-left: 1.25rem; margin: 0; line-height: 1.5;">
                <li>Buka situs PPDB resmi dan masuk ke menu <strong>Login Siswa</strong>.</li>
                <li>Gunakan <strong>Nomor Pendaftaran</strong> di atas sebagai Username.</li>
                <li>Password awal adalah 8 digit Tanggal Lahir Anda (<code>DDMMYYYY</code>).</li>
                <li>Unggah dokumen Kartu Keluarga, Akta Kelahiran, dan Ijazah/SKL sebelum batas waktu berakhir.</li>
                <li>Bawa lembar bukti pendaftaran fisik ini saat pengambilan seragam di ruang Tata Usaha (TU) sekolah.</li>
            </ol>
        </div>

        {{-- Tanda Tangan & Stempel --}}
        <div class="ttd-grid">
            <div class="ttd-box">
                <div>Calon Peserta Didik,</div>
                <div class="ttd-space"></div>
                <div style="font-weight: 700; text-decoration: underline;">{{ $siswa->nama_lengkap }}</div>
            </div>

            <div class="ttd-box">
                <div>Paguyangan, {{ date('d F Y') }}<br>Panitia PPDB Sekolah,</div>
                <div class="ttd-space"></div>
                <div style="font-weight: 700; text-decoration: underline;">Panitia PPDB SMK</div>
                <div style="font-size: 0.75rem; color: #64748b;">NBM. / NIP. Resmi Sekolah</div>
            </div>
        </div>
    </div>
</body>
</html>
