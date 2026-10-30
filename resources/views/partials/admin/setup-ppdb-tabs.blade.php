<nav class="admin-subnav-wrapper" aria-label="Sub-navigasi Setup & Konfigurasi PPDB">
    <a href="{{ route('admin.tahun-ajaran.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}">
        <flux:icon name="calendar-days" class="tab-icon" />
        <span>Tahun Ajaran</span>
    </a>

    <a href="{{ route('admin.gelombang.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.gelombang.*') ? 'active' : '' }}">
        <flux:icon name="calendar" class="tab-icon" />
        <span>Gelombang PPDB</span>
    </a>

    <a href="{{ route('admin.jurusan.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
        <flux:icon name="academic-cap" class="tab-icon" />
        <span>Jurusan Kejuruan</span>
    </a>

    <a href="{{ route('admin.jenis-berkas.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.jenis-berkas.*') ? 'active' : '' }}">
        <flux:icon name="folder" class="tab-icon" />
        <span>Syarat Berkas</span>
    </a>

    <a href="{{ route('admin.pengaturan.index') }}" 
       class="admin-subnav-tab {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
        <flux:icon name="cog-6-tooth" class="tab-icon" />
        <span>Pengaturan PPDB</span>
    </a>
</nav>

