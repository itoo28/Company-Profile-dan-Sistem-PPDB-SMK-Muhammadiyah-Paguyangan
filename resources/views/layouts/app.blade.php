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
        @yield('content')
    </main>

    {{-- Footer Institusi --}}
    @include('partials.footer')

    <!-- Skrip Interaktivitas -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
