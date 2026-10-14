@extends('layouts.admin')

@section('title', 'Pendaftaran Calon Siswa Offline — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Pendaftaran Calon Siswa Baru (Loket Offline)')
@section('header-subtitle', 'Formulir pendaftaran bagi calon siswa yang datang langsung ke kampus dan dilayani oleh Petugas Piket PPDB (FR-11)')
@section('header-action')
    <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
        &larr; <span>Kembali ke Daftar</span>
    </a>
@endsection

@section('content')
<div style="max-width: 960px; margin: 0 auto;">
    @if($errors->any())
        <div class="admin-alert admin-alert-danger" style="margin-bottom: 1.5rem;">
            <div>
                <strong style="display: block; margin-bottom: 0.35rem;">Mohon periksa kesalahan input berikut:</strong>
                <ul style="padding-left: 1.25rem; margin: 0; font-size: 0.85rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.pendaftar.baru.simpan') }}" method="POST">
        @csrf

        {{-- Section 1: Jalur & Pilihan Jurusan --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">1. Gelombang, Jalur & Pilihan Jurusan</h2>
            </div>
            <div class="admin-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label for="gelombang_id" class="form-label">Gelombang Pendaftaran <span style="color: #ef4444;">*</span></label>
                        <select name="gelombang_id" id="gelombang_id" class="form-select" required>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}" {{ old('gelombang_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama_gelombang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jalur_pendaftaran" class="form-label">Jalur Pendaftaran <span style="color: #ef4444;">*</span></label>
                        <select name="jalur_pendaftaran" id="jalur_pendaftaran" class="form-select" required>
                            <option value="umum" {{ old('jalur_pendaftaran') === 'umum' ? 'selected' : '' }}>Jalur Umum / Reguler</option>
                            <option value="prestasi" {{ old('jalur_pendaftaran') === 'prestasi' ? 'selected' : '' }}>Jalur Prestasi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jurusan_id" class="form-label">Konsentrasi Keahlian <span style="color: #ef4444;">*</span></label>
                        <select name="jurusan_id" id="jurusan_id" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }} ({{ $j->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Data Diri Calon Siswa --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">2. Data Diri Calon Siswa</h2>
            </div>
            <div class="admin-card-body">
                <div class="form-group mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap Siswa <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" placeholder="Sesuai Ijazah / Akta Kelahiran" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="nik" class="form-label">NIK (16 Digit) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" maxlength="16" placeholder="Contoh: 332901..." required>
                    </div>

                    <div>
                        <label for="nisn" class="form-label">NISN (10 Digit) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nisn" id="nisn" class="form-control" value="{{ old('nisn') }}" maxlength="10" placeholder="Contoh: 0081234567" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="asal_sekolah" class="form-label">Asal Sekolah (SMP / MTs) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control" value="{{ old('asal_sekolah') }}" placeholder="Nama SMP / MTs asal" required>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label for="alamat" class="form-label">Alamat Lengkap Tempat Tinggal <span style="color: #ef4444;">*</span></label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Nama Dusun/Desa, RT/RW, Kecamatan, Kabupaten..." required>{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 3: Data Orang Tua / Wali --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">3. Data Orang Tua / Wali</h2>
            </div>
            <div class="admin-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="nama_orang_tua" class="form-label">Nama Orang Tua / Wali <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nama_orang_tua" id="nama_orang_tua" class="form-control" value="{{ old('nama_orang_tua') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="no_hp_orang_tua" class="form-label">Nomor WhatsApp / HP Aktif <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="no_hp_orang_tua" id="no_hp_orang_tua" class="form-control" value="{{ old('no_hp_orang_tua') }}" placeholder="Contoh: 0812-3456-7890" required>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 1.5rem;">
            <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" id="btnSubmitOffline" style="padding: 10px 24px;">
                Simpan Pendaftaran Offline & Terbitkan Bukti
            </button>
        </div>
    </form>
</div>
@endsection
