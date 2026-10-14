@extends('layouts.admin')

@section('title', 'Kelola Warta & Berita — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Warta & Berita Sekolah')
@section('header-subtitle', 'Kelola artikel publikasi, pengumuman resmi, dan informasi kegiatan sekolah')

@section('header-action')
    <a href="{{ route('admin.berita.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-weight: 700;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tulis Berita Baru</span>
    </a>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Filter & Pencarian Bar -->
    <div class="admin-filter-bar">
        <form action="{{ route('admin.berita.index') }}" method="GET" style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 12px;">
            <div class="admin-filter-group">
                <div style="position: relative; min-width: 260px;">
                    <input type="text" name="q" class="form-control" placeholder="Cari judul berita..." value="{{ $cari ?? '' }}" style="padding-left: 36px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                <div style="min-width: 150px;">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="terbit" {{ request('status') === 'terbit' ? 'selected' : '' }}>Hanya Terbit</option>
                        <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Hanya Draf</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary" style="padding: 8px 14px;">Cari</button>

                @if(!empty($cari) || request('status'))
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-outline" style="padding: 8px 12px; font-size: 0.85rem; color: #64748b; border-color: #cbd5e1;">Reset</a>
                @endif
            </div>

            <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">
                Total: <span style="color: #092347; font-weight: 800;">{{ $beritaList->total() }}</span> Berita
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Daftar Publikasi Artikel & Warta</h3>
        </div>
        <div style="padding: 0;">
            @if($beritaList->count() > 0)
                <div class="admin-table-container" style="border: none; border-radius: 0;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: center;">Sampul</th>
                                <th>Informasi & Ringkasan Berita</th>
                                <th style="width: 130px;">Tanggal Terbit</th>
                                <th style="width: 110px; text-align: center;">Status</th>
                                <th style="width: 160px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($beritaList as $item)
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-thumb-box">
                                            @if($item->gambar_url)
                                                <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'table-thumb-placeholder\'>Warta</div>';">
                                            @else
                                                <div class="table-thumb-placeholder">Warta</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.95rem; font-weight: 700; color: #092347; margin-bottom: 4px; line-height: 1.35;">
                                            {{ $item->judul }}
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 12px; font-size: 0.775rem; color: #64748b; margin-bottom: 6px;">
                                            <span>Oleh: <strong style="color: #475569;">{{ $item->penulis ?? ($item->pembuat->name ?? 'Admin') }}</strong></span>
                                            <span>&bull;</span>
                                            <span>Dibaca: <strong style="color: #0b57d0;">{{ number_format($item->views_count ?? 0) }}x</strong></span>
                                        </div>
                                        @if($item->ringkasan)
                                            <div style="font-size: 0.8125rem; color: #475569; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $item->ringkasan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <div style="font-weight: 600; color: #092347;">
                                            {{ $item->tanggal_terbit ? $item->tanggal_terbit->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        @if($item->status === 'terbit')
                                            <span class="badge-status badge-status-success">
                                                <span class="badge-status-dot"></span>
                                                Terbit
                                            </span>
                                        @else
                                            <span class="badge-status badge-status-warning">
                                                <span class="badge-status-dot"></span>
                                                Draf
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.berita.edit', $item->id) }}" class="btn-action-edit" title="Sunting Berita">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                                </svg>
                                                <span>Edit</span>
                                            </a>
                                            <form action="{{ route('admin.berita.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita &quot;{{ addslashes($item->judul) }}&quot;?');" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-delete" title="Hapus Berita">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                    <span>Hapus</span>
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
                    {{ $beritaList->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: #64748b;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem;">
                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path>
                    </svg>
                    <div style="font-weight: 700; color: #092347; font-size: 1rem;">Tidak ada artikel berita ditemukan</div>
                    <p style="font-size: 0.85rem; margin: 4px 0 16px;">Silakan sesuaikan kata kunci pencarian atau buat publikasi berita baru.</p>
                    <a href="{{ route('admin.berita.create') }}" class="btn btn-primary btn-sm">+ Tulis Berita Pertama</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
