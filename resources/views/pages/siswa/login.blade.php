@extends('layouts.app')

@section('title', 'Masuk Portal Calon Siswa')
@section('meta_description', 'Akses masuk Portal Siswa PPDB SMK Muhammadiyah Paguyangan untuk melengkapi berkas dan memantau status verifikasi.')

@section('content')
<section class="py-section bg-canvas">
    <div class="container container-narrow">
        <div class="login-siswa-card">
            {{-- Header Card --}}
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 56px; height: 56px; background-color: var(--primary-surface); color: var(--primary-base); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 0.35rem;">
                    Portal Calon Siswa
                </h1>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">
                    Masukkan Nomor Pendaftaran dan Kata Sandi / Tanggal Lahir Anda untuk memantau status verifikasi dan berkas.
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

            <form action="{{ route('siswa.login.proses') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nomor_pendaftaran" class="form-label">
                        Nomor Pendaftaran <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" 
                           name="nomor_pendaftaran" 
                           id="nomor_pendaftaran" 
                           class="form-control" 
                           value="{{ old('nomor_pendaftaran') }}" 
                           placeholder="Contoh: PPDB/2026/0001" 
                           required 
                           autofocus>
                    <span class="form-help">Sesuai yang tertera pada lembar bukti pendaftaran Anda.</span>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Kata Sandi / Tanggal Lahir <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control" 
                               value="{{ old('password') }}" 
                               placeholder="Tanggal lahir (DDMMYYYY) atau kata sandi Anda" 
                               required 
                               autocomplete="current-password"
                               style="padding-right: 2.75rem;">
                        <button type="button" 
                                id="togglePasswordBtn"
                                onclick="toggleLoginPassword()"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0.25rem; display: flex; align-items: center;">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <span class="form-help">
                        Gunakan tanggal lahir (format: <strong>DDMMYYYY</strong>, contoh: <em>21072008</em>) untuk login pertama kali, atau kata sandi kustom jika sudah pernah mengubahnya.
                    </span>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-body); cursor: pointer;">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat sesi masuk di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;" id="btnSubmitLoginSiswa">
                    Masuk ke Portal Siswa
                </button>
            </form>

            <div style="border-top: 1px solid var(--border-default); margin-top: 2rem; padding-top: 1.25rem; text-align: center; font-size: 0.8125rem; color: var(--text-muted);">
                Belum mendaftar sebagai calon siswa baru?<br>
                <a href="{{ route('ppdb.daftar') }}" style="font-weight: 700; color: var(--primary-base); text-decoration: underline;">
                    Isi Formulir Pendaftaran Online Di Sini
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    function toggleLoginPassword() {
        var input = document.getElementById('password');
        var icon = document.getElementById('eyeIcon');
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }
</script>
@endsection
