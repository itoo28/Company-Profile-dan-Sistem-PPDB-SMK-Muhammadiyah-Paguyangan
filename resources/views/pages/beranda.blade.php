@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Situs Resmi SMK Muhammadiyah Paguyangan Brebes. Pusat keunggulan vokasi kejuruan teknik otomotif, jaringan komputer, dan administrasi perkantoran.')

@section('content')
@php
    $heroSlides = collect();

    // Kombinasi berita dan prestasi secara bergantian untuk carousel sorotan
    $maxItems = max($beritaTerbaru->count(), $prestasiTerbaru->count());
    for ($i = 0; $i < $maxItems; $i++) {
        if (isset($beritaTerbaru[$i])) {
            $b = $beritaTerbaru[$i];
            $heroSlides->push([
                'type' => 'berita',
                'type_label' => 'Berita Terkini',
                'title' => $b->judul,
                'meta' => $b->tanggal_terbit->translatedFormat('d F Y'),
                'meta_secondary' => $b->penulis ?? 'Humas SMK',
                'image' => $b->gambar_url ?? asset('images/gambargedungbaru.jpg'),
                'excerpt' => Str::limit($b->ringkasan, 95),
                'url' => route('berita.detail', $b->slug),
            ]);
        }
        if (isset($prestasiTerbaru[$i])) {
            $p = $prestasiTerbaru[$i];
            $heroSlides->push([
                'type' => 'prestasi',
                'type_label' => 'Prestasi Siswa',
                'title' => $p->nama_prestasi,
                'meta' => $p->tingkat ?? 'Kejuaraan',
                'meta_secondary' => $p->nama_peraih,
                'image' => $p->foto_url ?? asset('images/gambarutama2.jpeg'),
                'excerpt' => Str::limit($p->deskripsi, 95),
                'url' => route('prestasi.detail', $p->slug),
            ]);
        }
    }

    if ($heroSlides->isEmpty()) {
        $heroSlides->push([
            'type' => 'berita',
            'type_label' => 'Fasilitas Unggulan',
            'title' => 'Gedung Belajar Baru SMK Muhammadiyah Paguyangan',
            'meta' => 'Tahun Ajaran 2026/2027',
            'meta_secondary' => 'Brebes, Jawa Tengah',
            'image' => asset('images/gambargedungbaru.jpg'),
            'excerpt' => 'Fasilitas pembelajaran kejuruan teknik dan laboratorium modern yang representatif.',
            'url' => route('fasilitas'),
        ]);
    }
@endphp

