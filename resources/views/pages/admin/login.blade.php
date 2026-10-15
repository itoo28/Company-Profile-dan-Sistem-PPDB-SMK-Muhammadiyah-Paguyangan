@extends('layouts.app')

@section('title', 'Masuk Petugas Administrasi')

@section('content')
<section class="py-section bg-canvas">
    <div class="container container-narrow">
        <div class="login-siswa-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 56px; height: 56px; background-color: var(--primary-deep); color: #ffffff; border-radius: var(--radius-card); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 0.35rem;">
                    Portal Administrasi PPDB
                </h1>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">
                    Area khusus Staf Piket dan Super Administrator SMK Muhammadiyah Paguyangan.
                </p>
            </div>

            @if(session('sukses'))
                <div class="smk-alert smk-alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <div>{{ session('sukses') }}</div>
                </div>
            @endif

            @if($errors->has('auth'))
                <div class="smk-alert smk-alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>{{ $errors->first('auth') }}</div>
                </div>
            @endif

            <form action="{{ route('admin.login.proses') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username" class="form-label">Username Staf <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" placeholder="Username akun staf Anda" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password <span style="color: #ef4444;">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password akun staf Anda" required>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-body); cursor: pointer;">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat sesi masuk di komputer ini</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; background-color: var(--primary-deep); border-color: var(--primary-deep);" id="btnSubmitLoginAdmin">
                    Masuk ke Panel Administrasi
                </button>
            </form>

            <div style="border-top: 1px solid var(--border-default); margin-top: 2rem; padding-top: 1.25rem; text-align: center; font-size: 0.8125rem; color: var(--text-muted);">
                Kendala akses akun staf? Hubungi Super Admin di ruang Pusat Komputer.
            </div>
        </div>
    </div>
</section>
@endsection
