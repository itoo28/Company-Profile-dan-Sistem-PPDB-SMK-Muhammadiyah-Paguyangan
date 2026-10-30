@extends('layouts.admin')

@section('title', 'Kelola Prestasi Siswa & Sekolah — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Prestasi Siswa & Sekolah')
@section('header-subtitle', 'Kelola arsip capaian kejuaraan, penghargaan, dan prestasi akademik maupun non-akademik')

@section('header-action')
    <flux:button href="{{ route('admin.prestasi.create') }}" variant="primary" icon="plus">
        Tambah Prestasi Baru
    </flux:button>
@endsection

@section('subnav')
    @include('partials.admin.warta-tabs')
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Filter & Pencarian Bar -->
    <div class="admin-filter-bar">
        <form action="{{ route('admin.prestasi.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-group">
                <div class="search-input-wrapper">
                    <input type="text" name="q" class="form-control form-control-search text-xs h-9" placeholder="Cari nama prestasi, peraih..." value="{{ $cari ?? '' }}">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                <div class="filter-select-wrapper">
                    <select name="status" class="form-select text-xs h-9" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="terbit" {{ request('status') === 'terbit' ? 'selected' : '' }}>Hanya Terbit</option>
                        <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Hanya Draf</option>
                    </select>
                </div>

                <div class="admin-filter-actions">
                    <flux:button type="submit" variant="primary" size="sm">
                        Cari
                    </flux:button>

                    @if(!empty($cari) || request('status'))
                        <flux:button href="{{ route('admin.prestasi.index') }}" variant="subtle" size="sm">
                            Reset
                        </flux:button>
                    @endif
                </div>
            </div>

            <div class="admin-filter-total text-xs text-slate-500 font-semibold">
                Total: <span class="text-slate-900 font-bold">{{ $prestasiList->total() }}</span> Prestasi
            </div>
        </form>
    </div>

    <!-- Data Table Card Menggunakan Flux Card -->
    <flux:card class="p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Arsip Capaian Kejuaraan</h3>
        </div>
        <div>
            @if($prestasiList->count() > 0)
                <div class="overflow-x-auto">
                    <table class="admin-table w-full">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: center;">Foto</th>
                                <th>Nama Kejuaraan / Prestasi</th>
                                <th style="width: 170px;">Peraih / Tim</th>
                                <th style="width: 140px;">Tingkat & Peringkat</th>
                                <th style="width: 120px;">Tgl Capai</th>
                                <th style="width: 110px; text-align: center;">Status</th>
                                <th style="width: 180px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prestasiList as $item)
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-thumb-box">
                                            @if($item->foto_url)
                                                <img src="{{ $item->foto_url }}" alt="{{ $item->nama_prestasi }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'table-thumb-placeholder\'>Foto</div>';">
                                            @else
                                                <div class="table-thumb-placeholder">Foto</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.95rem; font-weight: 700; color: #092347; margin-bottom: 4px; line-height: 1.35;">
                                            {{ $item->nama_prestasi }}
                                        </div>
                                        <div style="font-size: 0.8125rem; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $item->deskripsi }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #1e293b;">
                                            {{ $item->nama_peraih }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0b57d0; font-size: 0.85rem;">{{ $item->peringkat }}</div>
                                        <div class="mt-1">
                                            <flux:badge color="sky" size="sm">Tingkat {{ $item->tingkat }}</flux:badge>
                                        </div>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <div style="font-weight: 600; color: #092347;">
                                            {{ $item->tanggal_perolehan ? $item->tanggal_perolehan->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        @if($item->status === 'terbit')
                                            <flux:badge color="emerald" size="sm">Terbit</flux:badge>
                                        @else
                                            <flux:badge color="amber" size="sm">Draf</flux:badge>
                                        @endif
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <flux:button href="{{ route('admin.prestasi.edit', $item->id) }}" variant="subtle" size="sm" icon="pencil-square">
                                                Edit
                                            </flux:button>
                                            <form action="{{ route('admin.prestasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip prestasi &quot;{{ addslashes($item->nama_prestasi) }}&quot;?');" class="inline-flex m-0 p-0 items-center">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" variant="danger" size="sm" icon="trash">
                                                    Hapus
                                                </flux:button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9;">
                    {{ $prestasiList->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: #64748b;">
                    <div style="font-weight: 700; color: #092347; font-size: 1rem;">Tidak ada arsip prestasi ditemukan</div>
                    <p style="font-size: 0.85rem; margin: 4px 0 16px;">Silakan sesuaikan kata kunci pencarian atau tambah dokumentasi prestasi baru.</p>
                    <flux:button href="{{ route('admin.prestasi.create') }}" variant="primary" icon="plus">
                        Tambah Prestasi Pertama
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:card>
</div>
@endsection
