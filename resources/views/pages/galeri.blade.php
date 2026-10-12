@extends('layouts.app')

@section('title', 'Galeri Dokumentasi')
@section('meta_description', 'Dokumentasi foto kegiatan belajar, praktikum bengkel, dan lingkungan SMK Muhammadiyah Paguyangan.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Dokumentasi Foto</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Galeri Aktivitas Sekolah</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Merekam jejak kegiatan belajar mengajar, praktikum kejuruan, dan kebersamaan di lingkungan sekolah.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container">
        <div class="card-grid-3">
            @forelse($galeriList as $item)
                <div class="card">
                    <div class="card-img-frame" style="aspect-ratio: 4/3;">
                        <img src="{{ asset($item->foto) }}" alt="{{ $item->judul }}" class="card-img">
                        <span style="position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(9, 35, 71, 0.85); color: #ffffff; padding: 0.25rem 0.625rem; font-size: 0.75rem; font-weight: 700; border-radius: var(--radius-sm);">
                            {{ $item->kategori }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title" style="font-size: 1rem;">{{ $item->judul }}</h3>
                        @if($item->keterangan)
                            <p class="card-text" style="font-size: 0.875rem;">{{ $item->keterangan }}</p>
                        @endif
                        @if($item->tanggal)
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: auto;">
                                {{ $item->tanggal->translatedFormat('d F Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted); grid-column: 1 / -1;">Belum ada dokumentasi galeri.</p>
            @endforelse
        </div>

        <div class="pagination-wrapper">
            {{ $galeriList->links() }}
        </div>
    </div>
</section>
@endsection
