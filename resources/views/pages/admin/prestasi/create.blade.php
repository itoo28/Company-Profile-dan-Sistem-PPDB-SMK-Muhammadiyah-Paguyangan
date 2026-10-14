@extends('layouts.admin')

@section('title', 'Tambah Prestasi Baru — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Tambah Capaian Prestasi Baru')
@section('header-subtitle', 'Dokumentasikan penghargaan atau kejuaraan yang diraih siswa maupun sekolah')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Formulir Catatan Prestasi</h3>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.prestasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <label for="nama_prestasi" class="form-label">Nama Kejuaraan / Prestasi <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="nama_prestasi" name="nama_prestasi" class="form-control @error('nama_prestasi') is-invalid @enderror" value="{{ old('nama_prestasi') }}" placeholder="Contoh: Lomba Kompetensi Siswa (LKS) Bidang IT Network Systems" required>
                    @error('nama_prestasi')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="nama_peraih" class="form-label">Nama Peraih / Tim <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="nama_peraih" name="nama_peraih" class="form-control @error('nama_peraih') is-invalid @enderror" value="{{ old('nama_peraih') }}" placeholder="Contoh: Ahmad Fauzi (Siswa Kelas XII RPL)" required>
                    @error('nama_peraih')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="peringkat" class="form-label">Peringkat / Gelar Juara <span style="color: #dc2626;">*</span></label>
                        <input type="text" id="peringkat" name="peringkat" class="form-control @error('peringkat') is-invalid @enderror" value="{{ old('peringkat') }}" placeholder="Contoh: Juara 1 / Medali Emas" required>
                        @error('peringkat')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="tingkat" class="form-label">Tingkat Penyelenggaraan <span style="color: #dc2626;">*</span></label>
                        <select name="tingkat" id="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                            <option value="Kabupaten" {{ old('tingkat') === 'Kabupaten' ? 'selected' : '' }}>Kabupaten / Kota</option>
                            <option value="Karesidenan" {{ old('tingkat') === 'Karesidenan' ? 'selected' : '' }}>Karesidenan</option>
                            <option value="Provinsi" {{ old('tingkat', 'Provinsi') === 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                            <option value="Nasional" {{ old('tingkat') === 'Nasional' ? 'selected' : '' }}>Nasional</option>
                            <option value="Internasional" {{ old('tingkat') === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                        </select>
                        @error('tingkat')
                            <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan <span style="color: #dc2626;">*</span></label>
                        <input type="date" id="tanggal_perolehan" name="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan', date('Y-m-d')) }}" required>
                        @error('tanggal_perolehan')
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
                    <label for="foto" class="form-label">Foto Dokumentasi / Sertifikat (Maks. 2 MB - JPG/PNG)</label>
                    <input type="file" id="foto" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                    @error('foto')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="deskripsi" class="form-label">Deskripsi & Keterangan Prestasi <span style="color: #dc2626;">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Ceritakan detail kejuaraan, penyelenggara, dan capaian membanggakan ini..." required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                    <a href="{{ route('admin.prestasi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Simpan Prestasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