{{-- 1. HERO SECTION DENGAN LATAR GEDUNG BARU & SLIDER BERITA/PRESTASI --}}
<section class="hero-section" style="background-image: linear-gradient(to right, rgba(6, 25, 51, 0.94) 0%, rgba(9, 35, 71, 0.86) 45%, rgba(14, 45, 92, 0.78) 100%), url('{{ asset('images/gambargedungbaru.jpg') }}');">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <h1 class="hero-title">
                    Mencetak Generasi Vokasi <span>Berkarakter Islami</span> dan Siap Masuk Dunia Industri
                </h1>
                <p class="hero-subtitle">
                    Selamat datang di SMK Muhammadiyah Paguyangan, Brebes. Kami memadukan keterampilan teknis kejuruan terstandar, sarana praktikum lengkap, serta penanaman akhlak mulia berlandaskan nilai Islam Berkemajuan.
                </p>
                <div class="hero-cta-group">
                    <a href="{{ route('ppdb.daftar') }}" class="btn btn-primary btn-lg" id="btnHeroDaftar">
                        Daftar Siswa Baru
                    </a>
                    <a href="{{ route('ppdb.info') }}" class="btn btn-secondary btn-lg" id="btnHeroAlur">
                        Alur & Persyaratan Berkas
                    </a>
                </div>
            </div>

            <div class="hero-media-wrapper">
                <div class="hero-slider-wrapper" aria-roledescription="carousel" aria-label="Sorotan Berita dan Prestasi">
                    <div class="hero-slider-card">
                        {{-- Header Bar Kartu Slider --}}
                        <div class="hero-slider-header">
                            <div class="hero-slider-live">
                                <span class="live-dot" aria-hidden="true"></span>
                                <span class="live-text">Sorotan & Warta Terkini</span>
                            </div>
                            <div class="hero-slider-counter" id="heroSlideCounter" aria-live="polite">
                                <span id="heroCurrentSlide">1</span> / <span id="heroTotalSlides">{{ count($heroSlides) }}</span>
                            </div>
                        </div>

                        {{-- Slides Container --}}
                        <div class="hero-slider-track" id="heroSliderTrack">
                            @foreach($heroSlides as $index => $slide)
                                <div class="hero-slide {{ $index === 0 ? 'is-active' : '' }}" 
                                     data-slide-index="{{ $index }}"
                                     role="group" 
                                     aria-roledescription="slide" 
                                     aria-label="{{ $slide['type_label'] }}: {{ $slide['title'] }}">
                                    <div class="hero-slide-media">
                                        <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="hero-slide-img" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" onerror="this.onerror=null; this.src='{{ asset('images/gambargedungbaru.jpg') }}';">
                                        <div class="hero-slide-badge hero-slide-badge--{{ $slide['type'] }}">
                                            @if($slide['type'] === 'berita')
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path>
                                                    <path d="M18 14h-8"></path>
                                                    <path d="M15 18h-5"></path>
                                                    <path d="M10 6h8v4h-8V6Z"></path>
                                                </svg>
                                            @else
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                                    <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                                    <path d="M4 22h16"></path>
                                                    <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                                                    <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                                                    <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                                                </svg>
                                            @endif
                                            <span>{{ $slide['type_label'] }}</span>
                                        </div>
                                    </div>

                                    <div class="hero-slide-body">
                                        <div class="hero-slide-meta">
                                            <span>{{ $slide['meta'] }}</span>
                                            @if(!empty($slide['meta_secondary']))
                                                <span>•</span>
                                                <span>{{ $slide['meta_secondary'] }}</span>
                                            @endif
                                        </div>
                                        <h2 class="hero-slide-title">
                                            <a href="{{ $slide['url'] }}">{{ $slide['title'] }}</a>
                                        </h2>
                                        <p class="hero-slide-excerpt">
                                            {{ $slide['excerpt'] }}
                                        </p>
                                        <div class="hero-slide-footer">
                                            <a href="{{ $slide['url'] }}" class="hero-slide-link">
                                                <span>{{ $slide['type'] === 'berita' ? 'Baca Selengkapnya' : 'Lihat Detail Prestasi' }}</span>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                    <polyline points="12 5 19 12 12 19"></polyline>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Navigasi Bawah & Dot Indicators --}}
                        <div class="hero-slider-nav">
                            <div class="hero-slider-dots" id="heroSliderDots" role="tablist" aria-label="Navigasi Slide">
                                @foreach($heroSlides as $index => $slide)
                                    <button type="button" 
                                            class="hero-slider-dot {{ $index === 0 ? 'is-active' : '' }}" 
                                            data-slide-target="{{ $index }}"
                                            role="tab"
                                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-label="Slide {{ $index + 1 }}: {{ $slide['title'] }}"></button>
                                @endforeach
                            </div>
                            <div class="hero-slider-arrows">
                                <button type="button" class="hero-slider-arrow" id="heroPrevBtn" aria-label="Slide Sebelumnya">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </button>
                                <button type="button" class="hero-slider-arrow" id="heroNextBtn" aria-label="Slide Selanjutnya">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Auto-slide Progress Bar --}}
                        <div class="hero-slider-progress" aria-hidden="true">
                            <div class="hero-slider-progress-bar" id="heroProgressBar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. PROGRAM KEAHLIAN UNGGULAN (DESIGN.md Section 5.1 No. 2) --}}
