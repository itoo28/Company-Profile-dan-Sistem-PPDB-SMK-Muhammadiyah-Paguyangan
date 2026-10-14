@extends('layouts.admin')

@section('title', 'Ubah Prestasi — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ubah Capaian Prestasi')
@section('header-subtitle', 'Sunting arsip dokumentasi kejuaraan dan capaian penghargaan siswa atau sekolah')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Sunting Prestasi: {{ $prestasi->nama_prestasi }}</h3>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.prestasi.update', $prestasi->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label for="nama_prestasi" class="form-label">Nama Kejuaraan / Prestasi <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="nama_prestasi" name="nama_prestasi" class="form-control @error('nama_prestasi') is-invalid @enderror" value="{{ old('nama_prestasi', $prestasi->nama_prestasi) }}" required>
                    @error('nama_prestasi')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="nama_peraih" class="form-label">Nama Peraih / Tim <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="nama_peraih" name="nama_peraih" class="form-control @error('nama_peraih') is-invalid @enderror" value="{{ old('nama_peraih', $prestasi->nama_peraih) }}" required>
                    @error('nama_peraih')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="peringkat" class="form-label">Peringkat / Gelar Juara <span style="color: #dc2626;">*</span></label>
                        <input type="text" id="peringkat" name="peringkat" class="form-control @error('peringkat') is-invalid @enderror" value="{{ old('peringkat', $prestasi->peringkat) }}" required>
                        @error('peringkat')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="tingkat" class="form-label">Tingkat Penyelenggaraan <span style="color: #dc2626;">*</span></label>
                        <select name="tingkat" id="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                            <option value="Kabupaten" {{ old('tingkat', $prestasi->tingkat) === 'Kabupaten' ? 'selected' : '' }}>Kabupaten / Kota</option>
                            <option value="Karesidenan" {{ old('tingkat', $prestasi->tingkat) === 'Karesidenan' ? 'selected' : '' }}>Karesidenan</option>
                            <option value="Provinsi" {{ old('tingkat', $prestasi->tingkat) === 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                            <option value="Nasional" {{ old('tingkat', $prestasi->tingkat) === 'Nasional' ? 'selected' : '' }}>Nasional</option>
                            <option value="Internasional" {{ old('tingkat', $prestasi->tingkat) === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                        </select>
                        @error('tingkat')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan <span style="color: #dc2626;">*</span></label>
                        <input type="date" id="tanggal_perolehan" name="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan', $prestasi->tanggal_perolehan ? $prestasi->tanggal_perolehan->format('Y-m-d') : '') }}" required>
                        @error('tanggal_perolehan')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="status" class="form-label">Status Publikasi <span style="color: #dc2626;">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="terbit" {{ old('status', $prestasi->status) === 'terbit' ? 'selected' : '' }}>Terbitkan Langsung (Publik)</option>
                            <option value="draf" {{ old('status', $prestasi->status) === 'draf' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="foto" class="form-label">Ganti Foto Dokumentasi (Kosongkan jika tidak diubah)</label>
                    @if($prestasi->foto)
                        <div style="margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
                            <img src="{{ $prestasi->foto_url }}" alt="Foto Saat Ini" onerror="this.style.display='none'" style="max-height: 80px; border-radius: 6px; border: 1px solid #e2e8f0; object-fit: cover;">
                            <span style="font-size: 0.8rem; color: #64748b;">Foto dokumentasi saat ini</span>
                        </div>
                    @endif
                    <input type="file" id="foto" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                    @error('foto')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="deskripsi" class="form-label">Deskripsi & Keterangan Prestasi <span style="color: #dc2626;">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $prestasi->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                    <a href="{{ route('admin.prestasi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
