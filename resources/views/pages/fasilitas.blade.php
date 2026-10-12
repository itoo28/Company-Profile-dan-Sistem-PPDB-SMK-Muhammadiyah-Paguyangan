@extends('layouts.app')

@section('title', 'Fasilitas & Sarana')
@section('meta_description', 'Fasilitas sarana prasarana penunjang praktikum kejuruan dan pembelajaran di SMK Muhammadiyah Paguyangan.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Sarana Pembelajaran</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Fasilitas Sekolah & Laboratorium</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Dukungan fasilitas praktikum modern berstandar industri untuk mengasah kecakapan vokasi peserta didik.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container">
        {{-- Kategori Filter --}}
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <a href="{{ route('fasilitas') }}" class="btn btn-sm {{ !$kategori ? 'btn-primary' : 'btn-secondary' }}">
                Semua Fasilitas
            </a>
            @foreach($kategoriList as $kat)
                <a href="{{ route('fasilitas', ['kategori' => $kat]) }}" class="btn btn-sm {{ $kategori === $kat ? 'btn-primary' : 'btn-secondary' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>

        {{-- Grid Fasilitas --}}
        <div class="card-grid-3">
            @forelse($fasilitas as $item)
                <div class="card">
                    <div class="card-img-frame">
                        <img src="{{ asset($item->foto ?? 'images/gambargedungbaru.jpg') }}" alt="{{ $item->nama }}" class="card-img">
                        <span style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--primary-deep); color: #ffffff; padding: 0.25rem 0.625rem; font-size: 0.75rem; font-weight: 700; border-radius: var(--radius-sm);">
                            {{ $item->kategori }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $item->nama }}</h3>
                        <p class="card-text">{{ $item->deskripsi }}</p>
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted); grid-column: 1 / -1;">Tidak ada data fasilitas dalam kategori ini.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
