<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin Panel') | SMK Muhammadiyah Paguyangan</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin-portal.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @stack('styles')
</head>
<body>
    <div class="admin-backdrop" id="adminBackdrop" onclick="toggleSidebar()"></div>
    <div class="admin-wrapper">
        {{-- Sidebar Kiri (DESIGN.md Section 5.3: Deep Royal Navy #092347) --}}
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo SMK" width="36" height="36">
                    <div>
                        <div class="admin-sidebar-title">SMK Muhammadiyah</div>
                        <div class="admin-sidebar-subtitle">Panel Administrasi</div>
                    </div>
                </div>
                <button type="button" class="admin-sidebar-close" onclick="toggleSidebar()" aria-label="Tutup Menu">&times;</button>
            </div>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="9"></rect>
                        <rect x="14" y="3" width="7" height="5"></rect>
                        <rect x="14" y="12" width="7" height="9"></rect>
                        <rect x="3" y="16" width="7" height="5"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div class="admin-nav-group-title">Operasional PPDB</div>

                <a href="{{ route('admin.pendaftar.index') }}" class="admin-nav-item {{ request()->routeIs('admin.pendaftar.index', 'admin.pendaftar.show') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Data Pendaftar</span>
                </a>

                <a href="{{ route('admin.pendaftar.baru') }}" class="admin-nav-item {{ request()->routeIs('admin.pendaftar.baru') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Pendaftaran Offline</span>
                </a>

                <a href="{{ route('admin.seragam.index') }}" class="admin-nav-item {{ request()->routeIs('admin.seragam.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 1.96v.58a2 2 0 0 0 1.09 1.76l2.63 1.32V21a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9.08l2.63-1.32A2 2 0 0 0 21.72 6v-.58a2 2 0 0 0-1.34-1.96z"></path>
                    </svg>
                    <span>Loket Seragam</span>
                </a>

                <div class="admin-nav-group-title">Warta & Publikasi</div>

                <a href="{{ route('admin.berita.index') }}" class="admin-nav-item {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path>
                        <path d="M18 14h-8"></path>
                        <path d="M15 18h-5"></path>
                        <path d="M10 6h8v4h-8V6Z"></path>
                    </svg>
                    <span>Kelola Berita</span>
                </a>

                <a href="{{ route('admin.prestasi.index') }}" class="admin-nav-item {{ request()->routeIs('admin.prestasi.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                    <span>Kelola Prestasi</span>
                </a>

                {{-- Menu Khusus Super Admin (FR-15, FR-45 s.d. FR-48) --}}
                @if(auth('web')->user()->isSuperAdmin())
                    <div class="admin-nav-group-title">Master Data & Konfigurasi</div>

                    <a href="{{ route('admin.gelombang.index') }}" class="admin-nav-item {{ request()->routeIs('admin.gelombang.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Gelombang PPDB</span>
                    </a>

                    <a href="{{ route('admin.jurusan.index') }}" class="admin-nav-item {{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span>Jurusan Kejuruan</span>
                    </a>

                    <a href="{{ route('admin.jenis-berkas.index') }}" class="admin-nav-item {{ request()->routeIs('admin.jenis-berkas.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        <span>Master Berkas</span>
                    </a>

                    <a href="{{ route('admin.pesan-masuk.index') }}" class="admin-nav-item {{ request()->routeIs('admin.pesan-masuk.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span>Inbox Kontak</span>
                    </a>

                    <a href="{{ route('admin.ekspor.index') }}" class="admin-nav-item {{ request()->routeIs('admin.ekspor.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Laporan</span>
                    </a>

                    <a href="{{ route('admin.pengaturan.index') }}" class="admin-nav-item {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Pengaturan PPDB</span>
                    </a>

                    <a href="{{ route('admin.staf.index') }}" class="admin-nav-item {{ request()->routeIs('admin.staf.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <span>Akun Staf Piket</span>
                    </a>

                    <a href="{{ route('admin.log.index') }}" class="admin-nav-item {{ request()->routeIs('admin.log.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="4 17 10 11 4 5"></polyline>
                            <line x1="12" y1="19" x2="20" y2="19"></line>
                        </svg>
                        <span>Log Aktivitas</span>
                    </a>
                @endif
            </nav>
        </aside>

        {{-- Main Area --}}
        <div class="admin-main">
            {{-- Topbar Admin --}}
            <header class="admin-topbar">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <button type="button" class="admin-sidebar-toggle" onclick="toggleSidebar()" aria-label="Buka Menu Navigasi">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="admin-topbar-breadcrumb">
                        <span class="admin-topbar-school">SMK Muhammadiyah Paguyangan</span>
                        <span class="admin-topbar-sep">&bull;</span>
                        <span class="admin-topbar-module">Sistem PPDB & Administrasi</span>
                    </div>
                </div>

                <div class="admin-user-profile">
                    <a href="{{ route('beranda') }}" target="_blank" class="admin-site-link" title="Buka Halaman Depan Web Sekolah">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <span>Lihat Web</span>
                    </a>

                    <div class="admin-user-info-box">
                        <flux:badge color="{{ auth('web')->user()->isSuperAdmin() ? 'blue' : 'amber' }}" size="sm">
                            {{ auth('web')->user()->isSuperAdmin() ? 'Super Admin' : 'Admin Piket' }}
                        </flux:badge>
                        <span class="admin-user-name">{{ auth('web')->user()->name }}</span>
                    </div>

                    <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="admin-btn-logout" title="Keluar dari Panel">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Body Konten --}}
            <main class="admin-content">
                @if(session('sukses'))
                    <div class="admin-alert admin-alert-success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div>{{ session('sukses') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="admin-alert admin-alert-danger">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                {{-- Page Header Otomatis jika didefinisikan di view --}}
                @hasSection('header-title')
                    <div class="admin-page-header">
                        <div>
                            <h1 class="admin-page-title">@yield('header-title')</h1>
                            @hasSection('header-subtitle')
                                <p class="admin-page-subtitle">@yield('header-subtitle')</p>
                            @endif
                        </div>
                        @hasSection('header-action')
                            <div class="admin-page-action">
                                @yield('header-action')
                            </div>
                        @endif
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>
    @fluxScripts
    @stack('scripts')
</body>
</html>
