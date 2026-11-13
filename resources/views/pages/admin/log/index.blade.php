@extends('layouts.admin')

@section('title', 'Log Audit Aktivitas — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Log Aktivitas Sistem')
@section('header-subtitle', 'Jejak audit kronologis tindakan operasional, verifikasi berkas, dan pendaftaran')

@section('content')
{{-- 1. Kartu Ringkasan Metrik / Quick KPI --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <flux:card class="p-4 bg-white border border-slate-200">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Jejak Audit</div>
        <div class="text-2xl font-extrabold text-slate-800">{{ number_format($totalLogs) }}</div>
        <div class="text-[11px] text-slate-400 mt-1">Seluruh riwayat tercatat</div>
    </flux:card>

    <flux:card class="p-4 bg-white border border-blue-200 bg-blue-50/20">
        <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Aktivitas Hari Ini</div>
        <div class="text-2xl font-extrabold text-blue-700">{{ number_format($todayLogs) }}</div>
        <div class="text-[11px] text-blue-600/80 mt-1">Tindakan staf & sistem hari ini</div>
    </flux:card>

    <flux:card class="p-4 bg-white border border-emerald-200 bg-emerald-50/20">
        <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">PPDB & Keuangan</div>
        <div class="text-2xl font-extrabold text-emerald-700">{{ number_format($ppdbLogs) }}</div>
        <div class="text-[11px] text-emerald-600/80 mt-1">Verifikasi, bayar, & seragam</div>
    </flux:card>

    <flux:card class="p-4 bg-white border border-rose-200 bg-rose-50/20">
        <div class="text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">Peringatan Keamanan</div>
        <div class="text-2xl font-extrabold text-rose-700">{{ number_format($securityAlerts) }}</div>
        <div class="text-[11px] text-rose-600/80 mt-1">Gagal login / akses ditolak</div>
    </flux:card>
</div>

{{-- 2. Bilah Filter Multi-Kriteria Responsif --}}
<div class="admin-filter-bar mb-4">
    <form action="{{ route('admin.log.index') }}" method="GET" class="admin-filter-form">
        <div class="admin-filter-group flex-wrap gap-2.5">
            {{-- Input Pencarian Bebas --}}
            <div class="search-input-wrapper flex-1" style="min-width: 220px;">
                <input type="text" 
                       id="q" 
                       name="q" 
                       class="form-control text-xs h-9" 
                       placeholder="Cari pelaku, aksi, modul, ket, atau IP..." 
                       value="{{ request('q') }}">
            </div>

            {{-- Filter Modul / Objek --}}
            <div class="filter-select-wrapper" style="min-width: 170px;">
                <select name="objek" class="form-select text-xs h-9" onchange="this.form.submit()">
                    <option value="">Semua Modul / Objek</option>
                    @foreach($daftarModul as $modul)
                        <option value="{{ $modul }}" {{ request('objek') === $modul ? 'selected' : '' }}>
                            {{ $modul }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori / Tipe Tindakan --}}
            <div class="filter-select-wrapper" style="min-width: 190px;">
                <select name="kategori_aksi" class="form-select text-xs h-9" onchange="this.form.submit()">
                    <option value="">Semua Tipe Tindakan</option>
                    @foreach($kategoriAksiList as $val => $label)
                        <option value="{{ $val }}" {{ request('kategori_aksi') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Peran Pelaku --}}
            <div class="filter-select-wrapper" style="min-width: 150px;">
                <select name="peran" class="form-select text-xs h-9" onchange="this.form.submit()">
                    <option value="">Semua Peran</option>
                    @foreach($peranList as $val => $label)
                        <option value="{{ $val }}" {{ request('peran') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Rentang Tanggal --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <div class="flex items-center gap-1 text-xs text-slate-500">
                    <span class="font-medium text-[11px]">Dari:</span>
                    <input type="date" 
                           name="tanggal_mulai" 
                           value="{{ request('tanggal_mulai') }}" 
                           class="form-control text-xs h-9 py-1 px-2" 
                           style="width: 135px;"
                           onchange="this.form.submit()">
                </div>
                <div class="flex items-center gap-1 text-xs text-slate-500">
                    <span class="font-medium text-[11px]">s.d:</span>
                    <input type="date" 
                           name="tanggal_selesai" 
                           value="{{ request('tanggal_selesai') }}" 
                           class="form-control text-xs h-9 py-1 px-2" 
                           style="width: 135px;"
                           onchange="this.form.submit()">
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="admin-filter-actions">
                <flux:button type="submit" variant="primary" size="sm">
                    Terapkan
                </flux:button>
                @if(request()->hasAny(['q', 'objek', 'kategori_aksi', 'peran', 'tanggal_mulai', 'tanggal_selesai']))
                    <flux:button href="{{ route('admin.log.index') }}" variant="subtle" size="sm">
                        Reset
                    </flux:button>
                @endif
            </div>
        </div>

        <div class="admin-filter-total text-xs text-slate-500 font-semibold mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
            <span>Menampilkan <strong>{{ $logs->count() }}</strong> dari total <strong>{{ $logs->total() }}</strong> riwayat aktivitas</span>
            @if(request()->hasAny(['q', 'objek', 'kategori_aksi', 'peran', 'tanggal_mulai', 'tanggal_selesai']))
                <span class="inline-flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-[11px]">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Filter Aktif
                </span>
            @endif
        </div>
    </form>
</div>

{{-- 3. Tabel Data Log Aktivitas --}}
<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <div>
            <h3 class="text-sm font-bold text-slate-900 m-0">Riwayat Jejak Audit Sistem ({{ $logs->total() }})</h3>
            <p class="text-xs text-slate-500 m-0 mt-0.5">Seluruh rekaman tindakan tervalidasi dengan stempel waktu dan identitas pelaksana</p>
        </div>
        <span class="text-xs text-slate-500 font-mono">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() ?: 1 }}</span>
    </div>

    @if($logs->count() > 0)
        {{-- Tampilan Desktop --}}
        <div class="admin-desktop-table overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 150px;">Waktu & Tanggal</th>
                        <th style="width: 170px;">Pelaku & Peran</th>
                        <th style="width: 160px;">Tindakan / Aksi</th>
                        <th style="width: 150px;">Modul / Objek</th>
                        <th>Keterangan Aktivitas</th>
                        <th style="width: 130px; text-align: right;">Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            {{-- Waktu & Tanggal --}}
                            <td class="text-xs text-slate-600 whitespace-nowrap">
                                <strong class="text-slate-900 font-semibold">{{ $log->created_at->translatedFormat('d M Y') }}</strong><br>
                                <span class="text-slate-500 font-mono text-[11px]">{{ $log->created_at->format('H:i:s') }} WIB</span>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Pelaku & Peran --}}
                            <td>
                                <strong class="text-slate-900 text-xs block leading-tight">{{ $log->nama_pelaku }}</strong>
                                <flux:badge color="{{ $log->badge_peran_color }}" size="sm" class="text-[10px] mt-1">
                                    {{ $log->peran_formatted }}
                                </flux:badge>
                            </td>

                            {{-- Tindakan / Aksi Semantik --}}
                            <td>
                                <flux:badge color="{{ $log->badge_color }}" size="sm" class="font-semibold text-xs whitespace-nowrap">
                                    {{ $log->nama_aksi_formatted }}
                                </flux:badge>
                                <div class="text-[10px] font-mono text-slate-400 mt-1 leading-none">{{ $log->jenis_aksi }}</div>
                            </td>

                            {{-- Modul / Objek --}}
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $log->objek ?? '-' }}
                                </span>
                            </td>

                            {{-- Keterangan Aktivitas --}}
                            <td class="text-xs text-slate-700 leading-relaxed">
                                <div class="max-w-xl">
                                    {{ $log->keterangan ?? '-' }}
                                </div>
                            </td>

                            {{-- Alamat IP --}}
                            <td style="text-align: right;">
                                <span class="text-[11px] font-mono text-slate-500 bg-slate-100/80 px-2 py-0.5 rounded border border-slate-200">
                                    {{ $log->ip_address ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tampilan Kartu Mobile Smartphone Android --}}
        <div class="admin-mobile-cards p-3 flex flex-col gap-3">
            @foreach($logs as $log)
                <div class="admin-data-card p-3.5 bg-white rounded-lg border border-slate-200 shadow-xs">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <strong class="text-sm text-slate-900 block">{{ $log->nama_pelaku }}</strong>
                            <div class="flex items-center gap-1.5 mt-1">
                                <flux:badge color="{{ $log->badge_peran_color }}" size="sm" class="text-[10px]">
                                    {{ $log->peran_formatted }}
                                </flux:badge>
                                <span class="text-[11px] text-slate-400 font-mono">&bull; {{ $log->created_at->format('H:i') }} WIB</span>
                            </div>
                        </div>
                        <flux:badge color="{{ $log->badge_color }}" size="sm" class="font-semibold text-xs whitespace-nowrap">
                            {{ $log->nama_aksi_formatted }}
                        </flux:badge>
                    </div>

                    <div class="text-xs font-semibold text-slate-800 mb-1.5 flex items-center gap-1.5">
                        <span class="text-slate-500 font-normal">Modul:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $log->objek ?? '-' }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-2.5 rounded-lg border border-slate-100 mb-2">
                        {{ $log->keterangan ?? '-' }}
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono pt-1.5 border-t border-slate-100">
                        <span>{{ $log->created_at->translatedFormat('d M Y') }} ({{ $log->created_at->diffForHumans() }})</span>
                        <span>IP: {{ $log->ip_address ?? '-' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Navigasi Paginasi --}}
        <div class="px-5 py-3.5 border-t border-slate-100 bg-white">
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center text-slate-500 py-12 px-4">
            <svg class="size-10 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="m-0 text-sm font-semibold text-slate-700">Tidak ada data log audit yang sesuai dengan filter.</p>
            <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau bersihkan parameter filter Anda.</p>
            @if(request()->hasAny(['q', 'objek', 'kategori_aksi', 'peran', 'tanggal_mulai', 'tanggal_selesai']))
                <div class="mt-3">
                    <flux:button href="{{ route('admin.log.index') }}" variant="subtle" size="sm">
                        Reset Semua Filter
                    </flux:button>
                </div>
            @endif
        </div>
    @endif
</flux:card>
@endsection
