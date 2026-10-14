@extends('layouts.app')

@section('title', 'Kabar & Berita')
@section('meta_description', 'Informasi dan berita terkini kegiatan belajar mengajar serta agenda SMK Muhammadiyah Paguyangan Brebes.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Warta Sekolah</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Kabar & Berita Terkini</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Seputar aktivitas pembelajaran, kegiatan kejuruan, dan agenda resmi SMK Muhammadiyah Paguyangan.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container">
        {{-- Formulir Pencarian Berita --}}
        <div style="margin-bottom: 2.5rem; max-width: 500px;">
            <form action="{{ route('berita') }}" method="GET" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <input type="text" name="q" value="{{ $cari ?? '' }}" placeholder="Cari judul atau isi berita..." class="form-control" style="flex: 1; min-width: 200px;">
                <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Cari Berita
                    </button>
                    @if($cari)
                        <a href="{{ route('berita') }}" class="btn btn-secondary btn-sm" title="Reset Pencarian">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
            @if($cari)
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">
                    Menampilkan hasil pencarian untuk: <strong>"{{ $cari }}"</strong>
                </div>
            @endif
        </div>

        {{-- Grid Berita --}}
        <div class="card-grid-3">
            @forelse($beritaList as $berita)
                <article class="card">
                    <div class="card-img-frame">
                        <img src="{{ $berita->gambar_url ?? asset('images/gambargedungbaru.jpg') }}" alt="{{ $berita->judul }}" class="card-img" onerror="this.onerror=null; this.src='{{ asset('images/gambargedungbaru.jpg') }}';">
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>{{ $berita->tanggal_terbit->translatedFormat('d F Y') }}</span>
                            <span>•</span>
                            <span>{{ $berita->penulis ?? 'Humas' }}</span>
                        </div>
                        <h2 class="card-title" style="font-size: 1.125rem;">
                            <a href="{{ route('berita.detail', $berita->slug) }}">{{ $berita->judul }}</a>
                        </h2>
                        <p class="card-text">
                            {{ Str::limit($berita->ringkasan, 130) }}
                        </p>
                        <div class="card-footer">
                            <a href="{{ route('berita.detail', $berita->slug) }}" class="btn btn-outline btn-sm">
                                Baca Artikel
                            </a>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $berita->views_count }}x dibaca
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                <p style="color: var(--text-muted); grid-column: 1 / -1;">Tidak ditemukan artikel berita yang sesuai.</p>
            @endforelse
        </div>

        {{-- Paginasi --}}
        <div class="pagination-wrapper">
            {{ $beritaList->links() }}
        </div>
    </div>
</section>
@endsection
