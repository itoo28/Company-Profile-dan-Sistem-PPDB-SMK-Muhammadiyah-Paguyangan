@extends('layouts.app')

@section('title', $prestasi->nama_prestasi)
@section('meta_description', Str::limit(strip_tags($prestasi->deskripsi), 150))

@section('content')
<section style="background-color: var(--canvas-bg); padding: 1.5rem 0; border-bottom: 1px solid var(--border-default);">
    <div class="container container-narrow">
        <nav aria-label="Breadcrumb" style="font-size: 0.8125rem; color: var(--text-muted); display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('prestasi') }}">Prestasi</a>
            <span>/</span>
            <span style="color: var(--text-heading); font-weight: 600;">{{ Str::limit($prestasi->nama_prestasi, 40) }}</span>
        </nav>
    </div>
</section>

<section class="py-section bg-white">
    <div class="container container-narrow">
        <article>
            <div class="article-header">
                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem;">
                    <span class="badge badge-accent">{{ $prestasi->peringkat }}</span>
                    <span class="badge badge-primary">Tingkat {{ $prestasi->tingkat }}</span>
                </div>
                <h1 class="article-title">{{ $prestasi->nama_prestasi }}</h1>
                <div class="article-meta-bar">
                    <span><strong>Peraih / Penerima:</strong> {{ $prestasi->nama_peraih }}</span>
                    <span>•</span>
                    <span><strong>Waktu Perolehan:</strong> {{ $prestasi->tanggal_perolehan->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            @if($prestasi->foto_url)
                <div class="article-feature-image">
                    <img src="{{ $prestasi->foto_url }}" alt="{{ $prestasi->nama_prestasi }}" onerror="this.onerror=null; this.src='{{ asset('images/gambarutama2.jpeg') }}';">
                </div>
            @endif

            <div class="article-prose">
                <p>{{ $prestasi->deskripsi }}</p>
            </div>

            <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--border-default); display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('prestasi') }}" class="btn btn-secondary btn-sm">
                    ← Kembali ke Daftar Prestasi
                </a>
            </div>
        </article>
    </div>
</section>
@endsection
