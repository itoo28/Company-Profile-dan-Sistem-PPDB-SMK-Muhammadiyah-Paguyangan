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
    <noscript>
        <style>
            .reveal-on-scroll {
                opacity: 1 !important;
                transform: none !important;
            }
        </style>
    </noscript>
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

    {{-- Floating WhatsApp Concierge Button (Poin 3.3) --}}
    @php
        $pengaturanWa = \App\Models\PengaturanSistem::instance();
        $waRaw = (!empty($pengaturanWa) && !empty($pengaturanWa->whatsapp)) ? $pengaturanWa->whatsapp : '0857-2345-6789';
        $waClean = preg_replace('/[^0-9]/', '', $waRaw);
        if (str_starts_with($waClean, '0')) {
            $waClean = '62' . substr($waClean, 1);
        }
        if (empty($waClean)) {
            $waClean = '6285723456789';
        }
        $waMsg = 'Halo Panitia PPDB SMK Muhammadiyah Paguyangan, saya ingin berkonsultasi mengenai pendaftaran siswa baru.';
        $waUrl = 'https://wa.me/' . $waClean . '?text=' . urlencode($waMsg);
    @endphp
    <aside class="floating-wa-wrapper" aria-label="Layanan Konsultasi WhatsApp">
        <a href="{{ $waUrl }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="floating-wa-btn" 
           id="floatingWaBtn"
           aria-label="Konsultasi PPDB via WhatsApp ({{ $waRaw }})">
            <span class="floating-wa-icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </span>
            <span class="floating-wa-label">Konsultasi PPDB</span>
        </a>
    </aside>

    <!-- Skrip Interaktivitas -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/theme-ui.js') }}"></script>
    @stack('scripts')
</body>
</html>
