@extends('layouts.app')

@section('title', 'Prestasi Siswa')
@section('meta_description', 'Prestasi dan capaian penghargaan siswa SMK Muhammadiyah Paguyangan di bidang vokasi, kepanduan, dan olahraga.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Rekam Jejak Kejuaraan</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Prestasi & Penghargaan Siswa</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Apresiasi atas dedikasi dan ikhtiar peserta didik dalam mengharumkan nama sekolah dan persyarikatan.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container">
        {{-- Filter Tingkat --}}
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <a href="{{ route('prestasi') }}" class="btn btn-sm {{ !$tingkat ? 'btn-primary' : 'btn-secondary' }}">
                Semua Tingkat
            </a>
            @foreach($tingkatList as $itemTingkat)
                <a href="{{ route('prestasi', ['tingkat' => $itemTingkat]) }}" class="btn btn-sm {{ $tingkat === $itemTingkat ? 'btn-primary' : 'btn-secondary' }}">
                    {{ $itemTingkat }}
                </a>
            @endforeach
        </div>

        {{-- Grid Prestasi --}}
        <div class="card-grid-3">
            @forelse($prestasiList as $prestasi)
                <article class="prestasi-card">
                    {{-- Ambient Full-Bleed Media Cover & Scrim Gradient Minimalis --}}
                    <div class="prestasi-card-media-backdrop">
                        <img src="{{ $prestasi->foto_url ?? asset('images/gambarutama2.jpeg') }}" alt="{{ $prestasi->nama_prestasi }}" class="prestasi-card-bg-img" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/gambarutama2.jpeg') }}';">
                        <div class="prestasi-card-scrim"></div>
                    </div>

                    {{-- Floating Topbar (Peringkat & Tingkat) --}}
                    <div class="prestasi-card-topbar">
                        <span class="prestasi-rank-badge">{{ $prestasi->peringkat }}</span>
                        <span class="prestasi-level-badge">{{ $prestasi->tingkat }}</span>
                    </div>

                    {{-- Content Area Ringkas di Dasar Kartu --}}
                    <div class="prestasi-card-content">
                        <div class="prestasi-peraih-sub">
                            Peraih: {{ $prestasi->nama_peraih }}
                        </div>
                        <h2 class="prestasi-title" style="font-size: 1.25rem;">
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}">{{ $prestasi->nama_prestasi }}</a>
                        </h2>

                        <div class="prestasi-card-footer">
                            <span class="prestasi-card-date">{{ $prestasi->tanggal_perolehan->translatedFormat('F Y') }}</span>
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}" class="prestasi-card-link">
                                Detail Kejuaraan <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <p style="color: var(--text-muted); grid-column: 1 / -1;">Belum ada data prestasi untuk kategori ini.</p>
            @endforelse
        </div>

        <div class="pagination-wrapper">
            {{ $prestasiList->links() }}
        </div>
    </div>
</section>
@endsection
