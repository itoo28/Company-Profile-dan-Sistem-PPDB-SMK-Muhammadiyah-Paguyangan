@php
    $pengaturanFooter = \App\Models\PengaturanSistem::instance();
@endphp
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            {{-- Kolom Identitas Sekolah --}}
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo SMK Muhammadiyah Paguyangan" style="width: 44px; height: 44px; object-fit: contain; border-radius: var(--radius-sm); background: #ffffff; padding: 2px;">
                    <div>
                        <div class="footer-brand-title">SMK Muhammadiyah Paguyangan</div>
                        <div style="font-size: 0.75rem; color: #f59e0b; font-weight: 700;">Kabupaten Brebes, Jawa Tengah</div>
                    </div>
                </div>
                <p class="footer-brand-desc">
                    Sekolah Menengah Kejuruan berbasis nilai-nilai Islam Berkemajuan. Menghasilkan lulusan yang berkarakter kuat, menguasai keahlian teknis industri, dan berdaya saing kerja tinggi.
                </p>
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255, 255, 255, 0.08); padding: 0.4rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.8125rem; color: #cbd5e1;">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e;"></span>
                    <span>NPSN: 20326543 | Akreditasi B</span>
                </div>
            </div>

            {{-- Kolom Program Keahlian --}}
            <div>
                <h3 class="footer-col-title">Program Kejuruan</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('fasilitas') }}">Teknik Bisnis Sepeda Motor (TBSM)</a></li>
                    <li><a href="{{ route('fasilitas') }}">Teknik Jaringan Komputer & Telekomunikasi (TJKT)</a></li>
                    <li><a href="{{ route('fasilitas') }}">Manajemen Perkantoran & Layanan Bisnis (MPLB)</a></li>
                    <li><a href="{{ route('fasilitas') }}">Laboratorium Komputer & Bengkel Resmi</a></li>
                    <li><a href="{{ route('prestasi') }}">Prestasi Siswa Kejuruan</a></li>
                </ul>
            </div>

            {{-- Kolom Informasi & PPDB --}}
            <div>
                <h3 class="footer-col-title">Layanan & Informasi</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('visi-misi') }}">Profil Visi, Misi & Tujuan</a></li>
                    <li><a href="{{ route('ppdb.info') }}">Panduan & Alur PPDB Online</a></li>
                    <li><a href="{{ route('ppdb.daftar') }}">Pendaftaran Siswa Baru</a></li>
                    <li><a href="{{ route('siswa.login') }}">Masuk Portal Calon Siswa</a></li>
                    <li><a href="{{ route('pengumuman') }}">Pengumuman Terkini</a></li>
                    <li><a href="{{ route('berita') }}">Kabar & Berita Sekolah</a></li>
                    <li><a href="{{ route('galeri') }}">Galeri Dokumentasi</a></li>
                </ul>
            </div>

            {{-- Kolom Kontak & Lokasi --}}
            <div>
                <h3 class="footer-col-title">Lokasi & Kontak</h3>
                <ul class="footer-contact-list">
                    <li class="footer-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>{{ $pengaturanFooter->alamat }}</span>
                    </li>
                    <li class="footer-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <span>Telepon: {{ $pengaturanFooter->telepon }}</span>
                    </li>
                    <li class="footer-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                        </svg>
                        <span>Email: {{ $pengaturanFooter->email }}</span>
                    </li>
                    <li class="footer-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>{{ $pengaturanFooter->jam_operasional }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Baris Hak Cipta & Link Admin --}}
        <div class="footer-bottom">
            <div>
                &copy; {{ date('Y') }} SMK Muhammadiyah Paguyangan. Seluruh hak cipta dilindungi undang-undang.
            </div>
            <div style="display: flex; gap: 1.25rem;">
                <a href="{{ route('kontak') }}" style="color: #94a3b8;">Kontak Sekolah</a>
                <a href="{{ route('admin.login') }}" style="color: #94a3b8;">Akses Petugas Loket</a>
            </div>
        </div>
    </div>
</footer>
