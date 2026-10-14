@extends('layouts.app')

@section('title', 'Pendaftaran Berhasil')

@section('content')
<section class="py-section bg-canvas">
    <div class="container container-narrow">
        <div style="background-color: #ffffff; border: 1px solid var(--border-default); border-radius: var(--radius-card); padding: 2.5rem; text-align: center; box-shadow: var(--shadow-dropdown);">
            {{-- Icon Centang Sukses --}}
            <div style="width: 72px; height: 72px; background-color: var(--status-success-bg); color: var(--status-success-text); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <span style="font-size: 0.8125rem; font-weight: 700; color: var(--status-success-text); text-transform: uppercase; letter-spacing: 0.05em;">
                Registrasi Mandiri Berhasil
            </span>
            <h1 style="font-size: 1.875rem; font-weight: 800; color: var(--primary-deep); margin: 0.35rem 0 0.75rem 0;">
                Pendaftaran Telah Diterima Sistem
            </h1>
            <p style="font-size: 0.9375rem; color: var(--text-body); max-width: 580px; margin: 0 auto 2rem auto; line-height: 1.6;">
                Selamat, pendaftaran Anda untuk Tahun Ajaran {{ $siswa->tahunAjaran->nama }} telah berhasil disimpan. Simpan Nomor Pendaftaran resmi berikut untuk masuk ke Portal Calon Siswa.
            </p>

            {{-- Kartu Nomor Pendaftaran (FR-05) --}}
            <div style="background-color: var(--primary-surface); border: 2px dashed var(--primary-base); border-radius: var(--radius-card); padding: 1.5rem; max-width: 440px; margin: 0 auto 2rem auto;">
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary-base); text-transform: uppercase; letter-spacing: 0.05em;">
                    Nomor Pendaftaran Resmi Anda
                </span>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary-deep); letter-spacing: 0.03em; margin-top: 0.25rem;">
                    {{ $siswa->nomor_pendaftaran }}
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.35rem;">
                    Nomor Urut Pendaftaran: <strong>#{{ $siswa->nomor_urut }}</strong> &bull; Jalur {{ ucfirst($siswa->jalur_pendaftaran) }}
                </div>
            </div>

            {{-- Kotak Instruksi Login (FR-05: Nilai password TIDAK dicetak) --}}
            <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-md); padding: 1.25rem; text-align: left; max-width: 540px; margin: 0 auto 2rem auto; font-size: 0.875rem;">
                <strong style="color: #92400e; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9375rem; margin-bottom: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Instruksi Akses Portal Siswa (Fase 2)
                </strong>
                <p style="color: #78350f; margin-bottom: 0.75rem; line-height: 1.5;">
                    Gunakan kredensial berikut untuk masuk ke portal dan mengunggah berkas persyaratan:
                </p>
                <ul style="padding-left: 1.25rem; margin: 0; color: #78350f; display: flex; flex-direction: column; gap: 0.35rem;">
                    <li><strong>Username:</strong> Nomor Pendaftaran di atas (<code>{{ $siswa->nomor_pendaftaran }}</code>)</li>
                    <li><strong>Password:</strong> Tanggal Lahir Anda dengan format 8 digit <code>DDMMYYYY</code> (Contoh: jika lahir 15 Agustus 2008, maka passwordnya adalah <code>15082008</code>).</li>
                </ul>
                <div style="font-size: 0.75rem; color: #a16207; margin-top: 0.75rem; font-style: italic;">
                    * Catatan: Nilai sandi sengaja tidak dicetak di lembar ini demi menjaga keamanan dan privasi data pribadi Anda.
                </div>
            </div>

            {{-- Ringkasan Data Siswa --}}
            <div style="max-width: 540px; margin: 0 auto 2.5rem auto; text-align: left;">
                <table class="dense-table" style="font-size: 0.875rem;">
                    <tbody>
                        <tr>
                            <td style="width: 40%; color: var(--text-muted); font-weight: 600;">Nama Lengkap</td>
                            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 600;">Pilihan Jurusan</td>
                            <td>{{ $siswa->jurusan->nama_jurusan }}</td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 600;">Gelombang Masuk</td>
                            <td>{{ $siswa->gelombang->nama_gelombang ?? 'Gelombang Reguler' }}</td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 600;">Status Benefit SPP 3 Bulan</td>
                            <td>
                                @if($siswa->status_benefit === 'kandidat_100_pertama')
                                    <span class="badge" style="background-color: var(--status-warning-bg); color: var(--status-warning-text); border-color: var(--status-warning-border);">
                                        Kandidat 100 Pendaftar Pertama (Gratis SPP 3 Bulan)
                                    </span>
                                @elseif($siswa->status_benefit === 'klaim_aktif')
                                    <span class="badge badge-success">
                                        Resmi Berhak Bebas SPP 3 Bulan
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #f1f5f9; color: var(--text-muted); border-color: var(--border-default);">
                                        Reguler
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Tombol Aksi --}}
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('ppdb.bukti', $siswa->id) }}" target="_blank" class="btn btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak / Unduh Bukti PDF</span>
                </a>

                <a href="{{ route('siswa.login') }}" class="btn btn-primary">
                    <span>Masuk ke Portal Siswa</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
