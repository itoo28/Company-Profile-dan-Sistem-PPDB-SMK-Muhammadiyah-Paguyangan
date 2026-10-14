@extends('layouts.admin')

@section('title', 'Dashboard Administrasi — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ringkasan Operasional PPDB')
@section('header-subtitle', 'Tahun Ajaran ' . $tahunAjaran->nama . ' • Data Terkini Sistem PPDB SMK Muhammadiyah Paguyangan')

@section('header-action')
    <a href="{{ route('admin.pendaftar.baru') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; font-weight: 700;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Pendaftaran Offline Baru</span>
    </a>
    <a href="{{ route('admin.seragam.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; font-weight: 600;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 1.96v.58a2 2 0 0 0 1.09 1.76l2.63 1.32V21a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9.08l2.63-1.32A2 2 0 0 0 21.72 6v-.58a2 2 0 0 0-1.34-1.96z"></path>
        </svg>
        <span>Buka Loket Seragam</span>
    </a>
@endsection

@section('content')

{{-- 4 Stat Cards Berbasis Data Nyata (FR-30, No Invented Slop Numbers) --}}
<div class="admin-stat-grid">
    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-label">Total Calon Siswa</div>
            <div class="admin-stat-value">{{ $totalPendaftar }}</div>
        </div>
        <div class="admin-stat-desc">
            {{ $pendaftarOnline }} Daring &bull; {{ $pendaftarOffline }} Langsung (Offline)
        </div>
    </div>

    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-label">Berkas Menunggu Verifikasi</div>
            <div class="admin-stat-value" style="color: var(--accent-text);">{{ $berkasMenungguCount }}</div>
        </div>
        <div class="admin-stat-desc">
            {{ $berkasDitolakCount }} berkas berstatus perlu perbaikan
        </div>
    </div>

    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-label">Pengambilan Seragam</div>
            <div class="admin-stat-value" style="color: var(--status-success-text);">{{ $totalSeragamDiambil }}</div>
        </div>
        <div class="admin-stat-desc">
            Siswa yang telah dilayani di loket Tata Usaha
        </div>
    </div>

    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-label">Promo Bebas SPP 3 Bulan</div>
            <div class="admin-stat-value">{{ $promoKlaimAktifCount }} <span style="font-size: 1rem; color: var(--text-muted); font-weight: 500;">/ {{ $kuotaPromo }}</span></div>
        </div>
        <div class="admin-stat-desc">
            {{ $promoKandidatCount }} kandidat belum tuntas berkas
        </div>
    </div>
</div>

<div class="card-grid-2" style="margin-bottom: 1.5rem;">
    {{-- Tabel Rekap Daya Tampung per Jurusan --}}
    <div class="admin-table-container">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-default); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1rem; font-weight: 800; color: var(--primary-deep); margin: 0;">
                Keterisian Kuota per Jurusan Vokasi
            </h2>
            <a href="{{ route('admin.jurusan.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--primary-base);">
                Kelola Jurusan &rarr;
            </a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Jurusan</th>
                    <th style="text-align: center;">Daya Tampung</th>
                    <th style="text-align: center;">Pendaftar</th>
                    <th style="text-align: center;">Sisa Kuota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurusans as $j)
                    @php
                        $sisa = max(0, $j->kuota - $j->calon_siswa_count);
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $j->nama_jurusan }}</strong>
                            <div style="font-size: 0.6875rem; color: var(--text-muted);">{{ $j->bidang_keahlian }}</div>
                        </td>
                        <td style="text-align: center;">{{ $j->kuota }}</td>
                        <td style="text-align: center; font-weight: 700; color: var(--primary-base);">{{ $j->calon_siswa_count }}</td>
                        <td style="text-align: center; color: {{ $sisa === 0 ? 'var(--status-danger-text)' : 'var(--text-body)' }}; font-weight: 600;">
                            {{ $sisa }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tabel Rekap Gelombang PPDB --}}
    <div class="admin-table-container">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-default); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1rem; font-weight: 800; color: var(--primary-deep); margin: 0;">
                Pendaftar per Gelombang
            </h2>
            @if(auth('web')->user()->isSuperAdmin())
                <a href="{{ route('admin.gelombang.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--primary-base);">
                    Kelola Gelombang &rarr;
                </a>
            @endif
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama Gelombang</th>
                    <th>Rentang Tanggal</th>
                    <th style="text-align: center;">Pendaftar</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gelombangs as $g)
                    <tr>
                        <td>
                            <strong>{{ $g->nama_gelombang }}</strong>
                            @if($g->is_bidikmisi)
                                <span class="badge badge-accent" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Bidikmisi</span>
                            @endif
                        </td>
                        <td style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ $g->tanggal_mulai->format('d/m/Y') }} - {{ $g->tanggal_selesai->format('d/m/Y') }}
                        </td>
                        <td style="text-align: center; font-weight: 700;">{{ $g->calon_siswa_count }}</td>
                        <td style="text-align: center;">
                            @if($g->isSedangBerjalan())
                                <span class="badge badge-success" style="font-size: 0.6875rem;">Aktif Saat Ini</span>
                            @else
                                <span class="badge" style="background-color: #f1f5f9; color: var(--text-muted); font-size: 0.6875rem;">
                                    {{ $g->is_aktif ? 'Terjadwal' : 'Nonaktif' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada gelombang pendaftaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
