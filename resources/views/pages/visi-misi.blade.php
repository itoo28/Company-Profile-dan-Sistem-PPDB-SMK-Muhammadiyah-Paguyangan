@extends('layouts.app')

@section('title', 'Profil Visi & Misi')
@section('meta_description', 'Visi, misi, dan tujuan pendidikan SMK Muhammadiyah Paguyangan Brebes dalam mewujudkan pendidikan kejuruan berkarakter Islami.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Profil Sekolah</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Visi, Misi & Kepemimpinan</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Pedoman dan arah pembinaan pendidikan SMK Muhammadiyah Paguyangan untuk mewujudkan lulusan yang berakhlak mulia dan unggul dalam keterampilan kejuruan.
        </p>
    </div>
</section>

<section class="py-section bg-white">
    <div class="container container-narrow">
        {{-- Visi --}}
        <div style="background-color: var(--primary-surface); border-left: 4px solid var(--primary-base); padding: 2rem; border-radius: 0 var(--radius-md) var(--radius-md) 0; margin-bottom: 2.5rem;">
            <span class="section-eyebrow">Arah Utama</span>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 0.75rem;">Visi SMK Muhammadiyah Paguyangan</h2>
            <p style="font-size: 1.125rem; line-height: 1.7; color: var(--primary-deep); font-weight: 600;">
                "{{ $visi ? $visi->konten : 'Terwujudnya SMK Muhammadiyah Paguyangan sebagai pusat keunggulan pendidikan vokasi yang berakhlak mulia, unggul dalam kompetensi kejuruan, mandiri, dan berdaya saing di dunia industri nasional.' }}"
            </p>
        </div>

        {{-- Misi --}}
        <div style="margin-bottom: 3rem;">
            <span class="section-eyebrow">Langkah Nyata</span>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 1rem;">Misi Sekolah</h2>
            <div class="article-prose">
                {!! $misi ? $misi->konten : '<ol><li>Menanamkan nilai-nilai keislaman dan pembinaan akhlak karimah.</li><li>Menyelenggarakan proses pembelajaran kejuruan berbasis industri.</li></ol>' !!}
            </div>
        </div>

        {{-- Tujuan --}}
        <div style="margin-bottom: 3rem; background-color: var(--canvas-bg); padding: 2rem; border-radius: var(--radius-card); border: 1px solid var(--border-default);">
            <span class="section-eyebrow">Capaian Lulusan</span>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 1rem;">Tujuan Pendidikan</h2>
            <div class="article-prose">
                {!! $tujuan ? $tujuan->konten : '<ul><li>Menghasilkan lulusan yang siap langsung bekerja atau berwirausaha.</li></ul>' !!}
            </div>
        </div>

        {{-- Sambutan Lengkap Kepala Sekolah --}}
        <div style="border-top: 2px solid var(--border-default); padding-top: 3rem;">
            <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                <div style="width: 200px; flex-shrink: 0; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-default);">
                    <img src="{{ asset('images/gambarutama2.jpeg') }}" alt="Kepala Sekolah" style="width: 100%; height: 240px; object-fit: cover;">
                    <div style="background-color: var(--primary-deep); color: #ffffff; padding: 0.75rem; text-align: center;">
                        <div style="font-size: 0.8125rem; font-weight: 700;">Drs. H. Bambang Suhartono</div>
                        <div style="font-size: 0.6875rem; color: #f59e0b;">Kepala Sekolah</div>
                    </div>
                </div>

                <div style="flex-grow: 1; min-width: 280px;">
                    <span class="section-eyebrow">Sambutan Pimpinan</span>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 1rem;">
                        Pesan Kepala SMK Muhammadiyah Paguyangan
                    </h2>
                    <div class="article-prose">
                        {!! $sambutan ? $sambutan->konten : '<p>Selamat datang di kampus vokasi kami.</p>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
