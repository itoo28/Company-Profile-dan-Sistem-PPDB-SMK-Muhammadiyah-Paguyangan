<nav class="admin-subnav-wrapper" aria-label="Sub-navigasi Warta Sekolah">
    <a href="{{ route('admin.berita.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
        <flux:icon name="newspaper" class="tab-icon" />
        <span>Kelola Berita</span>
    </a>

    <a href="{{ route('admin.prestasi.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.prestasi.*') ? 'active' : '' }}">
        <flux:icon name="trophy" class="tab-icon" />
        <span>Kelola Prestasi</span>
    </a>

    @if(auth('web')->user()->isSuperAdmin())
        <a href="{{ route('admin.pengumuman.index') }}" 
           class="admin-subnav-tab {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
            <flux:icon name="megaphone" class="tab-icon" />
            <span>Pengumuman Resmi</span>
        </a>
    @endif
</nav>

