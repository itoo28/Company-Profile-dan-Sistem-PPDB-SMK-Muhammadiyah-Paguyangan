@extends('layouts.admin')

@section('title', 'Daftar Calon Siswa PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Data Calon Siswa Baru (PPDB)')
@section('header-subtitle', 'Kelola pendaftar daring dan luring, verifikasi berkas, dan pantau clearance seragam')

@section('header-action')
    <flux:button href="{{ route('admin.pendaftar.baru') }}" variant="primary" icon="user-plus">
        Tambah Pendaftar Offline
    </flux:button>
@endsection

@section('content')
<div class="content-wrapper">

{{-- Filter & Pencarian Cepat (FR-12 & FR-26) --}}
<form method="GET" action="{{ route('admin.pendaftar.index') }}" class="admin-filter-bar flex items-center justify-between flex-wrap gap-3">
    <div class="flex-1 min-w-[220px]">
        <input type="text" 
               name="q" 
               value="{{ request('q') }}" 
               class="form-control text-xs h-9" 
               placeholder="Cari Nama, No. Pendaftaran, NIK, atau Asal Sekolah...">
    </div>

    <div>
        <select name="gelombang_id" class="form-select text-xs h-9">
            <option value="">Semua Gelombang</option>
            @foreach($gelombangs as $g)
                <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>
                    {{ $g->nama_gelombang }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <select name="jurusan_id" class="form-select text-xs h-9">
            <option value="">Semua Jurusan</option>
            @foreach($jurusans as $j)
                <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                    {{ $j->kode }} - {{ $j->nama_jurusan }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <select name="jalur_pendaftaran" class="form-select text-xs h-9">
            <option value="">Semua Jalur</option>
            <option value="umum" {{ request('jalur_pendaftaran') === 'umum' ? 'selected' : '' }}>Umum</option>
            <option value="prestasi" {{ request('jalur_pendaftaran') === 'prestasi' ? 'selected' : '' }}>Prestasi</option>
        </select>
    </div>

    <div>
        <select name="status_berkas" class="form-select text-xs h-9">
            <option value="">Semua Status Berkas</option>
            <option value="menunggu" {{ request('status_berkas') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="ditolak" {{ request('status_berkas') === 'ditolak' ? 'selected' : '' }}>Ada yang Ditolak</option>
        </select>
    </div>

    <div class="flex items-center gap-1.5">
        <flux:button type="submit" variant="primary" size="sm">
            Filter
        </flux:button>
        @if(request()->hasAny(['q', 'gelombang_id', 'jurusan_id', 'jalur_pendaftaran', 'status_berkas']))
            <flux:button href="{{ route('admin.pendaftar.index') }}" variant="subtle" size="sm" title="Reset Filter">
                Reset
            </flux:button>
        @endif
    </div>
</form>

{{-- Tabel Data Pendaftar Menggunakan Flux Card --}}
<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Calon Siswa PPDB ({{ $pendaftarList->total() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama Lengkap</th>
                    <th>Jurusan</th>
                    <th>Jalur & Gelombang</th>
                    <th>Benefit SPP</th>
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
                        <td class="text-slate-500 font-semibold">{{ $pendaftarList->firstItem() + $index }}</td>
                        <td>
                            <strong class="text-blue-600 font-mono text-xs">{{ $item->nomor_pendaftaran }}</strong>
                            <div class="text-xs text-slate-500 mt-0.5">
                                Urut #{{ $item->nomor_urut }} &bull; {{ ucfirst($item->jalur_registrasi) }}
                            </div>
                        </td>
                        <td>
                            <div class="font-bold text-slate-900">{{ $item->nama_lengkap }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                NIK: {{ $item->nik }} &bull; {{ $item->asal_sekolah }}
                            </div>
                        </td>
                        <td>
                            <flux:badge color="blue" size="sm">
                                {{ $item->jurusan->kode ?? '-' }}
                            </flux:badge>
                        </td>
                        <td>
                            <div class="font-semibold text-slate-800 text-xs">{{ ucfirst($item->jalur_pendaftaran) }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $item->gelombang->nama_gelombang ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @if($item->status_benefit === 'klaim_aktif')
                                <flux:badge color="emerald" size="sm">
                                    Bebas SPP (Aktif)
                                </flux:badge>
                            @elseif($item->status_benefit === 'kandidat_100_pertama')
                                <flux:badge color="amber" size="sm">
                                    Kandidat 100
                                </flux:badge>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td>
                            @if($statusRingkas === 'disetujui')
                                <flux:badge color="emerald" size="sm">Disetujui</flux:badge>
                            @elseif($statusRingkas === 'menunggu')
                                <flux:badge color="amber" size="sm">Menunggu</flux:badge>
                            @elseif($statusRingkas === 'ditolak')
                                <flux:badge color="rose" size="sm">Ditolak</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Belum Lengkap</flux:badge>
                            @endif
                        </td>
                        <td>
                            @if($item->pengambilanSeragam)
                                <flux:badge color="emerald" size="sm">Sudah Ambil</flux:badge>
                            @elseif($item->isSeragamClearanceApproved())
                                <flux:badge color="teal" size="sm">Siap Ambil</flux:badge>
                            @else
                                <span class="text-xs text-slate-400">Terkunci</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <flux:button href="{{ route('admin.pendaftar.show', $item->id) }}" variant="subtle" size="xs" icon-trailing="arrow-right">
                                Detail
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-slate-500 py-8">
                            Tidak ada data calon siswa yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3.5 border-t border-slate-100 bg-white">
        {{ $pendaftarList->links() }}
    </div>
</flux:card>
</div>
@endsection