<section class="py-section bg-white" id="jurusan">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Pilihan Jurusan Vokasi</span>
            <h2 class="section-title">Konsentrasi Keahlian Berorientasi Kerja</h2>
            <p class="section-subtitle">
                Kurikulum kejuruan terintegrasi dengan dunia usaha dan industri mitra untuk memastikan lulusan memiliki daya saing tinggi.
            </p>
        </div>

        <div class="program-grid">
            @php
                $jurusans = $programKeahlian->extra_data['jurusan'] ?? [
                    [
                        'kode' => 'TKR',
                        'bidang' => 'TEKNIK OTOMOTIF',
                        'nama' => 'Teknik Kendaraan Ringan (TKR)',
                        'deskripsi' => 'Mempelajari pemeliharaan, diagnosis kerusakan, dan perbaikan mesin kendaraan roda empat, sistem transmisi manual dan otomatis, sasis, sistem rem, suspensi, dan kelistrikan mobil modern.',
                        'prospek' => 'Mekanik bengkel resmi mobil (ATPM), teknisi servis mandiri, teknisi armada transportasi, operator industri perakitan, wirausaha bengkel.',
                    ],
                    [
                        'kode' => 'TSM',
                        'bidang' => 'TEKNIK OTOMOTIF',
                        'nama' => 'Teknik Sepeda Motor (TSM)',
                        'deskripsi' => 'Mempelajari perawatan berkala mesin sepeda motor, teknologi sistem injeksi (EFI), transmisi otomatis CVT, kelistrikan roda dua, dan tata kelola bengkel resmi.',
                        'prospek' => 'Teknisi bengkel resmi sepeda motor, mekanik servis injeksi, teknisi perakitan motor, wirausahawan bengkel modern.',
                    ],
                    [
                        'kode' => 'TOTR',
                        'bidang' => 'OTOMOTIF & ELEKTRONIKA',
                        'nama' => 'Teknik Ototronik (TOTR)',
                        'deskripsi' => 'Mempelajari integrasi teknologi elektronika dan komputasi kendaraan canggih, meliputi Engine Management System (EMS), sensor cerdas, scanner diagnostik OBD, rem ABS, airbag, dan teknologi kendaraan hybrid/listrik.',
                        'prospek' => 'Spesialis scanner & kelistrikan otomotif, teknisi kendaraan hybrid & EV, teknisi instalasi audio & aksesoris, quality control otomotif.',
                    ],
                    [
                        'kode' => 'RPL',
                        'bidang' => 'TEKNOLOGI INFORMASI',
                        'nama' => 'Rekayasa Perangkat Lunak (RPL)',
                        'deskripsi' => 'Mempelajari logika pemrograman, pengembangan aplikasi web dan mobile, perancangan basis data, desain UI/UX antarmuka, serta pengujian sistem perangkat lunak terstandar.',
                        'prospek' => 'Junior software developer, web developer, mobile application developer, database administrator, staf IT support, pengembang freelance.',
                    ],
                    [
                        'kode' => 'BROADCASTING',
                        'bidang' => 'SENI & KREATIF DIGITAL',
                        'nama' => 'Produksi Film (Broadcasting)',
                        'deskripsi' => 'Mempelajari teknik sinematografi, pengoperasian kamera profesional, tata pencahayaan dan suara, penulisan naskah/skenario, editing video digital, hingga manajemen penyiaran multimedia.',
                        'prospek' => 'Videografer & video editor, juru kamera (cameraman) TV/studio, digital content creator, kru produksi sinematografi, staf humas audio visual.',
                    ],
                ];
            @endphp

            @foreach($jurusans as $jurusan)
                <div class="program-card">
                    <span class="program-badge">{{ $jurusan['bidang'] ?? 'KONSENTRASI KEAHLIAN' }}</span>
                    <h3 class="program-title">{{ $jurusan['nama'] }}</h3>
                    <p class="program-desc">
                        {{ $jurusan['deskripsi'] }}
                    </p>
                    <div class="program-prospek">
                        <span class="program-prospek-label">Peluang Karir:</span>
                        {{ $jurusan['prospek'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 3. FASILITAS & SARANA PRAKTIKUM (Menggunakan gambargedungbaru.jpg) --}}
<section class="py-section bg-canvas">
    <div class="container">
        <div class="card-grid-2" style="align-items: center;">
            <div>
                <span class="section-eyebrow">Sarana & Prasarana</span>
                <h2 class="section-title">Gedung Baru dan Bengkel Praktik Terstandar Industri</h2>
                <p style="margin-top: 1rem; margin-bottom: 1.25rem; font-size: 1rem; color: var(--text-body); line-height: 1.7;">
                    SMK Muhammadiyah Paguyangan terus memperkuat fasilitas pembelajaran kejuruan dengan menghadirkan sarana ruang belajar baru yang representatif di Paguyangan, Brebes.
                </p>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.875rem; margin-bottom: 1.75rem;">
                    <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <span style="color: var(--primary-base); font-weight: 800; font-size: 1.125rem;">✓</span>
                        <span><strong>Bengkel Otomotif TKR & TSM:</strong> Dilengkapi car lift, bike lift hidrolik, fuel injection tester, engine overhaul stand, dan peralatan diagnostik presisi.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <span style="color: var(--primary-base); font-weight: 800; font-size: 1.125rem;">✓</span>
                        <span><strong>Laboratorium RPL & Ototronik:</strong> Komputer workstation pemrograman cepat, scanner OBD-II, simulator EMS, dan jaringan serat optik.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <span style="color: var(--primary-base); font-weight: 800; font-size: 1.125rem;">✓</span>
                        <span><strong>Studio Produksi Film & Broadcasting:</strong> Kamera sinematografi, green screen akustik, tata cahaya studio, dan ruang pascaproduksi video.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <span style="color: var(--primary-base); font-weight: 800; font-size: 1.125rem;">✓</span>
                        <span><strong>Musholla & Pembinaan Karakter:</strong> Tempat ibadah berjamaah, mentoring tilawah Al-Quran, dan pembinaan kepribadian Islami.</span>
                    </li>
                </ul>
                <a href="{{ route('fasilitas') }}" class="btn btn-outline">
                    Lihat Seluruh Fasilitas Sekolah
                </a>
            </div>

            <div>
                <div style="border-radius: var(--radius-card); overflow: hidden; box-shadow: var(--shadow-hover); border: 1px solid var(--border-default);">
                    <img src="{{ asset('images/gambargedungbaru.jpg') }}" alt="Gedung Baru SMK Muhammadiyah Paguyangan" style="width: 100%; height: 360px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 4. SAMBUTAN KEPALA SEKOLAH --}}
<section class="py-section bg-white">
    <div class="container">
        <div class="headmaster-box">
            <div class="headmaster-photo-frame">
                <img src="{{ asset($sambutan->gambar ?? 'images/kepala-sekolah.jpg') }}" alt="Kepala SMK Muhammadiyah Paguyangan" class="headmaster-photo" style="object-position: top center;">
            </div>
            <div class="headmaster-text-group">
                <span class="section-eyebrow">Kepemimpinan Institusi</span>
                <div class="headmaster-name">{{ $sambutan->extra_data['nama_kepala_sekolah'] ?? 'Mohammad Ardianshah, S.Kom' }}</div>
                <div class="headmaster-title">Kepala SMK Muhammadiyah Paguyangan</div>
                <div class="headmaster-quote">
                    {!! $sambutan ? Str::limit(strip_tags($sambutan->konten), 340) : 'Selamat datang di SMK Muhammadiyah Paguyangan. Kami berikhtiar mencetak lulusan kejuruan yang menguasai keahlian teknis sesuai standar kerja industri, berintegritas moral, dan kokoh dalam aqidah Islam.' !!}
                </div>
                <div>
                    <a href="{{ route('visi-misi') }}" class="btn btn-secondary btn-sm">
                        Baca Selengkapnya
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. BERITA TERBARU & PENGUMUMAN (DESIGN.md Section 5.1 No. 3) --}}
<section class="py-section bg-canvas">
    <div class="container">
        <div class="news-announcement-grid">
            {{-- Bagian Berita (Kartu Format 16:9) --}}
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
                    <div>
                        <span class="section-eyebrow">Warta Sekolah</span>
                        <h2 class="section-title" style="font-size: 1.5rem;">Kabar & Berita Terkini</h2>
                    </div>
                    <a href="{{ route('berita') }}" style="font-weight: 700; font-size: 0.875rem;">Semua Berita</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @forelse($beritaTerbaru as $berita)
                        <article class="card news-card-horizontal">
                            <div class="news-card-thumb">
                                <img src="{{ $berita->gambar_url ?? asset('images/gambargedungbaru.jpg') }}" alt="{{ $berita->judul }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/gambargedungbaru.jpg') }}';">
                            </div>
                            <div class="card-body" style="padding: 1.125rem;">
                                <div class="card-meta">
                                    <span>{{ $berita->tanggal_terbit->translatedFormat('d F Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $berita->penulis ?? 'Humas' }}</span>
                                </div>
                                <h3 class="card-title" style="font-size: 1rem;">
                                    <a href="{{ route('berita.detail', $berita->slug) }}">{{ $berita->judul }}</a>
                                </h3>
                                <p class="card-text" style="font-size: 0.875rem; margin-bottom: 0;">
                                    {{ Str::limit($berita->ringkasan, 100) }}
                                </p>
                            </div>
                        </article>
                    @empty
                        <p style="color: var(--text-muted);">Belum ada berita yang diterbitkan saat ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Bagian Pengumuman (Listicle Vertikal Bertanggal) --}}
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
                    <div>
                        <span class="section-eyebrow">Pemberitahuan</span>
                        <h2 class="section-title" style="font-size: 1.5rem;">Pengumuman</h2>
                    </div>
                    <a href="{{ route('pengumuman') }}" style="font-weight: 700; font-size: 0.875rem;">Arsip Pengumuman</a>
                </div>

                <div class="announcement-list">
                    @forelse($pengumumanTerbaru as $pengumuman)
                        <div class="announcement-item">
                            <div class="announcement-date-box">
                                <div class="announcement-date-day">{{ $pengumuman->tanggal->format('d') }}</div>
                                <div class="announcement-date-month">{{ $pengumuman->tanggal->translatedFormat('M') }}</div>
                            </div>
                            <div class="announcement-content">
                                <h3 class="announcement-title">{{ $pengumuman->judul }}</h3>
                                <p class="announcement-excerpt">{{ Str::limit($pengumuman->isi, 120) }}</p>
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--text-muted);">Belum ada pengumuman baru saat ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 6. PRESTASI SISWA & SEKOLAH --}}
<section class="py-section bg-white">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Rekam Jejak Prestasi</span>
            <h2 class="section-title">Prestasi Siswa SMK Muhammadiyah Paguyangan</h2>
            <p class="section-subtitle">
                Bukti nyata dedikasi dan kerja keras para peserta didik dalam ajang kompetisi kejuruan, kepanduan, dan olahraga.
            </p>
        </div>

        <div class="card-grid-3">
            @forelse($prestasiTerbaru as $prestasi)
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
                        <h3 class="card-title">
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}">{{ $prestasi->nama_prestasi }}</a>
                        </h3>
                        <p style="font-size: 0.8125rem; color: var(--accent-text); font-weight: 700; margin-bottom: 0.5rem;">
                            Peraih: {{ $prestasi->nama_peraih }}
                        </p>
                        <p class="card-text">
                            {{ Str::limit($prestasi->deskripsi, 110) }}
                        </p>
                        <div class="card-footer">
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}" class="btn btn-outline btn-sm">
                                Detail Prestasi
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <p style="color: var(--text-muted); grid-column: 1 / -1; text-align: center;">Belum ada data prestasi yang ditampilkan.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- 7. ALUR PPDB SINGKAT & CTA SECTION (DESIGN.md Section 5.1 No. 4) --}}
<section class="py-section" style="background: linear-gradient(135deg, #092347 0%, #0d346c 100%); color: #ffffff;">
    <div class="container">
        <div class="section-header text-center" style="margin-bottom: 3rem;">
            <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Penerimaan Siswa Baru</span>
            <h2 class="section-title" style="color: #ffffff;">Alur Pendaftaran Peserta Didik Baru (PPDB)</h2>
            <p class="section-subtitle" style="color: #cbd5e1;">
                Empat langkah mudah untuk bergabung menjadi peserta didik SMK Muhammadiyah Paguyangan.
            </p>
        </div>

        <div class="flow-grid">
            <div class="flow-step-card">
                <div class="flow-step-num">1</div>
                <h3 class="flow-step-title">Pendaftaran Mandiri</h3>
                <p class="flow-step-desc">Mengisi data diri calon siswa secara online atau datang ke loket pendaftaran offline di kampus sekolah.</p>
            </div>

            <div class="flow-step-card">
                <div class="flow-step-num">2</div>
                <h3 class="flow-step-title">Unggah Berkas</h3>
                <p class="flow-step-desc">Masuk ke portal calon siswa untuk mengunggah dokumen KK, Akta Kelahiran, dan Ijazah/SKL secara bertahap.</p>
            </div>

            <div class="flow-step-card">
                <div class="flow-step-num">3</div>
                <h3 class="flow-step-title">Verifikasi Panitia</h3>
                <p class="flow-step-desc">Panitia memeriksa kelengkapan dan keabsahan berkas yang diunggah secara digital.</p>
            </div>

            <div class="flow-step-card">
                <div class="flow-step-num">4</div>
                <h3 class="flow-step-title">Ambil Seragam</h3>
                <p class="flow-step-desc">Setelah semua berkas disetujui panitia, calon siswa berhak mengambil seragam di loket Tata Usaha.</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <a href="{{ route('ppdb.daftar') }}" class="btn btn-accent btn-lg" id="btnCtaBottomPPDB">
                Daftar PPDB Sekarang
            </a>
            <a href="{{ route('ppdb.info') }}" class="btn btn-outline btn-lg" style="color: #ffffff; border-color: rgba(255, 255, 255, 0.4); margin-left: 0.75rem;">
                Panduan Persyaratan Lengkap
            </a>
        </div>
    </div>
</section>
@endsection
