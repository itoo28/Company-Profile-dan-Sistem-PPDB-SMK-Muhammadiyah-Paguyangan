@extends('layouts.app')

@section('title', 'Pengumuman Resmi')
@section('meta_description', 'Daftar pengumuman resmi dan surat edaran SMK Muhammadiyah Paguyangan Brebes.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Pemberitahuan</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Pengumuman & Edaran Resmi</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Pemberitahuan resmi mengenai agenda akademik, pengumuman seleksi berkas PPDB, dan jadwal kegiatan sekolah.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container container-narrow">
        <div class="announcement-list">
            @forelse($pengumumanList as $item)
                <div class="announcement-item" style="background-color: #ffffff;">
                    <div class="announcement-date-box">
                        <div class="announcement-date-day">{{ $item->tanggal->format('d') }}</div>
                        <div class="announcement-date-month">{{ $item->tanggal->translatedFormat('M Y') }}</div>
                    </div>
                    <div class="announcement-content">
                        <h2 class="announcement-title" style="font-size: 1.125rem;">{{ $item->judul }}</h2>
                        <div class="article-prose" style="font-size: 0.9375rem; color: var(--text-body); line-height: 1.6; margin-top: 0.5rem;">
                            {{ $item->isi }}
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted); text-align: center;">Belum ada pengumuman yang diterbitkan.</p>
            @endforelse
        </div>

        <div class="pagination-wrapper">
            {{ $pengumumanList->links() }}
        </div>
    </div>
</section>
@endsection
