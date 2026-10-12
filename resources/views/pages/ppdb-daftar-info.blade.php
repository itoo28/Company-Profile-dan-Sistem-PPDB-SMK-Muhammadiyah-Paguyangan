@extends('layouts.app')

@section('title', 'Pendaftaran PPDB')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Portal PPDB</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Pendaftaran Peserta Didik Baru</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            SMK Muhammadiyah Paguyangan Tahun Ajaran 2026/2027.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container container-narrow">
        <div style="background-color: #ffffff; border: 1px solid var(--border-default); border-radius: var(--radius-card); padding: 2.5rem; text-align: center;">
            <div style="width: 64px; height: 64px; background-color: var(--primary-surface); color: var(--primary-base); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
            </div>

            <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 0.75rem;">
                Formulir Pendaftaran Siswa Baru
            </h2>
            <p style="font-size: 1rem; color: var(--text-body); max-width: 580px; margin: 0 auto 1.75rem auto; line-height: 1.6;">
                Modul pendaftaran mandiri (Fase 1: Formulir Data Diri, NIK, NISN, Asal Sekolah, dan Pembuatan Nomor Pendaftaran) siap diimplementasikan pada Tahap T2 sesuai spesifikasi PRD.
            </p>

            <div style="background-color: var(--canvas-bg); padding: 1.25rem; border-radius: var(--radius-md); text-align: left; margin-bottom: 2rem; font-size: 0.875rem; border: 1px solid var(--border-default);">
                <strong style="color: var(--primary-deep); display: block; margin-bottom: 0.5rem;">Dokumen yang Perlu Disiapkan Sebelum Mendaftar:</strong>
                <ul style="padding-left: 1.25rem; display: flex; flex-direction: column; gap: 0.375rem; color: var(--text-body);">
                    <li>Nomor Induk Kependudukan (NIK 16 digit pada KK)</li>
                    <li>Nomor Induk Siswa Nasional (NISN 10 digit dari SMP/MTs)</li>
                    <li>Nomor WhatsApp aktif orang tua atau wali untuk konfirmasi</li>
                    <li>Surat Keterangan Lulus (SKL) atau Ijazah SMP/MTs</li>
                </ul>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('ppdb.info') }}" class="btn btn-primary">
                    Lihat Alur & Persyaratan Lengkap
                </a>
                <a href="{{ route('beranda') }}" class="btn btn-secondary">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
