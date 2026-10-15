<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Beranda') | SMK Muhammadiyah Paguyangan</title>
    <meta name="description" content="@yield('meta_description', 'Situs Resmi SMK Muhammadiyah Paguyangan Brebes. Pusat keunggulan vokasi kejuruan otomotif, jaringan komputer, dan administrasi bisnis.')">

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">

    <!-- Google Fonts: Plus Jakarta Sans (DESIGN.md Section 3) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Desain Resmi -->
    <link rel="stylesheet" href="{{ asset('css/company-profile.css') }}">
    @stack('styles')
</head>
<body>
    {{-- Header & Navigasi Utama --}}
    @include('partials.header')

    {{-- Konten Utama Halaman --}}
    <main id="main-content">
        @if(session('sukses'))
            <div class="container" style="padding-top: 1.5rem;">
                <div class="smk-alert smk-alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <div>{{ session('sukses') }}</div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container" style="padding-top: 1.5rem;">
                <div class="smk-alert smk-alert-danger">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer Institusi --}}
    @include('partials.footer')

    <!-- Skrip Interaktivitas -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/theme-ui.js') }}"></script>
    @stack('scripts')
</body>
</html>
