@extends('layouts.app')

@section('title', 'Informasi Alur & Persyaratan PPDB')
@section('meta_description', 'Panduan alur pendaftaran, persyaratan berkas dokumen, dan jadwal PPDB SMK Muhammadiyah Paguyangan Brebes.')

@section('content')
<section style="background-color: var(--primary-deep); color: #ffffff; padding: 3rem 0;">
    <div class="container">
        <span style="color: #fbbf24; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">Penerimaan Siswa Baru</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem; color: #ffffff;">Alur & Persyaratan PPDB</h1>
        <p style="color: #cbd5e1; margin-top: 0.5rem; max-width: 650px;">
            Tahun Ajaran {{ $tahunAjaran->nama }}. Pahami alur pendaftaran mandiri, kelengkapan berkas fisik maupun digital, serta jadwal pelayanan panitia.
        </p>
    </div>
</section>

<section class="py-section bg-white">
    <div class="container">
        {{-- Alur 4 Langkah --}}
        <div class="section-header text-center">
            <span class="section-eyebrow">Tahapan Proses</span>
            <h2 class="section-title">Alur Pendaftaran Peserta Didik Baru</h2>
            <p class="section-subtitle">
                Proses pendaftaran dirancang transparan dan memudahkan calon siswa maupun orang tua.
            </p>
        </div>

        <div class="flow-grid" style="margin-bottom: 4rem;">
            <div class="flow-step-card">
                <div class="flow-step-num">1</div>
                <h3 class="flow-step-title">Pendaftaran Mandiri</h3>
                <p class="flow-step-desc">
                    Isi formulir identitas calon siswa melalui tombol pendaftaran online atau kunjungi loket piket PPDB di kampus sekolah.
                </p>
            </div>

            <div class="flow-step-card">
                <div class="flow-step-num">2</div>
                <h3 class="flow-step-title">Unggah Berkas</h3>
                <p class="flow-step-desc">
                    Masuk ke portal siswa menggunakan nomor pendaftaran dan tanggal lahir. Unggah dokumen berkas persyaratan (PDF/JPG, maks 2 MB).
                </p>
            </div>

            <div class="flow-step-card">
                <div class="flow-step-num">3</div>
                <h3 class="flow-step-title">Verifikasi Panitia</h3>
                <p class="flow-step-desc">
                    Panitia memverifikasi kesesuaian dokumen secara daring. Jika berkas ditolak, siswa akan menerima catatan perbaikan di dashboard.
                </p>
            </div>

            <div class="flow-step-card">
                <div class="flow-step-num">4</div>
                <h3 class="flow-step-title">Ambil Seragam</h3>
                <p class="flow-step-desc">
                    Setelah seluruh berkas wajib disetujui, bawa bukti pendaftaran ke loket Tata Usaha sekolah untuk pengukuran dan pengambilan seragam.
                </p>
            </div>
        </div>

        {{-- Tabel Persyaratan Berkas (Terhubung Dinamis ke Master Jenis Berkas - FR-19) --}}
        <div style="margin-bottom: 4rem;">
            <div class="section-header">
                <span class="section-eyebrow">Dokumen yang Diperlukan</span>
                <h2 class="section-title">Daftar Berkas Persyaratan</h2>
                <p class="section-subtitle">
                    Berkas diunggah dalam format digital (PDF, JPG, JPEG, atau PNG) dengan ukuran maksimal {{ $pengaturan->max_file_size_mb }} MB per file.
                </p>
            </div>

            <div class="table-responsive">
                <table class="dense-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Jenis Berkas</th>
                            <th style="width: 140px;">Sifat Berkas</th>
                            <th>Keterangan / Panduan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisBerkas as $index => $berkas)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $berkas->nama }}</strong></td>
                                <td>
                                    @if($berkas->is_wajib)
                                        <span class="badge" style="background-color: #fee2e2; color: #b91c1c; border-color: #fecaca;">
                                            Wajib
                                        </span>
                                    @else
                                        <span class="badge" style="background-color: #f1f5f9; color: #475569; border-color: #e2e8f0;">
                                            Opsional
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $berkas->deskripsi ?? 'Disiapkan salinan scan atau foto jelas.' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted);">Daftar berkas belum dikonfigurasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Jadwal & Batas Waktu --}}
        <div class="card-grid-2" style="margin-bottom: 3rem;">
            <div style="background-color: var(--canvas-bg); padding: 2rem; border-radius: var(--radius-card); border: 1px solid var(--border-default);">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 1rem;">
                    Jadwal Gelombang Pendaftaran
                </h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.875rem; font-size: 0.9375rem;">
                    <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-default); padding-bottom: 0.5rem;">
                        <span>Gelombang Khusus / Prestasi</span>
                        <strong>Januari - Maret {{ date('Y') }}</strong>
                    </li>
                    <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-default); padding-bottom: 0.5rem;">
                        <span>Gelombang 1 Reguler</span>
                        <strong>April - Mei {{ date('Y') }}</strong>
                    </li>
                    <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-default); padding-bottom: 0.5rem;">
                        <span>Gelombang 2 (Sisa Kuota)</span>
                        <strong>Juni - Juli {{ date('Y') }}</strong>
                    </li>
                </ul>
            </div>

            <div style="background-color: var(--primary-surface); padding: 2rem; border-radius: var(--radius-card); border: 1px solid #bfdbfe; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 0.5rem;">
                        Batas Waktu Upload Berkas
                    </h3>
                    <p style="font-size: 0.9375rem; color: var(--text-body); margin-bottom: 1rem;">
                        Pastikan seluruh berkas wajib diunggah sebelum tenggat waktu berakhir. Akun yang melewati batas waktu tanpa berkas lengkap akan terkunci otomatis (lockdown).
                    </p>
                    <div style="font-size: 1.125rem; font-weight: 800; color: var(--primary-base);">
                        {{ $pengaturan->batas_waktu_berkas ? $pengaturan->batas_waktu_berkas->translatedFormat('d F Y, H:i') . ' WIB' : 'Ditentukan panitia' }}
                    </div>
                </div>

                <div style="margin-top: 1.5rem;">
                    <a href="{{ route('ppdb.daftar') }}" class="btn btn-primary" style="width: 100%;">
                        Daftar Siswa Baru Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
