@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Situs Resmi SMK Muhammadiyah Paguyangan Brebes. Pusat keunggulan vokasi kejuruan teknik otomotif, jaringan komputer, dan administrasi perkantoran.')

@section('content')
{{-- 1. HERO SECTION DENGAN TATA LETAK REFERENSI (FULL VIEWPORT & FLOATING ACTION CAPSULE) --}}
<section class="hero-section hero-section--modern" style="background-image: linear-gradient(180deg, rgba(9, 35, 71, 0.72) 0%, rgba(11, 87, 208, 0.58) 45%, rgba(6, 25, 51, 0.94) 100%), url('{{ asset('images/gambargedungbaru.jpg') }}');">
    <div class="container hero-container">
        
        {{-- Headline, Subtitle & Action Capsule (Centered Sesuai Referensi) --}}
        <div class="hero-center-content reveal-on-scroll">
            <h1 class="hero-main-title">
                Mencetak Generasi Vokasi Berkarakter Islami & Siap Masuk Dunia Industri
            </h1>
            <p class="hero-main-desc">
                Selamat datang di SMK Muhammadiyah Paguyangan, Brebes. Kami memadukan keterampilan teknis kejuruan terstandar industri, sarana bengkel modern, serta pembinaan akhlak mulia berlandaskan nilai Islam Berkemajuan.
            </p>

            {{-- Tombol Aksi Hero (Pill CTA Sesuai Tema) --}}
            <div class="hero-cta-group">
                <a href="{{ route('ppdb.daftar') }}" class="hero-btn-primary" id="btnHeroDaftar">
                    <span>Daftar Siswa Baru</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
                <a href="{{ route('ppdb.info') }}" class="hero-btn-secondary" id="btnHeroAlur">
                    <span>Alur & Persyaratan Berkas</span>
                </a>
            </div>
        </div>

    </div>
</section>

