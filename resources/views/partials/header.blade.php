<header class="site-header {{ request()->routeIs('beranda') ? 'site-header--hero' : 'site-header--solid' }}">
    <div class="navbar-container">
        <div class="navbar">
            {{-- Brand Logo & Nama Sekolah --}}
            <a href="{{ route('beranda') }}" class="brand-wrapper" aria-label="Beranda SMK Muhammadiyah Paguyangan">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo SMK Muhammadiyah Paguyangan" class="brand-logo" width="44" height="44">
                <div class="brand-text-group">
                    <span class="brand-title brand-title--light">SMK Muhammadiyah</span>
                    <span class="brand-subtitle brand-subtitle--light">Paguyangan, Brebes</span>
                </div>
            </a>

            {{-- Navigasi Desktop Terstruktur Sesuai Tema --}}
            <nav class="nav-menu-capsule" aria-label="Navigasi Utama">
                <a href="{{ route('beranda') }}" class="nav-capsule-link {{ request()->routeIs('beranda') ? 'active' : '' }}" data-nav="beranda" id="navLinkBeranda">
                    Beranda
                </a>

                {{-- Dropdown Menu: Profil Sekolah --}}
                <div class="nav-item-dropdown">
                    <button type="button" 
                            class="nav-capsule-link nav-dropdown-btn {{ request()->routeIs('visi-misi', 'fasilitas', 'ekstrakurikuler', 'prestasi*') ? 'active' : '' }}" 
                            aria-expanded="false" 
                            aria-haspopup="true"
                            id="dropdownProfilBtn">
                        <span>Profil</span>
                        <svg class="dropdown-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="nav-dropdown-menu" role="menu" aria-labelledby="dropdownProfilBtn">
                        <a href="{{ route('visi-misi') }}" class="dropdown-item {{ request()->routeIs('visi-misi') ? 'active' : '' }}" role="menuitem">
                            <span class="dropdown-item-title">Visi & Misi</span>
                            <span class="dropdown-item-desc">Arah haluan & nilai kepribadian sekolah</span>
                        </a>
                        <a href="{{ route('fasilitas') }}" class="dropdown-item {{ request()->routeIs('fasilitas') ? 'active' : '' }}" role="menuitem">
                            <span class="dropdown-item-title">Fasilitas & Bengkel</span>
                            <span class="dropdown-item-desc">Sarana praktikum kejuruan representatif</span>
                        </a>
                        <a href="{{ route('ekstrakurikuler') }}" class="dropdown-item {{ request()->routeIs('ekstrakurikuler') ? 'active' : '' }}" role="menuitem">
                            <span class="dropdown-item-title">Ekstrakurikuler</span>
                            <span class="dropdown-item-desc">Pengembangan bakat, kepanduan HW, & olahraga</span>
                        </a>
                        <a href="{{ route('prestasi') }}" class="dropdown-item {{ request()->routeIs('prestasi*') ? 'active' : '' }}" role="menuitem">
                            <span class="dropdown-item-title">Prestasi Siswa</span>
                            <span class="dropdown-item-desc">Capaian kejuaraan kompetisi kejuruan & sains</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('beranda') }}#jurusan" class="nav-capsule-link" data-nav="jurusan" id="navLinkJurusan">
                    Jurusan
                </a>

                {{-- Dropdown Menu: Warta & Informasi --}}
                <div class="nav-item-dropdown">
                    <button type="button" 
                            class="nav-capsule-link nav-dropdown-btn {{ request()->routeIs('berita*', 'pengumuman', 'galeri') ? 'active' : '' }}" 
                            aria-expanded="false" 
                            aria-haspopup="true"
                            id="dropdownInfoBtn">
                        <span>Informasi</span>
                        <svg class="dropdown-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="nav-dropdown-menu" role="menu" aria-labelledby="dropdownInfoBtn">
                        <a href="{{ route('berita') }}" class="dropdown-item {{ request()->routeIs('berita*') ? 'active' : '' }}" role="menuitem">
                            <span class="dropdown-item-title">Berita & Warta</span>
                            <span class="dropdown-item-desc">Kabar dan liputan kegiatan terbaru</span>
                        </a>
                        <a href="{{ route('pengumuman') }}" class="dropdown-item {{ request()->routeIs('pengumuman') ? 'active' : '' }}" role="menuitem">
                            <span class="dropdown-item-title">Pengumuman Resmi</span>
                            <span class="dropdown-item-desc">Agenda akademik & pemberitahuan penting</span>
                        </a>
                        <a href="{{ route('galeri') }}" class="dropdown-item {{ request()->routeIs('galeri') ? 'active' : '' }}" role="menuitem">
                            <span class="dropdown-item-title">Galeri Dokumentasi</span>
                            <span class="dropdown-item-desc">Dokumentasi visual kegiatan civitas sekolah</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('ppdb.info') }}" class="nav-capsule-link {{ request()->routeIs('ppdb.info') ? 'active' : '' }}">
                    Info PPDB
                </a>

                <a href="{{ route('kontak') }}" class="nav-capsule-link {{ request()->routeIs('kontak') ? 'active' : '' }}">
                    Kontak
                </a>
            </nav>

            {{-- CTA Button & Mobile Toggle --}}
            <div class="nav-cta-group">
                <a href="{{ route('ppdb.daftar') }}" class="btn-pill-cta" id="btnHeaderPPDB">
                    Daftar PPDB
                </a>
                <a href="{{ route('siswa.login') }}" class="btn-pill-secondary btn-sm" id="btnHeaderLoginSiswa">
                    Login Siswa
                </a>
            </div>

            <button class="mobile-toggle mobile-toggle--light" id="mobileMenuToggle" aria-label="Buka Menu Navigasi" aria-expanded="false" aria-controls="mobileDrawer">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Drawer --}}
    <div class="mobile-drawer" id="mobileDrawer" role="dialog" aria-modal="true" aria-label="Menu Navigasi Mobile">
        <div class="mobile-drawer-content">
            <div class="mobile-drawer-header">
                <div class="mobile-drawer-title">Menu Navigasi</div>
                <button type="button" class="mobile-drawer-close" id="mobileDrawerClose" aria-label="Tutup Menu Navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="mobile-nav-group">
                <a href="{{ route('beranda') }}" class="mobile-nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}" id="mobileNavLinkBeranda">
                    Beranda
                </a>
                <a href="{{ route('beranda') }}#jurusan" class="mobile-nav-link" id="mobileNavLinkJurusan">
                    Jurusan
                </a>
            </div>

            <div class="mobile-nav-group">
                <span class="mobile-nav-heading">Profil Sekolah</span>
                <a href="{{ route('visi-misi') }}" class="mobile-nav-link {{ request()->routeIs('visi-misi') ? 'active' : '' }}">Visi & Misi</a>
                <a href="{{ route('fasilitas') }}" class="mobile-nav-link {{ request()->routeIs('fasilitas') ? 'active' : '' }}">Fasilitas & Bengkel</a>
                <a href="{{ route('ekstrakurikuler') }}" class="mobile-nav-link {{ request()->routeIs('ekstrakurikuler') ? 'active' : '' }}">Ekstrakurikuler</a>
                <a href="{{ route('prestasi') }}" class="mobile-nav-link {{ request()->routeIs('prestasi*') ? 'active' : '' }}">Prestasi Siswa</a>
            </div>

            <div class="mobile-nav-group">
                <span class="mobile-nav-heading">Informasi & Warta</span>
                <a href="{{ route('berita') }}" class="mobile-nav-link {{ request()->routeIs('berita*') ? 'active' : '' }}">Berita & Liputan</a>
                <a href="{{ route('pengumuman') }}" class="mobile-nav-link {{ request()->routeIs('pengumuman') ? 'active' : '' }}">Pengumuman Resmi</a>
                <a href="{{ route('galeri') }}" class="mobile-nav-link {{ request()->routeIs('galeri') ? 'active' : '' }}">Galeri Dokumentasi</a>
            </div>

            <div class="mobile-nav-group">
                <span class="mobile-nav-heading">Layanan & Kontak</span>
                <a href="{{ route('ppdb.info') }}" class="mobile-nav-link {{ request()->routeIs('ppdb.info') ? 'active' : '' }}">Info & Alur PPDB</a>
                <a href="{{ route('kontak') }}" class="mobile-nav-link {{ request()->routeIs('kontak') ? 'active' : '' }}">Kontak & Lokasi</a>
            </div>

            <div class="mobile-drawer-cta">
                <a href="{{ route('ppdb.daftar') }}" class="btn btn-primary" style="width: 100%;">Daftar Siswa Baru (PPDB)</a>
                <a href="{{ route('siswa.login') }}" class="btn btn-secondary" style="width: 100%;">Masuk Portal Siswa</a>
                <a href="{{ route('admin.login') }}" class="btn btn-outline btn-sm" style="width: 100%;">Portal Staf Administrasi</a>
            </div>
        </div>
    </div>
</header>
