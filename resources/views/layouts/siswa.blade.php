<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Portal Siswa') | SMK Muhammadiyah Paguyangan</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/company-profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siswa-portal.css') }}">
    @stack('styles')
</head>
<body class="siswa-layout">
    {{-- Header Khusus Portal Calon Siswa --}}
    <header class="siswa-topbar">
        <div class="container siswa-topbar-container">
            <a href="{{ route('siswa.dashboard') }}" class="siswa-brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo SMK" width="40" height="40">
                <div>
                    <div class="siswa-brand-name">SMK Muhammadiyah Paguyangan</div>
                    <div class="siswa-brand-portal">Portal Calon Siswa Baru</div>
                </div>
            </a>

            <div class="siswa-user-menu">
                @if(auth('siswa')->check())
                    <span class="siswa-user-chip" title="{{ auth('siswa')->user()->nama_lengkap }} ({{ auth('siswa')->user()->nomor_pendaftaran }})">
                        <strong>{{ auth('siswa')->user()->nama_lengkap }}</strong> 
                        <span class="user-chip-no">({{ auth('siswa')->user()->nomor_pendaftaran }})</span>
                    </span>
                    <form action="{{ route('siswa.logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-logout-siswa">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </header>

    {{-- Main Container --}}
    <main style="flex: 1; padding: 2rem 0;">
        <div class="container">
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

            @yield('content')
        </div>
    </main>

    <footer style="background-color: #ffffff; border-top: 1px solid var(--border-default); padding: 1.25rem 0; font-size: 0.8125rem; color: var(--text-muted); text-align: center;">
        <div class="container">
            &copy; {{ date('Y') }} Panitia PPDB SMK Muhammadiyah Paguyangan. Layanan Bantuan CS WhatsApp: {{ \App\Models\PengaturanSistem::instance()->whatsapp }}.
        </div>
    </footer>

    <script src="{{ asset('js/theme-ui.js') }}"></script>
    @stack('scripts')
</body>
</html>
