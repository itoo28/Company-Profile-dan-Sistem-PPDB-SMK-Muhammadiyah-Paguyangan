@extends('layouts.admin')

@section('title', 'Ubah Prestasi — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ubah Capaian Prestasi')
@section('header-subtitle', 'Sunting arsip dokumentasi kejuaraan dan capaian penghargaan siswa atau sekolah')

@section('content')
<div class="max-w-3xl mx-auto">
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 m-0">Sunting Prestasi: {{ $prestasi->nama_prestasi }}</h3>
        </div>
        <form action="{{ route('admin.prestasi.update', $prestasi->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_prestasi" class="form-label">Nama Kejuaraan / Prestasi <span class="text-rose-500">*</span></label>
                <input type="text" id="nama_prestasi" name="nama_prestasi" class="form-control @error('nama_prestasi') is-invalid @enderror" value="{{ old('nama_prestasi', $prestasi->nama_prestasi) }}" required>
                @error('nama_prestasi')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama_peraih" class="form-label">Nama Peraih / Tim <span class="text-rose-500">*</span></label>
                <input type="text" id="nama_peraih" name="nama_peraih" class="form-control @error('nama_peraih') is-invalid @enderror" value="{{ old('nama_peraih', $prestasi->nama_peraih) }}" required>
                @error('nama_peraih')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="peringkat" class="form-label">Peringkat / Gelar Juara <span class="text-rose-500">*</span></label>
                    <input type="text" id="peringkat" name="peringkat" class="form-control @error('peringkat') is-invalid @enderror" value="{{ old('peringkat', $prestasi->peringkat) }}" required>
                    @error('peringkat')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="tingkat" class="form-label">Tingkat Penyelenggaraan <span class="text-rose-500">*</span></label>
                    <select name="tingkat" id="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                        <option value="Kabupaten" {{ old('tingkat', $prestasi->tingkat) === 'Kabupaten' ? 'selected' : '' }}>Kabupaten / Kota</option>
                        <option value="Karesidenan" {{ old('tingkat', $prestasi->tingkat) === 'Karesidenan' ? 'selected' : '' }}>Karesidenan</option>
                        <option value="Provinsi" {{ old('tingkat', $prestasi->tingkat) === 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                        <option value="Nasional" {{ old('tingkat', $prestasi->tingkat) === 'Nasional' ? 'selected' : '' }}>Nasional</option>
                        <option value="Internasional" {{ old('tingkat', $prestasi->tingkat) === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                    </select>
                    @error('tingkat')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan <span class="text-rose-500">*</span></label>
                    <input type="date" id="tanggal_perolehan" name="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan', $prestasi->tanggal_perolehan ? $prestasi->tanggal_perolehan->format('Y-m-d') : '') }}" required>
                    @error('tanggal_perolehan')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="status" class="form-label">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="terbit" {{ old('status', $prestasi->status) === 'terbit' ? 'selected' : '' }}>Terbitkan Langsung (Publik)</option>
                        <option value="draf" {{ old('status', $prestasi->status) === 'draf' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                    </select>
                    @error('status')
                        <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Ganti Foto Dokumentasi (Kosongkan jika tidak diubah)</label>
                @if($prestasi->foto)
                    <div class="mb-2 flex items-center gap-3 p-2 bg-slate-50 border border-slate-200 rounded-lg">
                        <img src="{{ $prestasi->foto_url }}" alt="Foto Saat Ini" onerror="this.style.display='none'" class="h-16 w-24 rounded object-cover border border-slate-200">
                        <span class="text-xs text-slate-500">Foto dokumentasi saat ini</span>
                    </div>
                @endif
                <input type="file" id="foto" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                @error('foto')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label for="deskripsi" class="form-label">Deskripsi & Keterangan Prestasi <span class="text-rose-500">*</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $prestasi->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                <flux:button href="{{ route('admin.prestasi.index') }}" variant="ghost">Batal</flux:button>
                <flux:button type="submit" variant="primary" class="px-6">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection
