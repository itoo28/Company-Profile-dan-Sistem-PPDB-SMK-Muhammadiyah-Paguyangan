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

                    <flux:navlist.item href="{{ route('admin.pembayaran.index') }}" icon="banknotes" :current="request()->routeIs('admin.pembayaran.*')">
                        Pembayaran PPDB
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
                        <flux:navlist.item href="{{ route('admin.tahun-ajaran.index') }}" icon="calendar-days" :current="request()->routeIs('admin.tahun-ajaran.*')">
                            Tahun Ajaran
                        </flux:navlist.item>

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

            {{-- Footer Profil Dropdown di Sidebar --}}
            <div class="mt-auto p-3 border-t border-white/10 bg-[#061830]">
                <flux:dropdown position="top" align="start" class="w-full">
                    <button type="button" class="w-full flex items-center gap-2.5 p-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition text-left cursor-pointer group" title="Menu Pengguna (Klik untuk opsi)">
                        <div class="size-8 rounded-full bg-blue-600/30 text-blue-300 font-bold flex items-center justify-center text-xs shrink-0 border border-blue-500/30">
                            {{ strtoupper(substr(auth('web')->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-white truncate group-hover:text-amber-300 transition" title="{{ auth('web')->user()->name }}">
                                {{ auth('web')->user()->name }}
                            </div>
                            <div class="text-[10px] text-amber-400 font-semibold uppercase tracking-wider">
                                {{ auth('web')->user()->isSuperAdmin() ? 'Super Admin' : 'Admin Piket' }}
                            </div>
                        </div>
                        <flux:icon.chevron-up-down class="size-4 text-slate-400 group-hover:text-white shrink-0 transition" />
                    </button>

                    <flux:menu class="w-60 shadow-xl border border-slate-200 bg-white">
                        <div class="px-3 py-2 border-b border-slate-100">
                            <div class="text-xs font-bold text-slate-900 truncate">{{ auth('web')->user()->name }}</div>
                            <div class="text-[11px] text-slate-500 truncate">{{ auth('web')->user()->email ?? auth('web')->user()->username }}</div>
                        </div>

                        <flux:menu.item href="{{ route('beranda') }}" target="_blank" icon="globe-alt">
                            Lihat Web Sekolah
                        </flux:menu.item>

                        <flux:menu.separator />

                        <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                            @csrf
                            <flux:menu.item as="button" type="submit" variant="danger" icon="arrow-left-start-on-rectangle">
                                Keluar (Logout)
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:sidebar>

        {{-- Main Area --}}
        <div class="admin-main">
            {{-- Topbar Admin (Bersih tanpa profil di kanan atas) --}}
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
            </header>

            {{-- Body Konten --}}
            <main class="admin-content">
                @if(session('sukses'))
                    <div class="smk-alert smk-alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div>{{ session('sukses') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="smk-alert smk-alert-danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    <script src="{{ asset('js/theme-ui.js') }}"></script>
    @fluxScripts
    @stack('scripts')
</body>
</html>
