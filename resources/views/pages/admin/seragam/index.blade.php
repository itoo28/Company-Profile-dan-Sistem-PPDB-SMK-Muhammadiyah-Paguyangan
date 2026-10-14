@extends('layouts.admin')

@section('title', 'Loket Pelayanan Seragam — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Loket Pelayanan & Pencatatan Seragam')
@section('header-subtitle', 'Pemberian seragam hanya untuk siswa yang seluruh berkas wajibnya Disetujui (BR-05) dan hanya dapat diambil sekali (BR-06)')

@section('content')

{{-- Search & Filter --}}
<form method="GET" action="{{ route('admin.seragam.index') }}" class="admin-filter-bar">
    <div style="flex: 1; min-width: 240px;">
        <input type="text" 
               name="q" 
               value="{{ request('q') }}" 
               class="form-control" 
               style="min-height: 38px; font-size: 0.8125rem;" 
               placeholder="Cari Nama Siswa atau Nomor Pendaftaran...">
    </div>

    <div>
        <select name="status" class="form-control" style="min-height: 38px; font-size: 0.8125rem;">
            <option value="">Semua Status Pengambilan</option>
            <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Ambil</option>
            <option value="sudah" {{ request('status') === 'sudah' ? 'selected' : '' }}>Sudah Ambil</option>
        </select>
    </div>

    <div style="display: flex; gap: 0.35rem;">
        <button type="submit" class="btn btn-secondary btn-sm" style="min-height: 38px;">
            Cari
        </button>
        @if(request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.seragam.index') }}" class="btn btn-outline btn-sm" style="min-height: 38px;">
                Reset
            </a>
        @endif
    </div>
</form>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>No. Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>Jurusan</th>
                <th>Jalur & Gelombang</th>
                <th>Clearance Berkas (BR-05)</th>
                <th>Status Pengambilan (BR-06)</th>
                <th style="text-align: right; width: 220px;">Aksi Loket TU</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $idx => $s)
                @php
                    $isClearance = $s->isSeragamClearanceApproved();
                    $isAmbil = $s->pengambilanSeragam !== null;
                @endphp
                <tr>
                    <td>{{ $siswaList->firstItem() + $idx }}</td>
                    <td>
                        <strong style="color: var(--primary-deep); font-family: monospace;">{{ $s->nomor_pendaftaran }}</strong>
                        <div style="font-size: 0.6875rem; color: var(--text-muted);">Urut #{{ $s->nomor_urut }}</div>
                    </td>
                    <td>
                        <strong>{{ $s->nama_lengkap }}</strong>
                        <div style="font-size: 0.6875rem; color: var(--text-muted);">{{ $s->asal_sekolah }}</div>
                    </td>
                    <td>
                        <span class="badge" style="background-color: var(--primary-surface); color: var(--primary-base); font-size: 0.6875rem;">
                            {{ $s->jurusan->kode ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div>{{ ucfirst($s->jalur_pendaftaran) }}</div>
                        <div style="font-size: 0.6875rem; color: var(--text-muted);">{{ $s->gelombang->nama_gelombang ?? '-' }}</div>
                    </td>
                    <td>
                        @if($isClearance)
                            <span class="badge badge-success" style="font-size: 0.6875rem;">Clearance Disetujui</span>
                        @else
                            <span class="badge" style="background-color: #fee2e2; color: #b91c1c; font-size: 0.6875rem;">Belum Lengkap/Disetujui</span>
                        @endif
                    </td>
                    <td>
                        @if($isAmbil)
                            <div style="font-size: 0.8125rem; font-weight: 700; color: #15803d;">
                                SUDAH DIAMBIL
                            </div>
                            <div style="font-size: 0.6875rem; color: var(--text-muted);">
                                {{ $s->pengambilanSeragam->waktu_ambil->format('d/m/Y H:i') }} &bull; Oleh: {{ $s->pengambilanSeragam->petugas->name ?? 'Staf' }}
                            </div>
                        @else
                            <span class="badge" style="background-color: #f1f5f9; color: var(--text-muted); font-size: 0.6875rem;">
                                Belum Mengambil
                            </span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if(!$isAmbil && $isClearance)
                            <form action="{{ route('admin.seragam.store') }}" method="POST" onsubmit="return confirm('Konfirmasi: Catat pengambilan seragam untuk {{ addslashes($s->nama_lengkap) }}? Data hanya boleh dicatat 1 kali (BR-06).')">
                                @csrf
                                <input type="hidden" name="calon_siswa_id" value="{{ $s->id }}">
                                <button type="submit" class="btn btn-sm" style="background-color: #15803d; color: #ffffff; min-height: 32px; font-size: 0.75rem;">
                                    Catat Pengambilan Seragam
                                </button>
                            </form>
                        @elseif($isAmbil)
                            <span style="font-size: 0.75rem; color: #15803d; font-weight: 600;">
                                Selesai Dilayani
                            </span>
                        @else
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;" title="Berkas wajib belum semua disetujui">
                                Terkunci (Belum Clearance)
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                        Tidak ada data calon siswa ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.25rem;">
    {{ $siswaList->links() }}
</div>
@endsection