{{-- 2. PROGRAM KEAHLIAN UNGGULAN (DESIGN.md Section 5.1 No. 2) --}}
<section class="py-section bg-white" id="jurusan">
    <div class="container">
        <div class="section-header text-center reveal-on-scroll">
            <span class="section-eyebrow">Pilihan Jurusan Vokasi</span>
            <h2 class="section-title">Konsentrasi Keahlian Berorientasi Kerja</h2>
            <p class="section-subtitle">
                Kurikulum kejuruan terintegrasi dengan dunia usaha dan industri mitra untuk memastikan lulusan memiliki daya saing tinggi.
            </p>
        </div>

        {{-- Filter Kategori Interaktif (Poin 3.1) --}}
        @php
            $industryBadges = [
                'TKR' => 'Standar Industri ATPM & Servis Presisi',
                'TSM' => 'Standar Bengkel Resmi AHASS & Motor Injeksi',
                'TOTR' => 'Teknologi Hybrid & Scanner OBD-II',
                'RPL' => 'Kurikulum Software House & Industri Digital',
                'BROADCASTING' => 'Standar Studio Penyiaran & Sinematografi',
            ];
            $otomotifCodes = ['TKR', 'TSM', 'TOTR'];

            $fallbackJurusans = $programKeahlian->extra_data['jurusan'] ?? [
                [
                    'kode' => 'TKR',
                    'bidang' => 'TEKNIK OTOMOTIF',
                    'nama' => 'Teknik Kendaraan Ringan (TKR)',
                    'deskripsi' => 'Mempelajari pemeliharaan, diagnosis kerusakan, dan perbaikan mesin kendaraan roda empat, sistem transmisi manual dan otomatis, sasis, sistem rem, suspensi, dan kelistrikan mobil modern.',
                    'prospek' => 'Mekanik bengkel ATPM resmi, teknisi servis mandiri, teknisi armada transportasi, operator industri perakitan, wirausaha bengkel.',
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

        <div class="program-grid reveal-stagger" id="programGrid">

            @if(isset($jurusans) && $jurusans->isNotEmpty())
                @foreach($jurusans as $jurusan)
                    @php
                        $jurusanCategory = in_array(strtoupper($jurusan->kode), $otomotifCodes) ? 'otomotif' : 'ti-digital';
                        $industryBadge = $industryBadges[strtoupper($jurusan->kode)] ?? 'Kurikulum Berbasis Industri TEFA';
                        $fotoUrl = !empty($jurusan->foto)
                            ? (str_starts_with($jurusan->foto, 'images/') ? asset($jurusan->foto) : asset('storage/' . $jurusan->foto))
                            : (in_array(strtoupper($jurusan->kode), ['RPL', 'BROADCASTING']) ? asset('images/gambarutama2.jpeg') : asset('images/gambargedungbaru.jpg'));
                    @endphp
                    <div class="program-card reveal-on-scroll" data-category="{{ $jurusanCategory }}">
                        {{-- Ambient Full-Bleed Media Cover & Scrim Gradient Minimalis --}}
                        <div class="program-card-media-backdrop">
                            <img src="{{ $fotoUrl }}" alt="{{ $jurusan->nama_jurusan }}" class="program-card-bg-img" loading="lazy">
                            <div class="program-card-scrim"></div>
                        </div>

                        {{-- Floating Topbar (Kode Jurusan Minimalis) --}}
                        <div class="program-card-topbar">
                            <span class="program-code-badge">{{ $jurusan->kode }}</span>
                        </div>

                        {{-- Content Area Ringkas di Dasar Kartu --}}
                        <div class="program-card-content">
                            <h3 class="program-title">{{ $jurusan->nama_jurusan }}</h3>

                            <div class="program-card-footer">
                                <span class="program-card-kuota">Kuota: {{ $jurusan->kuota ?? '72' }} Siswa</span>
                                <a href="{{ route('ppdb.daftar') }}" class="program-card-link">
                                    Daftar Pilihan Ini <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach($fallbackJurusans as $jurusan)
                    @php
                        $jurusanCategory = in_array(strtoupper($jurusan['kode']), $otomotifCodes) ? 'otomotif' : 'ti-digital';
                        $industryBadge = $industryBadges[strtoupper($jurusan['kode'])] ?? 'Kurikulum Berbasis Industri TEFA';
                        $fotoUrl = in_array(strtoupper($jurusan['kode']), ['RPL', 'BROADCASTING'])
                            ? asset('images/gambarutama2.jpeg')
                            : asset('images/gambargedungbaru.jpg');
                    @endphp
                    <div class="program-card reveal-on-scroll" data-category="{{ $jurusanCategory }}">
                        {{-- Ambient Full-Bleed Media Cover & Scrim Gradient Minimalis --}}
                        <div class="program-card-media-backdrop">
                            <img src="{{ $fotoUrl }}" alt="{{ $jurusan['nama'] }}" class="program-card-bg-img" loading="lazy">
                            <div class="program-card-scrim"></div>
                        </div>

                        {{-- Floating Topbar (Kode Jurusan Minimalis) --}}
                        <div class="program-card-topbar">
                            <span class="program-code-badge">{{ $jurusan['kode'] }}</span>
                        </div>

                        {{-- Content Area Ringkas di Dasar Kartu --}}
                        <div class="program-card-content">
                            <h3 class="program-title">{{ $jurusan['nama'] }}</h3>

                            <div class="program-card-footer">
                                <span class="program-card-kuota">Kuota: 72 Siswa</span>
                                <a href="{{ route('ppdb.daftar') }}" class="program-card-link">
                                    Daftar Pilihan Ini <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

{{-- 3. FASILITAS & SARANA PRAKTIKUM (Menggunakan gambargedungbaru.jpg) --}}
<section class="py-section bg-canvas">
    <div class="container">
        <div class="card-grid-2 reveal-on-scroll" style="align-items: center;">
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
        <div class="headmaster-box reveal-on-scroll">
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
        <div class="news-announcement-grid reveal-on-scroll">
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
        <div class="section-header text-center reveal-on-scroll">
            <span class="section-eyebrow">Rekam Jejak Prestasi</span>
            <h2 class="section-title">Prestasi Siswa SMK Muhammadiyah Paguyangan</h2>
            <p class="section-subtitle">
                Bukti nyata dedikasi dan kerja keras para peserta didik dalam ajang kompetisi kejuruan, kepanduan, dan olahraga.
            </p>
        </div>

        <div class="card-grid-3 reveal-stagger">
            @forelse($prestasiTerbaru as $prestasi)
                <article class="prestasi-card reveal-on-scroll">
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
                        <h3 class="prestasi-title">
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}">{{ $prestasi->nama_prestasi }}</a>
                        </h3>

                        <div class="prestasi-card-footer">
                            <span class="prestasi-card-date">{{ $prestasi->tanggal_perolehan->translatedFormat('F Y') }}</span>
                            <a href="{{ route('prestasi.detail', $prestasi->slug) }}" class="prestasi-card-link">
                                Detail Prestasi <span aria-hidden="true">&rarr;</span>
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
        <div class="section-header text-center reveal-on-scroll" style="margin-bottom: 3rem;">
            <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Penerimaan Siswa Baru</span>
            <h2 class="section-title" style="color: #ffffff;">Alur Pendaftaran Peserta Didik Baru (PPDB)</h2>
            <p class="section-subtitle" style="color: #cbd5e1;">
                Empat langkah mudah untuk bergabung menjadi peserta didik SMK Muhammadiyah Paguyangan.
            </p>
        </div>

        <div class="flow-grid reveal-stagger">
            <div class="flow-step-card reveal-on-scroll">
                <div class="flow-step-num">1</div>
                <h3 class="flow-step-title">Pendaftaran Mandiri</h3>
                <p class="flow-step-desc">Mengisi data diri calon siswa secara online atau datang ke loket pendaftaran offline di kampus sekolah.</p>
            </div>

            <div class="flow-step-card reveal-on-scroll">
                <div class="flow-step-num">2</div>
                <h3 class="flow-step-title">Unggah Berkas</h3>
                <p class="flow-step-desc">Masuk ke portal calon siswa untuk mengunggah dokumen KK, Akta Kelahiran, dan Ijazah/SKL secara bertahap.</p>
            </div>

            <div class="flow-step-card reveal-on-scroll">
                <div class="flow-step-num">3</div>
                <h3 class="flow-step-title">Verifikasi Panitia</h3>
                <p class="flow-step-desc">Panitia memeriksa kelengkapan dan keabsahan berkas yang diunggah secara digital.</p>
            </div>

            <div class="flow-step-card reveal-on-scroll">
                <div class="flow-step-num">4</div>
                <h3 class="flow-step-title">Ambil Seragam</h3>
                <p class="flow-step-desc">Setelah semua berkas disetujui panitia, calon siswa berhak mengambil seragam di loket Tata Usaha.</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 3rem;" class="reveal-on-scroll">
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
