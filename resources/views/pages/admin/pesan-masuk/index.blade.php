@extends('layouts.admin')

@section('title', 'Pesan Masuk Kontak — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Pesan Masuk Pengunjung')
@section('header-subtitle', 'Daftar pesan dan pertanyaan yang dikirim masyarakat melalui formulir kontak website')

@section('content')
<!-- Filter Bar -->
<div class="admin-filter-bar">
    <form action="{{ route('admin.pesan-masuk.index') }}" method="GET" style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <label class="form-label" style="margin: 0;">Status Pesan:</label>
            <select name="status" class="form-select" onchange="this.form.submit()" style="width: auto; min-width: 180px;">
                <option value="">Semua Status</option>
                <option value="belum_dibaca" {{ request('status') === 'belum_dibaca' ? 'selected' : '' }}>Belum Dibaca</option>
                <option value="sudah_dibaca" {{ request('status') === 'sudah_dibaca' ? 'selected' : '' }}>Sudah Dibaca</option>
                <option value="dibalas" {{ request('status') === 'dibalas' ? 'selected' : '' }}>Sudah Dibalas</option>
            </select>
        </div>
        <div>
            @if(request()->hasAny(['status']))
                <a href="{{ route('admin.pesan-masuk.index') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
            @endif
        </div>
    </form>
</div>

<!-- Data Table Card -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Daftar Pesan Masuk ({{ $pesanList->total() }})</h3>
    </div>
    
    @if($pesanList->count() > 0)
        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">Waktu Kirim</th>
                        <th style="width: 170px;">Pengirim</th>
                        <th style="width: 160px;">Kontak</th>
                        <th>Subjek & Pesan</th>
                        <th style="width: 130px; text-align: center;">Status</th>
                        <th style="width: 200px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanList as $pesan)
                        <tr style="{{ $pesan->status === 'belum_dibaca' ? 'background-color: #f8fafc;' : '' }}">
                            <td style="font-size: 0.775rem; color: #64748b; white-space: nowrap;">
                                <strong style="color: #092347;">{{ $pesan->created_at->format('d/m/Y') }}</strong><br>
                                <span>{{ $pesan->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <strong style="color: #092347; font-size: 0.9rem;">{{ $pesan->nama }}</strong>
                            </td>
                            <td style="font-size: 0.8rem;">
                                <div><a href="mailto:{{ $pesan->email }}" style="color: #0b57d0; text-decoration: none; font-weight: 500;">{{ $pesan->email }}</a></div>
                                @if($pesan->telepon)
                                    <div style="margin-top: 3px;">
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pesan->telepon)) }}" target="_blank" rel="noopener" style="color: #16a34a; text-decoration: none; font-weight: 600;">
                                            WA: {{ $pesan->telepon }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #092347; margin-bottom: 4px; font-size: 0.875rem;">{{ $pesan->subjek }}</div>
                                <div style="color: #475569; font-size: 0.825rem; line-height: 1.5; white-space: pre-wrap;">{{ $pesan->pesan }}</div>
                            </td>
                            <td style="text-align: center;">
                                @if($pesan->status === 'belum_dibaca')
                                    <span class="badge-status badge-status-warning">
                                        <span class="badge-status-dot"></span>
                                        Belum Dibaca
                                    </span>
                                @elseif($pesan->status === 'sudah_dibaca')
                                    <span class="badge-status badge-status-info">
                                        <span class="badge-status-dot"></span>
                                        Sudah Dibaca
                                    </span>
                                @else
                                    <span class="badge-status badge-status-success">
                                        <span class="badge-status-dot"></span>
                                        Dibalas
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                    <form action="{{ route('admin.pesan-masuk.update-status', $pesan->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select" onchange="this.form.submit()" style="font-size: 0.775rem; padding: 4px 8px; width: auto; height: 32px;">
                                            <option value="belum_dibaca" {{ $pesan->status === 'belum_dibaca' ? 'selected' : '' }}>Tandai Baru</option>
                                            <option value="sudah_dibaca" {{ $pesan->status === 'sudah_dibaca' ? 'selected' : '' }}>Tandai Dibaca</option>
                                            <option value="dibalas" {{ $pesan->status === 'dibalas' ? 'selected' : '' }}>Tandai Dibalas</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('admin.pesan-masuk.destroy', $pesan->id) }}" method="POST" onsubmit="return confirm('Hapus pesan dari {{ addslashes($pesan->nama) }}?');" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-delete" title="Hapus Pesan">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9;">
            {{ $pesanList->links() }}
        </div>
    @else
        <div style="text-align: center; color: #64748b; padding: 3rem 1rem;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin-bottom: 0.5rem;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            <p style="margin: 0; font-size: 0.9rem;">Belum ada pesan masuk dari pengunjung website.</p>
        </div>
    @endif
</div>
@endsection
