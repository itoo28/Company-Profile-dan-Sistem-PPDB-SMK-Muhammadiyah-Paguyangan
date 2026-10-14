@extends('layouts.admin')

@section('title', 'Tulis Warta Baru — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Tulis Warta & Berita Baru')
@section('header-subtitle', 'Buat artikel publikasi baru untuk portal informasi sekolah')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Formulir Artikel Berita</h3>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <label for="judul" class="form-label">Judul Berita <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" placeholder="Contoh: Prestasi Juara 1 LKS Tingkat Provinsi Tahun 2026" required>
                    @error('judul')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="tanggal_terbit" class="form-label">Tanggal Terbit <span style="color: #dc2626;">*</span></label>
                        <input type="date" id="tanggal_terbit" name="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror" value="{{ old('tanggal_terbit', date('Y-m-d')) }}" required>
                        @error('tanggal_terbit')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="status" class="form-label">Status Publikasi <span style="color: #dc2626;">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="terbit" {{ old('status', 'terbit') === 'terbit' ? 'selected' : '' }}>Terbitkan Langsung (Publik)</option>
                            <option value="draf" {{ old('status') === 'draf' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="ringkasan" class="form-label">Ringkasan Singkat (Lead Paragraph)</label>
                    <textarea id="ringkasan" name="ringkasan" rows="2" class="form-control @error('ringkasan') is-invalid @enderror" placeholder="Deskripsi singkat yang tampil pada cuplikan daftar warta...">{{ old('ringkasan') }}</textarea>
                    @error('ringkasan')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="gambar_sampul" class="form-label">Foto Sampul Utama (Maks. 2 MB - JPG/PNG)</label>
                    <input type="file" id="gambar_sampul" name="gambar_sampul" class="form-control @error('gambar_sampul') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                    @error('gambar_sampul')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="isi" class="form-label">Isi Lengkap Artikel <span style="color: #dc2626;">*</span></label>
                    <textarea id="isi" name="isi" rows="12" class="form-control @error('isi') is-invalid @enderror" placeholder="Tuliskan isi warta atau berita selengkapnya di sini..." required>{{ old('isi') }}</textarea>
                    <small class="form-help">
                        Mendukung tag HTML sederhana (&lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;blockquote&gt;).
                    </small>
                    @error('isi')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Simpan Berita</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
