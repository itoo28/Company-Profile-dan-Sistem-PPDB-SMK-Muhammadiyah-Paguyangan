@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Situs Resmi SMK Muhammadiyah Paguyangan Brebes. Pusat keunggulan vokasi kejuruan teknik otomotif, jaringan komputer, dan administrasi perkantoran.')

@section('content')
{{-- 1. HERO SECTION (DESIGN.md Section 5.1 No. 1) --}}
<section class="hero-section">
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
                <div class="hero-image-frame">
                    <img src="{{ asset('images/gambarutama2.jpeg') }}" alt="Aktivitas siswa SMK Muhammadiyah Paguyangan" class="hero-image">
                    <div class="hero-media-tag">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                        <div>
                            <div class="hero-media-tag-title">Pendidikan Karakter & Praktikum Terpadu</div>
                            <div class="hero-media-tag-subtitle">SMK Muhammadiyah Paguyangan, Kab. Brebes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. PROGRAM KEAHLIAN UNGGULAN (DESIGN.md Section 5.1 No. 2) --}}
<section class="py-section bg-white">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Pilihan Jurusan Vokasi</span>
            <h2 class="section-title">Program Keahlian Berorientasi Kerja</h2>
            <p class="section-subtitle">
                Kurikulum kejuruan terintegrasi dengan dunia usaha dan industri mitra untuk memastikan lulusan memiliki daya saing tinggi.
            </p>
        </div>

        <div class="card-grid-3">
            {{-- Program 1: TBSM --}}
            <div class="program-card">
                <span class="program-badge">TEKNIK OTOMOTIF</span>
                <h3 class="program-title">Teknik Bisnis Sepeda Motor (TBSM)</h3>
                <p class="program-desc">
                    Mempelajari sistem mekanik mesin injeksi modern, kelistrikan kendaraan roda dua, diagnostik sistem otomatis, hingga manajemen bengkel resmi terstandar.
                </p>
                <div class="program-prospek">
                    <span class="program-prospek-label">Peluang Karir:</span>
                    Mekanik bengkel resmi, teknisi servis otomotif, wirausahawan bengkel, operator industri perakitan motor.
                </div>
            </div>

            {{-- Program 2: TJKT --}}
            <div class="program-card">
                <span class="program-badge">TEKNOLOGI INFORMASI</span>
                <h3 class="program-title">Teknik Jaringan Komputer & Telekomunikasi (TJKT)</h3>
                <p class="program-desc">
                    Mendalami instalasi jaringan LAN/WAN kabel dan nirkabel, administrasi server Linux/Windows, routing Mikrotik, hingga pengamanan sistem data kantor.
                </p>
                <div class="program-prospek">
                    <span class="program-prospek-label">Peluang Karir:</span>
                    Network engineer, staf IT instansi, teknisi instalasi internet ISP, administrator sistem perkantoran.
                </div>
            </div>

            {{-- Program 3: MPLB --}}
            <div class="program-card">
                <span class="program-badge">BISNIS & MANAJEMEN</span>
                <h3 class="program-title">Manajemen Perkantoran & Layanan Bisnis (MPLB)</h3>
                <p class="program-desc">
                    Menguasai tata kelola administrasi dokumen digital, korespondensi resmi, kearsipan modern, komunikasi bisnis profesional, dan pelayanan prima.
                </p>
                <div class="program-prospek">
                    <span class="program-prospek-label">Peluang Karir:</span>
                    Staf administrasi perkantoran, frontliner / resepsionis kantor, arsiparis digital, staf operasional perbankan.
                </div>
            </div>
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
                        <span><strong>Bengkel TBSM Representatif:</strong> Dilengkapi bike lift, peralatan diagnostik injeksi, dan alat ukur mekanik presisi.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <span style="color: var(--primary-base); font-weight: 800; font-size: 1.125rem;">✓</span>
                        <span><strong>Laboratorium Jaringan Komputer:</strong> Puluhan komputer berkoneksi internet fiber optik dan server praktikum.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <span style="color: var(--primary-base); font-weight: 800; font-size: 1.125rem;">✓</span>
                        <span><strong>Musholla & Pembinaan Akhlak:</strong> Tempat ibadah berjamaah, mentoring tilawah, dan pembinaan kepribadian Islami.</span>
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
                <img src="{{ asset('images/gambarutama2.jpeg') }}" alt="Kepala SMK Muhammadiyah Paguyangan" class="headmaster-photo">
            </div>
            <div class="headmaster-text-group">
                <span class="section-eyebrow">Kepemimpinan Institusi</span>
                <div class="headmaster-name">Drs. H. Bambang Suhartono, M.Pd.</div>
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
                                <img src="{{ asset($berita->gambar_sampul ?? 'images/gambargedungbaru.jpg') }}" alt="{{ $berita->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
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
                        <img src="{{ asset($prestasi->foto ?? 'images/gambarutama2.jpeg') }}" alt="{{ $prestasi->nama_prestasi }}" class="card-img">
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
