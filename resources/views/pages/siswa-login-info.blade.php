@extends('layouts.app')

@section('title', 'Login Calon Siswa')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Portal PPDB</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Masuk Portal Calon Siswa</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Akses portal mandiri untuk mengunggah berkas persyaratan dan memantau status verifikasi panitia.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container container-narrow">
        <div style="background-color: #ffffff; border: 1px solid var(--border-default); border-radius: var(--radius-card); padding: 2.5rem;">
            <div style="max-width: 440px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h2 style="font-size: 1.375rem; font-weight: 800; color: var(--primary-deep);">
                        Masuk Portal Siswa
                    </h2>
                    <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">
                        Gunakan Nomor Pendaftaran dan Tanggal Lahir (DDMMYYYY)
                    </p>
                </div>

                <div style="background-color: var(--primary-surface); border: 1px solid #bfdbfe; padding: 1rem; border-radius: var(--radius-md); font-size: 0.8125rem; color: var(--primary-deep); margin-bottom: 1.5rem;">
                    <strong>Petunjuk Akses:</strong><br>
                    Username: Nomor Pendaftaran (contoh: <code>PPDB/2026/0001</code>)<br>
                    Password: Tanggal Lahir tanpa spasi (contoh: <code>15082009</code> untuk 15 Agustus 2009).
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Nomor Pendaftaran</label>
                        <input type="text" class="form-control" placeholder="PPDB/2026/xxxx" disabled style="background-color: var(--surface-subdued);">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir (DDMMYYYY)</label>
                        <input type="password" class="form-control" placeholder="DDMMYYYY" disabled style="background-color: var(--surface-subdued);">
                    </div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted); text-align: center;">
                        <em>Portal autentikasi calon siswa aktif pada Tahap T2 setelah fase registrasi formulir.</em>
                    </div>
                    <a href="{{ route('ppdb.info') }}" class="btn btn-primary" style="width: 100%;">
                        Pelajari Alur Pendaftaran PPDB
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
