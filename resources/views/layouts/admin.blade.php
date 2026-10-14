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
        {{-- Sidebar Kiri (Flux UI + DESIGN.md Section 5.3: Deep Royal Navy #092347) --}}
        <flux:sidebar class="admin-sidebar p-0 gap-0" id="adminSidebar">
            <flux:sidebar.header class="admin-sidebar-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo SMK" width="36" height="36">
                    <div>
                        <div class="admin-sidebar-title">SMK Muhammadiyah</div>
                        <div class="admin-sidebar-subtitle">Panel Administrasi</div>
                    </div>
                </div>
                <button type="button" class="admin-sidebar-close" onclick="toggleSidebar()" aria-label="Tutup Menu">&times;</button>
            </flux:sidebar.header>

            <flux:navlist class="admin-nav">
                <flux:navlist.item href="{{ route('admin.dashboard') }}" icon="home" :current="request()->routeIs('admin.dashboard')">
                    Dashboard
                </flux:navlist.item>

                <flux:navlist.group heading="Operasional PPDB">
                    <flux:navlist.item href="{{ route('admin.pendaftar.index') }}" icon="users" :current="request()->routeIs('admin.pendaftar.index', 'admin.pendaftar.show')">
                        Data Pendaftar
                    </flux:navlist.item>

                    <flux:navlist.item href="{{ route('admin.pendaftar.baru') }}" icon="user-plus" :current="request()->routeIs('admin.pendaftar.baru')">
                        Pendaftaran Offline
                    </flux:navlist.item>

                    <flux:navlist.item href="{{ route('admin.seragam.index') }}" icon="shopping-bag" :current="request()->routeIs('admin.seragam.*')">
                        Loket Seragam
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Warta & Publikasi">
                    <flux:navlist.item href="{{ route('admin.berita.index') }}" icon="newspaper" :current="request()->routeIs('admin.berita.*')">
                        Kelola Berita
                    </flux:navlist.item>

                    <flux:navlist.item href="{{ route('admin.prestasi.index') }}" icon="trophy" :current="request()->routeIs('admin.prestasi.*')">
                        Kelola Prestasi
                    </flux:navlist.item>
                </flux:navlist.group>

                {{-- Menu Khusus Super Admin (FR-15, FR-45 s.d. FR-48) --}}
                @if(auth('web')->user()->isSuperAdmin())
                    <flux:navlist.group heading="Master Data & Konfigurasi">
                        <flux:navlist.item href="{{ route('admin.gelombang.index') }}" icon="calendar" :current="request()->routeIs('admin.gelombang.*')">
                            Gelombang PPDB
                        </flux:navlist.item>

                        <flux:navlist.item href="{{ route('admin.jurusan.index') }}" icon="academic-cap" :current="request()->routeIs('admin.jurusan.*')">
                            Jurusan Kejuruan
                        </flux:navlist.item>

                        <flux:navlist.item href="{{ route('admin.jenis-berkas.index') }}" icon="folder" :current="request()->routeIs('admin.jenis-berkas.*')">
                            Master Berkas
                        </flux:navlist.item>

                        <flux:navlist.item href="{{ route('admin.pesan-masuk.index') }}" icon="envelope" :current="request()->routeIs('admin.pesan-masuk.*')">
                            Inbox Kontak
                        </flux:navlist.item>

                        <flux:navlist.item href="{{ route('admin.ekspor.index') }}" icon="arrow-down-tray" :current="request()->routeIs('admin.ekspor.*')">
                            Ekspor Laporan
                        </flux:navlist.item>

                        <flux:navlist.item href="{{ route('admin.pengaturan.index') }}" icon="cog-6-tooth" :current="request()->routeIs('admin.pengaturan.*')">
                            Pengaturan PPDB
                        </flux:navlist.item>

                        <flux:navlist.item href="{{ route('admin.staf.index') }}" icon="user-group" :current="request()->routeIs('admin.staf.*')">
                            Akun Staf Piket
                        </flux:navlist.item>

                        <flux:navlist.item href="{{ route('admin.log.index') }}" icon="clipboard-document-list" :current="request()->routeIs('admin.log.*')">
                            Log Aktivitas
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>
        </flux:sidebar>

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
