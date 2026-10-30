@extends('layouts.app')

@section('title', 'Formulir Pendaftaran Siswa Baru (PPDB)')
@section('meta_description', 'Formulir pendaftaran daring peserta didik baru SMK Muhammadiyah Paguyangan Tahun Ajaran ' . $tahunAjaran->nama)

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 2.75rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">PPDB Online TA {{ $tahunAjaran->nama }}</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Formulir Pendaftaran Siswa Baru</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 680px;">
            Lengkapi data diri calon peserta didik secara seksama. Setelah formulir dikirim, sistem akan menerbitkan Nomor Pendaftaran resmi untuk mengakses Portal Calon Siswa.
        </p>
    </div>
</section>

<section class="py-section bg-canvas">
    <div class="container container-narrow">
        {{-- Banner Info Gelombang & Benefit Promo 100 Pendaftar Pertama (FR-43 & FR-44) --}}
        <div class="ppdb-banner-grid">
            <div style="background-color: var(--primary-surface); border: 1px solid #bfdbfe; border-radius: var(--radius-card); padding: 1.25rem; box-sizing: border-box; overflow-wrap: break-word; word-break: break-word;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary-base); text-transform: uppercase;">Gelombang Aktif</div>
                <div style="font-size: 1.125rem; font-weight: 800; color: var(--primary-deep); margin-top: 0.25rem;">
                    {{ $gelombangAktif->nama_gelombang ?? 'Gelombang PPDB 2026' }}
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">
                    Periode: {{ $gelombangAktif ? $gelombangAktif->tanggal_mulai->translatedFormat('d F Y') . ' s.d. ' . $gelombangAktif->tanggal_selesai->translatedFormat('d F Y') : 'Dibuka setiap hari kerja' }}
                </div>
            </div>

            <div style="background-color: var(--accent-surface); border: 1px solid var(--status-warning-border); border-radius: var(--radius-card); padding: 1.25rem; box-sizing: border-box; overflow-wrap: break-word; word-break: break-word;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--accent-text); text-transform: uppercase;">Benefit 100 Pendaftar Pertama</div>
                <div style="font-size: 1.125rem; font-weight: 800; color: var(--text-heading); margin-top: 0.25rem;">
                    Gratis SPP 3 Bulan
                </div>
                <div style="font-size: 0.8125rem; color: var(--accent-text); margin-top: 0.25rem; font-weight: 600;">
                    Tersedia untuk 100 siswa pertama yang menyelesaikan registrasi hingga pengambilan seragam. (Tersisa: {{ $sisaKuotaPromo }} kuota)
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="smk-alert smk-alert-danger" style="margin-bottom: 1.5rem;">
                <svg class="smk-alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="smk-alert-content">
                    <strong style="display: block; margin-bottom: 0.35rem;">Mohon periksa kembali isian formulir Anda:</strong>
                    <ul style="padding-left: 1.25rem; margin: 0; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Form Pendaftaran --}}
        <form action="{{ route('ppdb.daftar.simpan') }}" method="POST" class="ppdb-form-card">
            @csrf

            {{-- Bagian 1: Jalur & Pilihan Jurusan --}}
            <div style="border-bottom: 1px solid var(--border-default); padding-bottom: 1.5rem; margin-bottom: 1.75rem;">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 0.35rem;">
                    1. Jalur & Pilihan Konsentrasi Keahlian
                </h2>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    Pilih jalur pendaftaran dan 1 kompetensi kejuruan vokasi yang diminati.
                </p>

                <div class="card-grid-2" style="margin-bottom: 1.25rem;">
                    <div class="form-group">
                        <label for="jalur_pendaftaran" class="form-label">
                            Kategori / Jalur Pendaftaran <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="jalur_pendaftaran" id="jalur_pendaftaran" class="form-control" required>
                            <option value="umum" {{ old('jalur_pendaftaran') === 'umum' ? 'selected' : '' }}>
                                Jalur Umum (Reguler SMP/MTs)
                            </option>
                            <option value="prestasi" {{ old('jalur_pendaftaran') === 'prestasi' ? 'selected' : '' }}>
                                Jalur Prestasi (Wajib Lampirkan Sertifikat Juara)
                            </option>
                        </select>
                        <span class="form-help">Jalur Prestasi diperuntukkan bagi peraih kejuaraan sains, olahraga, atau seni minimal tingkat kabupaten.</span>
                    </div>

                    <div class="form-group">
                        <label for="jurusan_id" class="form-label">
                            Konsentrasi Keahlian (Jurusan) <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="jurusan_id" id="jurusan_id" class="form-control" required>
                            <option value="">-- Pilih Konsentrasi Keahlian --</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }} ({{ $jurusan->kode }})
                                </option>
                            @endforeach
                        </select>
                        <span class="form-help">Pilih jurusan kejuruan utama yang ingin ditekuni.</span>
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Data Diri Calon Siswa --}}
            <div style="border-bottom: 1px solid var(--border-default); padding-bottom: 1.5rem; margin-bottom: 1.75rem;">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 0.35rem;">
                    2. Data Diri Calon Siswa
                </h2>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    Pastikan NIK dan NISN terisi tepat sesuai dokumen resmi kependudukan dan sekolah.
                </p>

                <div class="form-group">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap (sesuai Ijazah/Akta) <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Muhammad Rizki Pratama" required>
                </div>

                <div class="card-grid-2">
                    <div class="form-group">
                        <label for="nik" class="form-label">Nomor Induk Kependudukan (NIK 16 Digit) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" maxlength="16" placeholder="Contoh: 3329012308080001" required>
                        <span class="form-help">Tercantum pada Kartu Keluarga (KK). 16 digit angka.</span>
                    </div>

                    <div class="form-group">
                        <label for="nisn" class="form-label">Nomor Induk Siswa Nasional (NISN 10 Digit) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nisn" id="nisn" class="form-control" value="{{ old('nisn') }}" maxlength="10" placeholder="Contoh: 0081234567" required>
                        <span class="form-help">10 digit angka resmi dari SMP/MTs asal.</span>
                    </div>
                </div>

                <div class="card-grid-2">
                    <div class="form-group">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Brebes" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                        <span class="form-help">Tanggal lahir akan menjadi password masuk awal portal siswa.</span>
                    </div>
                </div>

                <div class="card-grid-2">
                    <div class="form-group">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asal_sekolah" class="form-label">Asal Sekolah (SMP / MTs) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control" value="{{ old('asal_sekolah') }}" placeholder="Contoh: SMP Muhammadiyah 1 Paguyangan" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat" class="form-label">Alamat Tempat Tinggal Lengkap <span style="color: #ef4444;">*</span></label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Nama Jalan, RT/RW, Dusun, Desa/Kelurahan, Kecamatan, Kabupaten" required>{{ old('alamat') }}</textarea>
                </div>
            </div>

            {{-- Bagian 3: Data Orang Tua / Wali --}}
            <div style="border-bottom: 1px solid var(--border-default); padding-bottom: 1.5rem; margin-bottom: 1.75rem;">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--primary-deep); margin-bottom: 0.35rem;">
                    3. Data Orang Tua / Wali
                </h2>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    Kontak aktif untuk pemberitahuan status verifikasi berkas dan layanan seragam.
                </p>

                <div class="card-grid-2">
                    <div class="form-group">
                        <label for="nama_orang_tua" class="form-label">Nama Orang Tua / Wali <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nama_orang_tua" id="nama_orang_tua" class="form-control" value="{{ old('nama_orang_tua') }}" placeholder="Nama Ayah / Ibu / Wali" required>
                    </div>

                    <div class="form-group">
                        <label for="no_hp_orang_tua" class="form-label">Nomor WhatsApp / HP Orang Tua <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="no_hp_orang_tua" id="no_hp_orang_tua" class="form-control" value="{{ old('no_hp_orang_tua') }}" placeholder="Contoh: 0812-3456-7890" required>
                        <span class="form-help">Pastikan nomor aktif terhubung ke WhatsApp.</span>
                    </div>
                </div>
            </div>

            {{-- Kotak Pernyataan dan Submit --}}
            <div style="background-color: var(--canvas-bg); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-default); margin-bottom: 1.75rem; font-size: 0.875rem; color: var(--text-body); box-sizing: border-box; overflow-wrap: break-word; word-break: break-word;">
                <label style="display: flex; align-items: flex-start; gap: 0.625rem; cursor: pointer;">
                    <input type="checkbox" required style="margin-top: 0.25rem; flex-shrink: 0;">
                    <span style="min-width: 0; line-height: 1.5;">Saya menyatakan dengan sesungguhnya bahwa seluruh data yang diisikan dalam formulir pendaftaran ini adalah benar, sah, dan dapat dipertanggungjawabkan sesuai dokumen aslinya.</span>
                </label>
            </div>

            <div class="form-action-group">
                <a href="{{ route('ppdb.info') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary btn-lg" id="btnSubmitPendaftaran">
                    Kirim Formulir Pendaftaran
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
