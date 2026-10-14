@extends('layouts.admin')

@section('title', 'Pendaftaran Calon Siswa Offline — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Pendaftaran Calon Siswa Baru (Loket Offline)')
@section('header-subtitle', 'Formulir pendaftaran bagi calon siswa yang datang langsung ke kampus dan dilayani oleh Petugas Piket PPDB (FR-11)')
@section('header-action')
    <flux:button href="{{ route('admin.pendaftar.index') }}" variant="ghost" size="sm" icon="arrow-left">
        Kembali ke Daftar
    </flux:button>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    @if($errors->any())
        <div class="admin-alert admin-alert-danger mb-6">
            <div>
                <strong class="block mb-1">Mohon periksa kesalahan input berikut:</strong>
                <ul class="pl-5 m-0 text-xs">
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
        <flux:card class="p-6 mb-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 m-0">1. Gelombang, Jalur & Pilihan Jurusan</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="gelombang_id" class="form-label">Gelombang Pendaftaran <span class="text-rose-500">*</span></label>
                    <select name="gelombang_id" id="gelombang_id" class="form-select" required>
                        @foreach($gelombangs as $g)
                            <option value="{{ $g->id }}" {{ old('gelombang_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_gelombang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="jalur_pendaftaran" class="form-label">Jalur Pendaftaran <span class="text-rose-500">*</span></label>
                    <select name="jalur_pendaftaran" id="jalur_pendaftaran" class="form-select" required>
                        <option value="umum" {{ old('jalur_pendaftaran') === 'umum' ? 'selected' : '' }}>Jalur Umum / Reguler</option>
                        <option value="prestasi" {{ old('jalur_pendaftaran') === 'prestasi' ? 'selected' : '' }}>Jalur Prestasi</option>
                    </select>
                </div>

                <div>
                    <label for="jurusan_id" class="form-label">Konsentrasi Keahlian <span class="text-rose-500">*</span></label>
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
        </flux:card>

        {{-- Section 2: Data Diri Calon Siswa --}}
        <flux:card class="p-6 mb-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 m-0">2. Data Diri Calon Siswa</h2>
            </div>
            
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" placeholder="Sesuai Ijazah / Akta Kelahiran" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="nik" class="form-label">NIK (16 Digit) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" maxlength="16" placeholder="Contoh: 332901..." required>
                </div>

                <div>
                    <label for="nisn" class="form-label">NISN (10 Digit) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nisn" id="nisn" class="form-control" value="{{ old('nisn') }}" maxlength="10" placeholder="Contoh: 0081234567" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-rose-500">*</span></label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                </div>

                <div>
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="asal_sekolah" class="form-label">Asal Sekolah (SMP / MTs) <span class="text-rose-500">*</span></label>
                    <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control" value="{{ old('asal_sekolah') }}" placeholder="Nama SMP / MTs asal" required>
                </div>
            </div>

            <div class="mb-0">
                <label for="alamat" class="form-label">Alamat Lengkap Tempat Tinggal <span class="text-rose-500">*</span></label>
                <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Nama Dusun/Desa, RT/RW, Kecamatan, Kabupaten..." required>{{ old('alamat') }}</textarea>
            </div>
        </flux:card>

        {{-- Section 3: Data Orang Tua / Wali --}}
        <flux:card class="p-6 mb-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 m-0">3. Data Orang Tua / Wali</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama_orang_tua" class="form-label">Nama Orang Tua / Wali <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_orang_tua" id="nama_orang_tua" class="form-control" value="{{ old('nama_orang_tua') }}" required>
                </div>

                <div>
                    <label for="no_hp_orang_tua" class="form-label">Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_hp_orang_tua" id="no_hp_orang_tua" class="form-control" value="{{ old('no_hp_orang_tua') }}" placeholder="Contoh: 0812-3456-7890" required>
                </div>
            </div>
        </flux:card>

        <div class="flex justify-end items-center gap-3">
            <flux:button href="{{ route('admin.pendaftar.index') }}" variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary" id="btnSubmitOffline" class="px-6">
                Simpan Pendaftaran Offline & Terbitkan Bukti
            </flux:button>
        </div>
    </form>
</div>
@endsection
