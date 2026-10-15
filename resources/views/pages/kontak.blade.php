@extends('layouts.app')

@section('title', 'Kontak & Lokasi')
@section('meta_description', 'Alamat, peta lokasi, nomor kontak WhatsApp, dan jam pelayanan loket SMK Muhammadiyah Paguyangan Brebes.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Hubungi Kami</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Kontak & Lokasi Sekolah</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Kami siap memberikan informasi dan mendampingi proses pendaftaran calon peserta didik baru.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container">
        @if(session('sukses'))
            <div class="smk-alert smk-alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div>{{ session('sukses') }}</div>
            </div>
        @endif

        <div class="card-grid-2" style="margin-bottom: 3rem;">
            {{-- Kartu Info Kontak Resmi --}}
            <div style="background-color: #ffffff; padding: 2rem; border-radius: var(--radius-card); border: 1px solid var(--border-default);">
                <span class="section-eyebrow">Informasi Pelayanan</span>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 1.5rem;">
                    Loket Informasi & PPDB
                </h2>

                <div style="display: flex; flex-direction: column; gap: 1.25rem; font-size: 0.9375rem;">
                    <div>
                        <strong style="color: var(--text-heading); display: block; margin-bottom: 0.25rem;">Alamat Sekolah:</strong>
                        <p style="color: var(--text-body);">{{ $pengaturan->alamat }}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <strong style="color: var(--text-heading); display: block; margin-bottom: 0.25rem;">Telepon Kantor:</strong>
                            <p style="color: var(--text-body);">{{ $pengaturan->telepon }}</p>
                        </div>
                        <div>
                            <strong style="color: var(--text-heading); display: block; margin-bottom: 0.25rem;">WhatsApp Panitia:</strong>
                            <p style="color: var(--text-body);">{{ $pengaturan->whatsapp }}</p>
                        </div>
                    </div>

                    <div>
                        <strong style="color: var(--text-heading); display: block; margin-bottom: 0.25rem;">Email Resmi:</strong>
                        <p style="color: var(--text-body);">{{ $pengaturan->email }}</p>
                    </div>

                    <div>
                        <strong style="color: var(--text-heading); display: block; margin-bottom: 0.25rem;">Jam Layanan Loket:</strong>
                        <p style="color: var(--text-body);">{{ $pengaturan->jam_operasional }}</p>
                    </div>
                </div>
            </div>

            {{-- Formulir Kirim Pesan / Pertanyaan --}}
            <div style="background-color: #ffffff; padding: 2rem; border-radius: var(--radius-card); border: 1px solid var(--border-default);">
                <span class="section-eyebrow">Kirim Pesan</span>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 1.25rem;">
                    Ada Pertanyaan Seputar Sekolah?
                </h2>

                <form action="{{ route('kontak.kirim') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="inputNama" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="inputNama" name="nama" value="{{ old('nama') }}" required class="form-control" placeholder="Contoh: Budi Prasetyo">
                        @error('nama')
                            <span style="font-size: 0.75rem; color: var(--status-danger-text);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputKontak" class="form-label">No. WhatsApp / Email <span class="required">*</span></label>
                        <input type="text" id="inputKontak" name="kontak" value="{{ old('kontak') }}" required class="form-control" placeholder="08xxxxxxxxxx atau email@contoh.com">
                        @error('kontak')
                            <span style="font-size: 0.75rem; color: var(--status-danger-text);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputSubjek" class="form-label">Subjek Pertanyaan <span class="required">*</span></label>
                        <input type="text" id="inputSubjek" name="subjek" value="{{ old('subjek') }}" required class="form-control" placeholder="Contoh: Pertanyaan Syarat Jalur Prestasi">
                        @error('subjek')
                            <span style="font-size: 0.75rem; color: var(--status-danger-text);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputPesan" class="form-label">Isi Pesan <span class="required">*</span></label>
                        <textarea id="inputPesan" name="pesan" required class="form-control" placeholder="Tuliskan pertanyaan Anda secara lengkap...">{{ old('pesan') }}</textarea>
                        @error('pesan')
                            <span style="font-size: 0.75rem; color: var(--status-danger-text);">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Kirim Pertanyaan
                    </button>
                </form>
            </div>
        </div>

        {{-- Peta Lokasi Google Maps Embed --}}
        <div style="background-color: #ffffff; padding: 1.5rem; border-radius: var(--radius-card); border: 1px solid var(--border-default);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 0;">
                    Peta Lokasi SMK Muhammadiyah Paguyangan di Google Maps
                </h3>
                <a href="https://maps.google.com/?q=-7.2948315297146245,109.03520916296365" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">
                    Buka di Google Maps
                </a>
            </div>
            <div style="border-radius: var(--radius-md); overflow: hidden; height: 380px; background-color: var(--surface-subdued);">
                <iframe 
                    title="Peta Lokasi SMK Muhammadiyah Paguyangan"
                    src="{{ $pengaturan->maps_embed_url ?? 'https://maps.google.com/maps?q=-7.2948315297146245,109.03520916296365&hl=id&z=16&output=embed' }}" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>
@endsection
