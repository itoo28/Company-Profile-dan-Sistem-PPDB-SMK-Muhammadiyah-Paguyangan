@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Operasional PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Pengaturan Sistem & PPDB')
@section('header-subtitle', 'Konfigurasi batas waktu unggah berkas, benefit promo pendaftar, dan identitas kontak sekolah')

@section('content')
<form action="{{ route('admin.pengaturan.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; align-items: start;">
        
        <!-- Konfigurasi Operasional PPDB -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Parameter Operasional PPDB</h3>
            </div>
            <div class="admin-card-body">
                <div class="form-group mb-3">
                    <label for="batas_waktu_berkas" class="form-label">
                        Batas Waktu Lockdown Berkas (Global)
                    </label>
                    <input type="datetime-local" id="batas_waktu_berkas" name="batas_waktu_berkas" 
                           class="form-control @error('batas_waktu_berkas') is-invalid @enderror" 
                           value="{{ old('batas_waktu_berkas', $pengaturan->batas_waktu_berkas ? $pengaturan->batas_waktu_berkas->format('Y-m-d\TH:i') : '') }}">
                    <small class="form-help">
                        Jika tanggal ini telah lewat, calon siswa tidak dapat lagi mengunggah atau mengganti berkas kecuali dibuka kunci (bypass lockdown) oleh Admin.
                    </small>
                    @error('batas_waktu_berkas')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="max_file_size_mb" class="form-label">
                        Batas Maksimal Ukuran Berkas (MB) <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="number" id="max_file_size_mb" name="max_file_size_mb" min="1" max="20"
                           class="form-control @error('max_file_size_mb') is-invalid @enderror" 
                           value="{{ old('max_file_size_mb', $pengaturan->max_file_size_mb ?? 2) }}" required>
                    <small class="form-help">
                        Batas upload per file dokumen/foto (Default: 2 MB, Maks: 20 MB).
                    </small>
                    @error('max_file_size_mb')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="border-top: 1px solid #f1f5f9; padding-top: 1.25rem; margin-top: 1.25rem;">
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #092347; margin: 0 0 12px 0;">Benefit Promo Pendaftar Awal</h4>

                    <div class="form-group mb-3">
                        <label for="kuota_promo_benefit" class="form-label">
                            Kuota Pendaftar Promo Bebas SPP 3 Bulan <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" id="kuota_promo_benefit" name="kuota_promo_benefit" min="0" max="1000"
                               class="form-control @error('kuota_promo_benefit') is-invalid @enderror" 
                               value="{{ old('kuota_promo_benefit', $pengaturan->kuota_promo_benefit ?? 100) }}" required>
                        <small class="form-help">
                            Kuota pendaftar pertama (misal: 100 siswa) yang berhak mendapat benefit SPP gratis 3 bulan setelah seluruh berkas diverifikasi.
                        </small>
                        @error('kuota_promo_benefit')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.875rem; font-weight: 600; color: #092347;">
                            <input type="checkbox" name="is_promo_aktif" value="1" {{ old('is_promo_aktif', $pengaturan->is_promo_aktif ?? true) ? 'checked' : '' }} style="width: 16px; height: 16px;">
                            Aktifkan Program Benefit Promo 100 Pendaftar Pertama
                        </label>
                        <small class="form-help" style="margin-left: 24px;">
                            Jika dinonaktifkan, pendaftar baru tidak akan dialokasikan nomor benefit promo secara otomatis.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identitas dan Kontak Sekolah -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Identitas & Kontak Resmi Sekolah</h3>
            </div>
            <div class="admin-card-body">
                <div class="form-group mb-3">
                    <label for="nama_sekolah" class="form-label">
                        Nama Sekolah <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="nama_sekolah" name="nama_sekolah" 
                           class="form-control @error('nama_sekolah') is-invalid @enderror" 
                           value="{{ old('nama_sekolah', $pengaturan->nama_sekolah) }}" required>
                    @error('nama_sekolah')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="slogan" class="form-label">Slogan / Tagline Sekolah</label>
                    <input type="text" id="slogan" name="slogan" 
                           class="form-control @error('slogan') is-invalid @enderror" 
                           value="{{ old('slogan', $pengaturan->slogan) }}">
                    @error('slogan')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="alamat" class="form-label">
                        Alamat Lengkap <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea id="alamat" name="alamat" rows="2" 
                              class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $pengaturan->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;" class="mb-3">
                    <div>
                        <label for="telepon" class="form-label">
                            No. Telepon Kantor <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="telepon" name="telepon" 
                               class="form-control @error('telepon') is-invalid @enderror" 
                               value="{{ old('telepon', $pengaturan->telepon) }}" required>
                        @error('telepon')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="whatsapp" class="form-label">
                            WhatsApp Resmi <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="whatsapp" name="whatsapp" 
                               class="form-control @error('whatsapp') is-invalid @enderror" 
                               value="{{ old('whatsapp', $pengaturan->whatsapp) }}" required>
                        @error('whatsapp')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="email" class="form-label">
                        Alamat Email Resmi <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="email" id="email" name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $pengaturan->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="jam_operasional" class="form-label">
                        Jam Pelayanan / Operasional <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="jam_operasional" name="jam_operasional" 
                           class="form-control @error('jam_operasional') is-invalid @enderror" 
                           value="{{ old('jam_operasional', $pengaturan->jam_operasional) }}" required>
                    @error('jam_operasional')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="maps_embed_url" class="form-label">URL Embed Google Maps</label>
                    <textarea id="maps_embed_url" name="maps_embed_url" rows="2" 
                              class="form-control @error('maps_embed_url') is-invalid @enderror" 
                              placeholder="https://www.google.com/maps/embed?pb=...">{{ old('maps_embed_url', $pengaturan->maps_embed_url) }}</textarea>
                    @error('maps_embed_url')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 12px;">
        <button type="submit" class="btn btn-primary" style="padding: 10px 28px; font-size: 0.95rem;">
            Simpan Perubahan Pengaturan
        </button>
    </div>
</form>
@endsection
