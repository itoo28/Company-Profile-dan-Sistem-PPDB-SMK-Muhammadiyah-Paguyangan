@extends('layouts.admin')

@section('title', 'Tulis Warta Baru — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Tulis Warta & Berita Baru')
@section('header-subtitle', 'Buat artikel publikasi baru untuk portal informasi sekolah')

@section('content')
<div class="max-w-4xl mx-auto">
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 m-0">Formulir Artikel Berita</h3>
        </div>
        <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="judul" class="form-label">Judul Berita <span class="text-rose-500">*</span></label>
                <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" placeholder="Contoh: Prestasi Juara 1 LKS Tingkat Provinsi Tahun 2026" required>
                @error('judul')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="tanggal_terbit" class="form-label">Tanggal Terbit <span class="text-rose-500">*</span></label>
                    <input type="date" id="tanggal_terbit" name="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror" value="{{ old('tanggal_terbit', date('Y-m-d')) }}" required>
                    @error('tanggal_terbit')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="status" class="form-label">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="terbit" {{ old('status', 'terbit') === 'terbit' ? 'selected' : '' }}>Terbitkan Langsung (Publik)</option>
                        <option value="draf" {{ old('status') === 'draf' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                    </select>
                    @error('status')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="ringkasan" class="form-label">Ringkasan Singkat (Lead Paragraph)</label>
                <textarea id="ringkasan" name="ringkasan" rows="2" class="form-control @error('ringkasan') is-invalid @enderror" placeholder="Deskripsi singkat yang tampil pada cuplikan daftar warta...">{{ old('ringkasan') }}</textarea>
                @error('ringkasan')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gambar_sampul" class="form-label">Foto Sampul Utama (Maks. 2 MB - JPG/PNG)</label>
                <input type="file" id="gambar_sampul" name="gambar_sampul" class="form-control @error('gambar_sampul') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                @error('gambar_sampul')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label for="isi" class="form-label">Isi Lengkap Artikel <span class="text-rose-500">*</span></label>
                <textarea id="isi" name="isi" rows="12" class="form-control @error('isi') is-invalid @enderror" placeholder="Tuliskan isi warta atau berita selengkapnya di sini..." required>{{ old('isi') }}</textarea>
                <small class="form-help">
                    Mendukung tag HTML sederhana (&lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;blockquote&gt;).
                </small>
                @error('isi')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                <flux:button href="{{ route('admin.berita.index') }}" variant="ghost">Batal</flux:button>
                <flux:button type="submit" variant="primary" class="px-6">Simpan Berita</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection
