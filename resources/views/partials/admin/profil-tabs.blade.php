<nav class="admin-subnav-wrapper" aria-label="Sub-navigasi Profil Sekolah">
    <a href="{{ route('admin.informasi-sekolah.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.informasi-sekolah.*') ? 'active' : '' }}">
        <flux:icon name="information-circle" class="tab-icon" />
        <span>Visi, Misi & Sambutan</span>
    </a>

    <a href="{{ route('admin.fasilitas.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.fasilitas.*') ? 'active' : '' }}">
        <flux:icon name="building-office" class="tab-icon" />
        <span>Sarana & Fasilitas</span>
    </a>

    <a href="{{ route('admin.ekstrakurikuler.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.ekstrakurikuler.*') ? 'active' : '' }}">
        <flux:icon name="flag" class="tab-icon" />
        <span>Ekstrakurikuler</span>
    </a>

    <a href="{{ route('admin.galeri.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}">
        <flux:icon name="photo" class="tab-icon" />
        <span>Galeri Foto</span>
    </a>
</nav>

