@extends('layouts.admin')

@section('title', 'Log Audit Aktivitas — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Log Aktivitas Sistem')
@section('header-subtitle', 'Jejak audit kronologis tindakan operasional, verifikasi berkas, dan pendaftaran')

@section('content')
<!-- Filter Pencarian Log -->
<div class="admin-filter-bar">
    <form action="{{ route('admin.log.index') }}" method="GET" class="admin-filter-form">
        <div class="admin-filter-group">
            <div class="search-input-wrapper">
                <input type="text" id="q" name="q" class="form-control text-xs h-9" placeholder="Cari nama pelaku, jenis tindakan, modul, atau keterangan..." value="{{ $cari }}">
            </div>
            <div class="admin-filter-actions">
                <flux:button type="submit" variant="primary" size="sm">
                    Cari
                </flux:button>
                @if($cari)
                    <flux:button href="{{ route('admin.log.index') }}" variant="subtle" size="sm">
                        Reset
                    </flux:button>
                @endif
            </div>
        </div>
        <div class="admin-filter-total text-xs text-slate-500 font-semibold">
            Total: <span class="text-slate-900 font-bold">{{ $logs->total() }}</span> Riwayat Log
        </div>
    </form>
</div>

<!-- Tabel Data Log Aktivitas -->
<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 m-0">Riwayat Log Audit ({{ $logs->total() }})</h3>
    </div>

    @if($logs->count() > 0)
        <div class="admin-desktop-table overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 140px;">Waktu & Tanggal</th>
                        <th style="width: 160px;">Pelaku</th>
                        <th style="width: 140px;">Tindakan / Aksi</th>
                        <th style="width: 140px;">Modul / Objek</th>
                        <th>Keterangan Aktivitas</th>
                        <th style="width: 120px;">Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td class="text-xs text-slate-500 whitespace-nowrap">
                                <strong class="text-slate-900">{{ $log->created_at->format('d/m/Y') }}</strong><br>
                                <span>{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            <td>
                                <strong class="text-slate-900">{{ $log->nama_pelaku }}</strong>
                            </td>
                            <td>
                                <flux:badge color="blue" size="sm" class="font-mono text-xs">
                                    {{ $log->jenis_aksi }}
                                </flux:badge>
                            </td>
                            <td class="font-semibold text-slate-700 text-xs">
                                {{ $log->objek }}
                            </td>
                            <td class="text-xs text-slate-700 leading-relaxed">
                                {{ $log->keterangan }}
                            </td>
                            <td class="text-xs font-mono text-slate-500">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tampilan Kartu Mobile Android -->
        <div class="admin-mobile-cards p-3 flex flex-col gap-3">
            @foreach($logs as $log)
                <div class="admin-data-card p-3.5 bg-white rounded-lg border border-slate-200 shadow-xs">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <strong class="text-sm text-slate-900 block">{{ $log->nama_pelaku }}</strong>
                            <div class="text-xs text-slate-500">
                                <span>{{ $log->created_at->format('d/m/Y H:i:s') }} WIB</span>
                            </div>
                        </div>
                        <flux:badge color="blue" size="sm" class="font-mono text-xs">
                            {{ $log->jenis_aksi }}
                        </flux:badge>
                    </div>

                    <div class="text-xs font-semibold text-slate-800 mb-1">
                        Modul: <span class="text-blue-600">{{ $log->objek }}</span>
                    </div>

                    <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-2.5 rounded border border-slate-100 mb-2">
                        {{ $log->keterangan }}
                    </div>

                    <div class="text-[11px] text-slate-400 font-mono text-right">
                        IP: {{ $log->ip_address ?? '-' }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-5 py-3.5 border-t border-slate-100 bg-white">
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center text-slate-500 py-12 px-4">
            <p class="m-0 text-sm">Belum ada riwayat aktivitas yang tercatat sesuai filter.</p>
        </div>
    @endif
</flux:card>
@endsection
