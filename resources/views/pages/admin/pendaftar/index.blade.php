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
<form method="GET" action="{{ route('admin.pendaftar.index') }}" class="admin-filter-bar flex flex-col gap-3">
    <div class="flex items-center gap-2 w-full flex-wrap sm:flex-nowrap">
        <div class="flex-1 w-full min-w-0">
            <input type="text" 
                   name="q" 
                   value="{{ request('q') }}" 
                   class="form-control text-xs h-9 w-full" 
                   placeholder="Cari Nama, No. Pendaftaran, NIK, atau Asal Sekolah...">
        </div>
        <div class="flex items-center gap-1.5 w-full sm:w-auto">
            <flux:button type="submit" variant="primary" size="sm" class="flex-1 sm:flex-none">
                Filter
            </flux:button>
            @if(request()->hasAny(['q', 'gelombang_id', 'jurusan_id', 'jalur_pendaftaran', 'status_berkas']))
                <flux:button href="{{ route('admin.pendaftar.index') }}" variant="subtle" size="sm" class="flex-1 sm:flex-none" title="Reset Filter">
                    Reset
                </flux:button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 w-full">
        <div>
            <select name="tahun_ajaran_id" class="form-select text-xs h-9 w-full font-semibold bg-blue-50/50 border-blue-200 text-blue-900" onchange="this.form.submit()">
                @foreach($semuaTahunAjaran as $ta)
                    <option value="{{ $ta->id }}" {{ $tahunAjaran->id == $ta->id ? 'selected' : '' }}>
                        Periode {{ $ta->nama }} {{ $ta->is_aktif ? '✓ (Aktif)' : '(Arsip)' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="gelombang_id" class="form-select text-xs h-9 w-full">
                <option value="">Semua Gelombang</option>
                @foreach($gelombangs as $g)
                    <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>
                        {{ $g->nama_gelombang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="jurusan_id" class="form-select text-xs h-9 w-full">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                        {{ $j->kode }} - {{ $j->nama_jurusan }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="jalur_pendaftaran" class="form-select text-xs h-9 w-full">
                <option value="">Semua Jalur</option>
                <option value="umum" {{ request('jalur_pendaftaran') === 'umum' ? 'selected' : '' }}>Umum</option>
                <option value="prestasi" {{ request('jalur_pendaftaran') === 'prestasi' ? 'selected' : '' }}>Prestasi</option>
            </select>
        </div>

        <div>
            <select name="status_berkas" class="form-select text-xs h-9 w-full">
                <option value="">Semua Status Berkas</option>
                <option value="menunggu" {{ request('status_berkas') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="ditolak" {{ request('status_berkas') === 'ditolak' ? 'selected' : '' }}>Ada yang Ditolak</option>
            </select>
        </div>
    </div>
</form>

{{-- Banner Indikator jika melihat Arsip Tahun Lalu --}}
@if(!$tahunAjaran->is_aktif)
    <div class="mb-4 p-3.5 bg-amber-50 border border-amber-200 rounded-lg flex items-center justify-between gap-3 text-xs text-amber-900">
        <div class="flex items-center gap-2">
            <flux:icon name="archive-box" class="size-4 text-amber-700 shrink-0" />
            <span>
                Anda sedang melihat <strong>Arsip Data Calon Siswa Tahun Ajaran {{ $tahunAjaran->nama }}</strong>. Periode operasional berjalan saat ini adalah: <strong>{{ $tahunAjaranAktif->nama ?? '-' }}</strong>.
            </span>
        </div>
        <a href="{{ route('admin.pendaftar.index') }}" class="font-bold text-amber-800 underline hover:text-amber-950 shrink-0">
            Kembali ke Periode Aktif &rarr;
        </a>
    </div>
@endif

{{-- Tabel Data Pendaftar Menggunakan Flux Card --}}
<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Calon Siswa PPDB ({{ $pendaftarList->total() }})</h3>
    </div>

    {{-- Mode Desktop: Tabel Lengkap --}}
    <div class="admin-desktop-table">
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <flux:button href="{{ route('admin.pendaftar.show', $item->id) }}" variant="subtle" size="xs" icon-trailing="arrow-right">
                                        Detail
                                    </flux:button>
                                    @if(auth('web')->user()->isSuperAdmin())
                                        <form action="{{ route('admin.pendaftar.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data calon siswa {{ addslashes($item->nama_lengkap) }} ({{ $item->nomor_pendaftaran }})?');" class="inline-flex m-0 p-0 items-center">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" variant="danger" size="xs" icon="trash" title="Hapus Data Calon Siswa">
                                                Hapus
                                            </flux:button>
                                        </form>
                                    @endif
                                </div>
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
    </div>

    {{-- Mode Mobile: Card View Ramah Sentuhan di Smartphone Android --}}
    <div class="admin-mobile-cards">
        @forelse($pendaftarList as $index => $item)
            @php
                $statusRingkas = $item->status_berkas_ringkas;
            @endphp
            <div class="admin-data-card">
                <div class="admin-data-card-header">
                    <div>
                        <span class="text-blue-600 font-mono text-xs font-bold">{{ $item->nomor_pendaftaran }}</span>
                        <span class="text-xs text-slate-400">&bull; Urut #{{ $item->nomor_urut }}</span>
                        <h4 class="font-bold text-slate-900 text-sm m-0 mt-0.5">{{ $item->nama_lengkap }}</h4>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $item->asal_sekolah }}</div>
                    </div>
                    <flux:badge color="blue" size="sm">
                        {{ $item->jurusan->kode ?? '-' }}
                    </flux:badge>
                </div>

                <div class="admin-data-card-badges">
                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                        {{ ucfirst($item->jalur_pendaftaran) }} &bull; {{ $item->gelombang->nama_gelombang ?? '-' }}
                    </span>

                    @if($item->status_benefit === 'klaim_aktif')
                        <flux:badge color="emerald" size="sm">Bebas SPP (Aktif)</flux:badge>
                    @elseif($item->status_benefit === 'kandidat_100_pertama')
                        <flux:badge color="amber" size="sm">Kandidat 100</flux:badge>
                    @endif

                    @if($statusRingkas === 'disetujui')
                        <flux:badge color="emerald" size="sm">Berkas Disetujui</flux:badge>
                    @elseif($statusRingkas === 'menunggu')
                        <flux:badge color="amber" size="sm">Berkas Menunggu</flux:badge>
                    @elseif($statusRingkas === 'ditolak')
                        <flux:badge color="rose" size="sm">Berkas Ditolak</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">Berkas Belum Lengkap</flux:badge>
                    @endif

                    @if($item->pengambilanSeragam)
                        <flux:badge color="emerald" size="sm">Seragam Diambil</flux:badge>
                    @elseif($item->isSeragamClearanceApproved())
                        <flux:badge color="teal" size="sm">Seragam Siap Ambil</flux:badge>
                    @endif
                </div>

                <div class="admin-data-card-footer">
                    <span class="text-xs text-slate-500">NIK: {{ $item->nik }}</span>
                    <div class="flex items-center gap-1.5">
                        <flux:button href="{{ route('admin.pendaftar.show', $item->id) }}" variant="subtle" size="sm" icon-trailing="arrow-right">
                            Periksa
                        </flux:button>
                        @if(auth('web')->user()->isSuperAdmin())
                            <form action="{{ route('admin.pendaftar.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data calon siswa {{ addslashes($item->nama_lengkap) }}?');" class="inline-flex m-0 p-0 items-center">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" variant="danger" size="sm" icon="trash">
                                    Hapus
                                </flux:button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-slate-500 py-8 px-4 text-xs">
                Tidak ada data calon siswa yang sesuai dengan filter pencarian.
            </div>
        @endforelse
    </div>
    <div class="px-5 py-3.5 border-t border-slate-100 bg-white">
        {{ $pendaftarList->links() }}
    </div>
</flux:card>
</div>
@endsection
