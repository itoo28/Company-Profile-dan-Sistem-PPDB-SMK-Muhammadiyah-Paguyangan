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
                <article class="card">
                    <div class="card-img-frame">
                        <img src="{{ $prestasi->foto_url ?? asset('images/gambarutama2.jpeg') }}" alt="{{ $prestasi->nama_prestasi }}" class="card-img" onerror="this.onerror=null; this.src='{{ asset('images/gambarutama2.jpeg') }}';">
                        <span style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--primary-deep); color: #ffffff; padding: 0.25rem 0.625rem; font-size: 0.75rem; font-weight: 700; border-radius: var(--radius-sm);">
                            {{ $prestasi->tingkat }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span class="badge badge-accent">{{ $prestasi->peringkat }}</span>
                            <span>{{ $prestasi->tanggal_perolehan->translatedFormat('F Y') }}</span>
                        </div>
                        <h2 class="card-title" style="font-size: 1.125rem;">
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}">{{ $prestasi->nama_prestasi }}</a>
                        </h2>
                        <div style="font-size: 0.8125rem; color: var(--accent-text); font-weight: 700; margin-bottom: 0.5rem;">
                            Peraih: {{ $prestasi->nama_peraih }}
                        </div>
                        <p class="card-text">
                            {{ Str::limit($prestasi->deskripsi, 130) }}
                        </p>
                        <div class="card-footer">
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}" class="btn btn-outline btn-sm">
                                Detail Kejuaraan
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
