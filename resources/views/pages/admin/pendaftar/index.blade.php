@extends('layouts.admin')

@section('title', 'Daftar Calon Siswa PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Data Calon Siswa Baru (PPDB)')
@section('header-subtitle', 'Kelola pendaftar daring dan luring, verifikasi berkas, dan pantau clearance seragam')

@section('header-action')
    <a href="{{ route('admin.pendaftar.baru') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-weight: 700;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Pendaftar Offline</span>
    </a>
@endsection

@section('content')
<div class="content-wrapper">

{{-- Filter & Pencarian Cepat (FR-12 & FR-26) --}}
<form method="GET" action="{{ route('admin.pendaftar.index') }}" class="admin-filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" 
               name="q" 
               value="{{ request('q') }}" 
               class="form-control" 
               style="min-height: 38px; font-size: 0.8125rem;" 
               placeholder="Cari Nama, No. Pendaftaran, NIK, atau Asal Sekolah...">
    </div>

    <div>
        <select name="gelombang_id" class="form-control" style="min-height: 38px; font-size: 0.8125rem;">
            <option value="">Semua Gelombang</option>
            @foreach($gelombangs as $g)
                <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>
                    {{ $g->nama_gelombang }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <select name="jurusan_id" class="form-control" style="min-height: 38px; font-size: 0.8125rem;">
            <option value="">Semua Jurusan</option>
            @foreach($jurusans as $j)
                <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                    {{ $j->kode }} - {{ $j->nama_jurusan }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <select name="jalur_pendaftaran" class="form-control" style="min-height: 38px; font-size: 0.8125rem;">
            <option value="">Semua Jalur</option>
            <option value="umum" {{ request('jalur_pendaftaran') === 'umum' ? 'selected' : '' }}>Umum</option>
            <option value="prestasi" {{ request('jalur_pendaftaran') === 'prestasi' ? 'selected' : '' }}>Prestasi</option>
        </select>
    </div>

    <div>
        <select name="status_berkas" class="form-control" style="min-height: 38px; font-size: 0.8125rem;">
            <option value="">Semua Status Berkas</option>
            <option value="menunggu" {{ request('status_berkas') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="ditolak" {{ request('status_berkas') === 'ditolak' ? 'selected' : '' }}>Ada yang Ditolak</option>
        </select>
    </div>

    <div style="display: flex; gap: 0.35rem;">
        <button type="submit" class="btn btn-secondary btn-sm" style="min-height: 38px;">
            Filter
        </button>
        @if(request()->hasAny(['q', 'gelombang_id', 'jurusan_id', 'jalur_pendaftaran', 'status_berkas']))
            <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-outline btn-sm" style="min-height: 38px;" title="Reset Filter">
                Reset
            </a>
        @endif
    </div>
</form>

{{-- Tabel Data Pendaftar Padat (Dense Table) --}}
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>No. Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>Jurusan</th>
                <th>Jalur & Gelombang</th>
                <th>Benefit Bebas SPP</th>
                <th>Status Berkas</th>
                <th>Seragam</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendaftarList as $index => $item)
                @php
                    $statusRingkas = $item->status_berkas_ringkas;
                @endphp
                <tr>
                    <td>{{ $pendaftarList->firstItem() + $index }}</td>
                    <td>
                        <strong style="color: var(--primary-deep); font-family: monospace;">{{ $item->nomor_pendaftaran }}</strong>
                        <div style="font-size: 0.6875rem; color: var(--text-muted);">
                            Urut #{{ $item->nomor_urut }} &bull; {{ ucfirst($item->jalur_registrasi) }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--text-heading);">{{ $item->nama_lengkap }}</div>
                        <div style="font-size: 0.6875rem; color: var(--text-muted);">
                            NIK: {{ $item->nik }} &bull; {{ $item->asal_sekolah }}
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background-color: var(--primary-surface); color: var(--primary-base); font-size: 0.6875rem;">
                            {{ $item->jurusan->kode ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ ucfirst($item->jalur_pendaftaran) }}</div>
                        <div style="font-size: 0.6875rem; color: var(--text-muted);">
                            {{ $item->gelombang->nama_gelombang ?? '-' }}
                        </div>
                    </td>
                    <td>
                        @if($item->status_benefit === 'klaim_aktif')
                            <span class="badge badge-success" style="font-size: 0.65rem;">
                                Bebas SPP 3 Bln (Aktif)
                            </span>
                        @elseif($item->status_benefit === 'kandidat_100_pertama')
                            <span class="badge" style="background-color: var(--status-warning-bg); color: var(--status-warning-text); font-size: 0.65rem;">
                                Kandidat 100 Pertama
                            </span>
                        @else
                            <span style="font-size: 0.75rem; color: var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td>
                        @if($statusRingkas === 'disetujui')
                            <span class="badge badge-success" style="font-size: 0.6875rem;">Disetujui Semua</span>
                        @elseif($statusRingkas === 'menunggu')
                            <span class="badge badge-accent" style="font-size: 0.6875rem;">Menunggu Verifikasi</span>
                        @elseif($statusRingkas === 'ditolak')
                            <span class="badge" style="background-color: var(--status-danger-bg); color: var(--status-danger-text); font-size: 0.6875rem;">Ada yang Ditolak</span>
                        @else
                            <span class="badge" style="background-color: #f1f5f9; color: var(--text-muted); font-size: 0.6875rem;">Belum Lengkap</span>
                        @endif
                    </td>
                    <td>
                        @if($item->pengambilanSeragam)
                            <span class="badge badge-success" style="font-size: 0.65rem;">Sudah Ambil</span>
                        @elseif($item->isSeragamClearanceApproved())
                            <span class="badge" style="background-color: #ecfdf5; color: #047857; font-size: 0.65rem; border: 1px solid #a7f3d0;">Siap Ambil</span>
                        @else
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Terkunci</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.pendaftar.show', $item->id) }}" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.625rem; min-height: 32px;">
                            Detail & Verifikasi
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                        Tidak ada data calon siswa yang sesuai dengan filter pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.25rem;">
    {{ $pendaftarList->links() }}
</div>
</div>
@endsection
