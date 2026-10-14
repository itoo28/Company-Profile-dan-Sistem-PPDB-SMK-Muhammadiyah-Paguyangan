@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Operasional PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Pengaturan Sistem & PPDB')
@section('header-subtitle', 'Konfigurasi batas waktu unggah berkas, benefit promo pendaftar, dan identitas kontak sekolah')

@section('content')
<form action="{{ route('admin.pengaturan.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        <!-- Konfigurasi Operasional PPDB -->
        <flux:card class="p-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 m-0">Parameter Operasional PPDB</h3>
            </div>
            
            <div class="mb-4">
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
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label for="max_file_size_mb" class="form-label">
                    Batas Maksimal Ukuran Berkas (MB) <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="max_file_size_mb" name="max_file_size_mb" min="1" max="20"
                       class="form-control @error('max_file_size_mb') is-invalid @enderror" 
                       value="{{ old('max_file_size_mb', $pengaturan->max_file_size_mb ?? 2) }}" required>
                <small class="form-help">
                    Batas upload per file dokumen/foto (Default: 2 MB, Maks: 20 MB).
                </small>
                @error('max_file_size_mb')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Benefit Promo Pendaftar Awal</h4>

                <div class="mb-4">
                    <label for="kuota_promo_benefit" class="form-label">
                        Kuota Pendaftar Promo Bebas SPP 3 Bulan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="kuota_promo_benefit" name="kuota_promo_benefit" min="0" max="1000"
                           class="form-control @error('kuota_promo_benefit') is-invalid @enderror" 
                           value="{{ old('kuota_promo_benefit', $pengaturan->kuota_promo_benefit ?? 100) }}" required>
                    <small class="form-help">
                        Kuota pendaftar pertama (misal: 100 siswa) yang berhak mendapat benefit SPP gratis 3 bulan setelah seluruh berkas diverifikasi.
                    </small>
                    @error('kuota_promo_benefit')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-900 m-0">
                        <input type="checkbox" name="is_promo_aktif" value="1" {{ old('is_promo_aktif', $pengaturan->is_promo_aktif ?? true) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                        <span>Aktifkan Program Benefit Promo 100 Pendaftar Pertama</span>
                    </label>
                    <small class="text-slate-500 text-xs block mt-1 ml-5">
                        Jika dinonaktifkan, pendaftar baru tidak akan dialokasikan nomor benefit promo secara otomatis.
                    </small>
                </div>
            </div>
        </flux:card>

        <!-- Identitas dan Kontak Sekolah -->
        <flux:card class="p-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 m-0">Identitas & Kontak Resmi Sekolah</h3>
            </div>
            
            <div class="mb-3">
                <label for="nama_sekolah" class="form-label">
                    Nama Sekolah <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_sekolah" name="nama_sekolah" 
                       class="form-control @error('nama_sekolah') is-invalid @enderror" 
                       value="{{ old('nama_sekolah', $pengaturan->nama_sekolah) }}" required>
                @error('nama_sekolah')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="slogan" class="form-label">Slogan / Tagline Sekolah</label>
                <input type="text" id="slogan" name="slogan" 
                       class="form-control @error('slogan') is-invalid @enderror" 
                       value="{{ old('slogan', $pengaturan->slogan) }}">
                @error('slogan')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">
                    Alamat Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea id="alamat" name="alamat" rows="2" 
                          class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $pengaturan->alamat) }}</textarea>
                @error('alamat')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="telepon" class="form-label">
                        No. Telepon Kantor <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="telepon" name="telepon" 
                           class="form-control @error('telepon') is-invalid @enderror" 
                           value="{{ old('telepon', $pengaturan->telepon) }}" required>
                    @error('telepon')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="whatsapp" class="form-label">
                        WhatsApp Resmi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="whatsapp" name="whatsapp" 
                           class="form-control @error('whatsapp') is-invalid @enderror" 
                           value="{{ old('whatsapp', $pengaturan->whatsapp) }}" required>
                    @error('whatsapp')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">
                    Alamat Email Resmi <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email', $pengaturan->email) }}" required>
                @error('email')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jam_operasional" class="form-label">
                    Jam Pelayanan / Operasional <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="jam_operasional" name="jam_operasional" 
                       class="form-control @error('jam_operasional') is-invalid @enderror" 
                       value="{{ old('jam_operasional', $pengaturan->jam_operasional) }}" required>
                @error('jam_operasional')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-2">
                <label for="maps_embed_url" class="form-label">URL Embed Google Maps</label>
                <textarea id="maps_embed_url" name="maps_embed_url" rows="2" 
                          class="form-control @error('maps_embed_url') is-invalid @enderror" 
                          placeholder="https://www.google.com/maps/embed?pb=...">{{ old('maps_embed_url', $pengaturan->maps_embed_url) }}</textarea>
                @error('maps_embed_url')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
        </flux:card>
    </div>

    <div class="mt-6 flex justify-end">
        <flux:button type="submit" variant="primary" class="px-8">
            Simpan Perubahan Pengaturan
        </flux:button>
    </div>
</form>
@endsection
