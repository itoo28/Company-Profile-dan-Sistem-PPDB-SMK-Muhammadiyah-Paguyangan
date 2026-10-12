@extends('layouts.app')

@section('title', 'Portal Masuk Staf')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Administrasi Sekolah</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Portal Petugas & Administrator</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Akses khusus untuk Super Administrator dan Petugas Piket PPDB SMK Muhammadiyah Paguyangan.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container container-narrow">
        <div style="background-color: #ffffff; border: 1px solid var(--border-default); border-radius: var(--radius-card); padding: 2.5rem;">
            <div style="max-width: 440px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h2 style="font-size: 1.375rem; font-weight: 800; color: var(--primary-deep);">
                        Masuk Panel Admin
                    </h2>
                    <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">
                        Area operasional verifikasi berkas dan manajemen berita
                    </p>
                </div>

                <div style="background-color: var(--primary-surface); border: 1px solid #bfdbfe; padding: 1rem; border-radius: var(--radius-md); font-size: 0.8125rem; color: var(--primary-deep); margin-bottom: 1.5rem;">
                    <strong>Akun Staf Awal dari Seeder:</strong><br>
                    • Super Admin: <code>superadmin</code> / <code>admin123</code><br>
                    • Admin Piket: <code>adminpiket</code> / <code>piket123</code>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Username Staf</label>
                        <input type="text" class="form-control" value="superadmin" placeholder="Masukkan username" disabled style="background-color: var(--surface-subdued);">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" value="••••••••" placeholder="Masukkan password" disabled style="background-color: var(--surface-subdued);">
                    </div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted); text-align: center;">
                        <em>Autentikasi admin dua guard dan panel admin terfilter peran dipersiapkan untuk integrasi operasional.</em>
                    </div>
                    <a href="{{ route('beranda') }}" class="btn btn-secondary" style="width: 100%;">
                        Kembali ke Halaman Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
