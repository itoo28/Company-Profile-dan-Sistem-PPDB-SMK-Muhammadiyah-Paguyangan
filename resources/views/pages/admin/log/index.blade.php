@extends('layouts.admin')

@section('title', 'Log Audit Aktivitas — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Log Aktivitas Sistem')
@section('header-subtitle', 'Jejak audit kronologis tindakan operasional, verifikasi berkas, dan pendaftaran')

@section('content')
<!-- Filter Pencarian Log -->
<div class="admin-filter-bar">
    <form action="{{ route('admin.log.index') }}" method="GET" style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 280px;">
            <input type="text" id="q" name="q" class="form-control" placeholder="Cari nama pelaku, jenis tindakan, modul, atau keterangan..." value="{{ $cari }}">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </div>
        <div>
            @if($cari)
                <a href="{{ route('admin.log.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Data Log Aktivitas -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Riwayat Log Audit ({{ $logs->total() }})</h3>
    </div>

    @if($logs->count() > 0)
        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
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
                            <td style="font-size: 0.775rem; color: #64748b; white-space: nowrap;">
                                <strong style="color: #092347;">{{ $log->created_at->format('d/m/Y') }}</strong><br>
                                <span>{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            <td>
                                <strong style="color: #092347; font-size: 0.875rem;">{{ $log->nama_pelaku }}</strong>
                            </td>
                            <td>
                                <span class="badge-status badge-status-info" style="font-family: monospace; font-size: 0.75rem;">
                                    {{ $log->jenis_aksi }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #475569; font-size: 0.825rem;">
                                {{ $log->objek }}
                            </td>
                            <td style="font-size: 0.825rem; color: #1e293b; line-height: 1.4;">
                                {{ $log->keterangan }}
                            </td>
                            <td style="font-size: 0.775rem; font-family: monospace; color: #64748b;">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9;">
            {{ $logs->links() }}
        </div>
    @else
        <div style="text-align: center; color: #64748b; padding: 3rem 1rem;">
            <p style="margin: 0; font-size: 0.9rem;">Belum ada riwayat aktivitas yang tercatat sesuai filter.</p>
        </div>
    @endif
</div>
@endsection
