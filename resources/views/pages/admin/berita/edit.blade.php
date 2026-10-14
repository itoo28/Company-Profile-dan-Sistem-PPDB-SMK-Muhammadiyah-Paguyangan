@extends('layouts.admin')

@section('title', 'Ubah Warta Berita — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ubah Warta & Berita')
@section('header-subtitle', 'Sunting isi artikel, status publikasi, atau ganti foto sampul berita')

@section('content')
<div class="max-w-4xl mx-auto">
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 m-0">Sunting Warta: {{ $berita->judul }}</h3>
        </div>
        <form action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="judul" class="form-label">Judul Berita <span class="text-rose-500">*</span></label>
                <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $berita->judul) }}" required>
                @error('judul')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="tanggal_terbit" class="form-label">Tanggal Terbit <span class="text-rose-500">*</span></label>
                    <input type="date" id="tanggal_terbit" name="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror" value="{{ old('tanggal_terbit', $berita->tanggal_terbit ? $berita->tanggal_terbit->format('Y-m-d') : '') }}" required>
                    @error('tanggal_terbit')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="status" class="form-label">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="terbit" {{ old('status', $berita->status) === 'terbit' ? 'selected' : '' }}>Terbitkan Langsung (Publik)</option>
                        <option value="draf" {{ old('status', $berita->status) === 'draf' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                    </select>
                    @error('status')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="ringkasan" class="form-label">Ringkasan Singkat (Lead Paragraph)</label>
                <textarea id="ringkasan" name="ringkasan" rows="2" class="form-control @error('ringkasan') is-invalid @enderror">{{ old('ringkasan', $berita->ringkasan) }}</textarea>
                @error('ringkasan')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gambar_sampul" class="form-label">Ganti Foto Sampul (Kosongkan jika tidak diubah)</label>
                @if($berita->gambar_sampul)
                    <div class="mb-2 flex items-center gap-3 p-2 bg-slate-50 border border-slate-200 rounded-lg">
                        <img src="{{ $berita->gambar_url }}" alt="Sampul Saat Ini" onerror="this.style.display='none'" class="h-16 w-24 rounded object-cover border border-slate-200">
                        <span class="text-xs text-slate-500">Foto sampul aktif saat ini</span>
                    </div>
                @endif
                <input type="file" id="gambar_sampul" name="gambar_sampul" class="form-control @error('gambar_sampul') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                @error('gambar_sampul')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label for="isi" class="form-label">Isi Lengkap Artikel <span class="text-rose-500">*</span></label>
                <textarea id="isi" name="isi" rows="12" class="form-control @error('isi') is-invalid @enderror" required>{{ old('isi', $berita->isi) }}</textarea>
                <small class="form-help">
                    Mendukung tag HTML sederhana (&lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;blockquote&gt;).
                </small>
                @error('isi')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                <flux:button href="{{ route('admin.berita.index') }}" variant="ghost">Batal</flux:button>
                <flux:button type="submit" variant="primary" class="px-6">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection
