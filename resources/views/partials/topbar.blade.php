@php
    $pengaturanTopbar = \App\Models\PengaturanSistem::instance();
@endphp
<div class="topbar">
    <div class="container">
        <div class="topbar-inner">
            <div class="topbar-info">
                <span class="topbar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>{{ $pengaturanTopbar->jam_operasional }}</span>
                </span>
                <span class="topbar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>Paguyangan, Brebes, Jawa Tengah</span>
                </span>
            </div>
            <div class="topbar-info">
                <span class="topbar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <span>{{ $pengaturanTopbar->telepon }}</span>
                </span>
                <a href="{{ route('admin.login') }}" class="topbar-item" style="color: #cbd5e1; text-decoration: underline;" title="Login Staf Administrasi">
                    <span>Portal Staf</span>
                </a>
            </div>
        </div>
    </div>
</div>
