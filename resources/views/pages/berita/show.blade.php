@extends('layouts.app')

@section('title', $berita->judul)
@section('meta_description', Str::limit(strip_tags($berita->ringkasan ?? $berita->isi), 150))

@section('content')
<section style="background-color: var(--canvas-bg); padding: 1.5rem 0; border-bottom: 1px solid var(--border-default);">
    <div class="container container-narrow">
        <nav aria-label="Breadcrumb" style="font-size: 0.8125rem; color: var(--text-muted); display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('berita') }}">Berita</a>
            <span>/</span>
            <span style="color: var(--text-heading); font-weight: 600;">{{ Str::limit($berita->judul, 40) }}</span>
        </nav>
    </div>
</section>

<section class="py-section bg-white">
    <div class="container container-narrow">
        <article>
            <div class="article-header">
                <span class="badge badge-primary" style="margin-bottom: 0.75rem;">Warta Sekolah</span>
                <h1 class="article-title">{{ $berita->judul }}</h1>
                <div class="article-meta-bar">
                    <span><strong>Tanggal:</strong> {{ $berita->tanggal_terbit->translatedFormat('l, d F Y') }}</span>
                    <span>•</span>
                    <span><strong>Penulis:</strong> {{ $berita->penulis ?? 'Humas SMK Muhammadiyah Paguyangan' }}</span>
                    <span>•</span>
                    <span>{{ $berita->views_count }} kali dilihat</span>
                </div>
            </div>

            @if($berita->gambar_url)
                <div class="article-feature-image">
                    <img src="{{ $berita->gambar_url }}" alt="{{ $berita->judul }}" onerror="this.onerror=null; this.src='{{ asset('images/gambargedungbaru.jpg') }}';">
                </div>
            @endif

            <div class="article-prose">
                {!! $berita->isi !!}
            </div>

            <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--border-default); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <a href="{{ route('berita') }}" class="btn btn-secondary btn-sm">
                    ← Kembali ke Semua Berita
                </a>
                <span style="font-size: 0.8125rem; color: var(--text-muted);">
                    Kategori: Berita & Pengumuman Sekolah
                </span>
            </div>
        </article>

        {{-- Berita Lainnya --}}
        @if($beritaLainnya->isNotEmpty())
            <div style="margin-top: 4rem; padding-top: 2.5rem; border-top: 2px solid var(--border-default);">
                <h2 style="font-size: 1.375rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 1.5rem;">
                    Berita Terkini Lainnya
                </h2>
                <div class="card-grid-3">
                    @foreach($beritaLainnya as $item)
                        <div class="card">
                            <div class="card-img-frame">
                                <img src="{{ $item->gambar_url ?? asset('images/gambargedungbaru.jpg') }}" alt="{{ $item->judul }}" class="card-img" onerror="this.onerror=null; this.src='{{ asset('images/gambargedungbaru.jpg') }}';">
                            </div>
                            <div class="card-body">
                                <div class="card-meta">
                                    <span>{{ $item->tanggal_terbit->translatedFormat('d M Y') }}</span>
                                </div>
                                <h3 class="card-title" style="font-size: 0.9375rem;">
                                    <a href="{{ route('berita.detail', $item->slug) }}">{{ $item->judul }}</a>
                                </h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
