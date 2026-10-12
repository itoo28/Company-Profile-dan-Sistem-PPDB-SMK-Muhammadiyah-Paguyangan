@extends('layouts.app')

@section('title', 'Ekstrakurikuler')
@section('meta_description', 'Kegiatan pengembangan minat, bakat, kepanduan, dan beladiri siswa di SMK Muhammadiyah Paguyangan.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Pengembangan Diri</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Ekstrakurikuler & Organisasi</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Wadah pembinaan bakat, kepemimpinan Islam, olahraga, dan teknologi bagi seluruh peserta didik.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container">
        <div class="card-grid-3">
            @forelse($ekskul as $item)
                <div class="card">
                    <div class="card-img-frame">
                        <img src="{{ asset($item->foto ?? 'images/gambarutama2.jpeg') }}" alt="{{ $item->nama }}" class="card-img">
                        <span style="position: absolute; top: 0.75rem; right: 0.75rem; background: var(--primary-deep); color: #ffffff; padding: 0.25rem 0.625rem; font-size: 0.75rem; font-weight: 700; border-radius: var(--radius-sm);">
                            {{ $item->kategori }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $item->nama }}</h3>
                        <p class="card-text">{{ $item->deskripsi }}</p>
                        
                        <div style="background-color: var(--surface-subdued); padding: 0.75rem; border-radius: var(--radius-sm); font-size: 0.8125rem; display: flex; flex-direction: column; gap: 0.25rem;">
                            @if($item->pembina)
                                <div><strong>Pembina:</strong> {{ $item->pembina }}</div>
                            @endif
                            @if($item->jadwal)
                                <div><strong>Jadwal Latihan:</strong> {{ $item->jadwal }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted); grid-column: 1 / -1;">Belum ada data ekstrakurikuler.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
