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

            {{-- Mode Desktop: Tabel Persyaratan --}}
            <div class="berkas-desktop-view">
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

            {{-- Mode Mobile: Kartu Persyaratan Responsif di Android --}}
            <div class="berkas-mobile-cards">
                @forelse($jenisBerkas as $index => $berkas)
                    <div style="background-color: #ffffff; border: 1px solid var(--border-default); border-radius: var(--radius-card); padding: 1rem 1.125rem; border-left: 4px solid {{ $berkas->is_wajib ? '#ef4444' : '#94a3b8' }};">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.35rem;">
                            <div>
                                <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Persyaratan #{{ $index + 1 }}</span>
                                <h3 style="font-size: 0.9375rem; font-weight: 800; color: var(--primary-deep); margin: 0.15rem 0 0 0;">{{ $berkas->nama }}</h3>
                            </div>
                            <div>
                                @if($berkas->is_wajib)
                                    <span class="badge" style="background-color: #fee2e2; color: #b91c1c; border-color: #fecaca; font-size: 0.7rem; padding: 2px 7px;">
                                        Wajib
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #f1f5f9; color: #475569; border-color: #e2e8f0; font-size: 0.7rem; padding: 2px 7px;">
                                        Opsional
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="font-size: 0.8125rem; color: var(--text-muted); line-height: 1.45; margin-top: 0.4rem;">
                            {{ $berkas->deskripsi ?? 'Disiapkan salinan scan atau foto jelas (PDF/JPG/PNG).' }}
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                        Daftar berkas belum dikonfigurasi.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Banner Promo 100 Pendaftar Pertama (Gratis SPP 3 Bulan - FR-43 & BR-14) --}}
        <div style="background: linear-gradient(135deg, #092347 0%, #0284c7 100%); color: #ffffff; padding: 2rem 2.25rem; border-radius: var(--radius-card); margin-bottom: 3.5rem; box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.25);">
            <div style="display: flex; flex-direction: column; justify-content: space-between; align-items: flex-start; gap: 1.5rem;">
                <div style="max-width: 680px;">
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255, 255, 255, 0.15); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; color: #fde047; margin-bottom: 0.75rem; text-transform: uppercase;">
                        <span>★ Program Promo Khusus PPDB</span>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #ffffff; margin: 0 0 0.5rem 0;">
                        Gratis SPP 3 Bulan untuk 100 Pendaftar Pertama
                    </h3>
                    <p style="font-size: 0.9375rem; color: #e0f2fe; line-height: 1.55; margin: 0;">
                        Dapatkan fasilitas bebas biaya SPP selama 3 bulan pertama. Kuota diberikan kepada 100 calon siswa pertama yang menyelesaikan registrasi, verifikasi berkas disetujui panitia, pembayaran lunas, hingga tahap penerimaan seragam sekolah.
                    </p>
                </div>
                <div style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); padding: 1.25rem 1.75rem; border-radius: var(--radius-card); text-align: center; min-width: 220px; width: 100%; max-width: 280px;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: #bae6fd; font-weight: 700; letter-spacing: 0.5px;">Sisa Kuota Promo</div>
                    <div style="font-size: 2.25rem; font-weight: 900; color: #fde047; margin: 0.25rem 0;">
                        {{ $sisaKuotaPromo ?? 100 }}
                    </div>
                    <div style="font-size: 0.8125rem; color: #f0f9ff;">
                        dari {{ $pengaturan->kuota_promo_benefit }} Kursi Tersedia
                    </div>
                    <a href="{{ route('ppdb.daftar') }}" class="btn btn-warning" style="margin-top: 1rem; width: 100%; font-size: 0.8125rem; font-weight: 700;">
                        Klaim Kuota Sekarang &rarr;
                    </a>
                </div>
            </div>
        </div>

        {{-- Jadwal & Batas Waktu (Dinamis dari Master Gelombang - FR-19 & FR-45) --}}
        <div class="card-grid-2" style="margin-bottom: 3rem;">
            <div style="background-color: var(--canvas-bg); padding: 2rem; border-radius: var(--radius-card); border: 1px solid var(--border-default);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--primary-deep); margin: 0;">
                        Jadwal Gelombang Pendaftaran
                    </h3>
                    @if(isset($tahunAjaran->nama))
                        <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 9999px; font-weight: 700;">
                            Tahun Ajaran {{ $tahunAjaran->nama }}
                        </span>
                    @endif
                </div>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.875rem; font-size: 0.9375rem; padding: 0; margin: 0;">
                    @forelse($gelombangs ?? [] as $g)
                        @php
                            $todayStr = now()->format('Y-m-d');
                            $mulaiStr = $g->tanggal_mulai->format('Y-m-d');
                            $selesaiStr = $g->tanggal_selesai->format('Y-m-d');
                            $isOpen = ($todayStr >= $mulaiStr && $todayStr <= $selesaiStr);
                            $isUpcoming = ($todayStr < $mulaiStr);
                        @endphp
                        <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-default); padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <strong>{{ $g->nama_gelombang }}</strong>
                                    @if($g->is_bidikmisi)
                                        <span class="badge" style="background-color: #fef3c7; color: #b45309; font-size: 0.7rem; padding: 2px 6px;">Bidikmisi</span>
                                    @endif
                                    @if($isOpen)
                                        <span class="badge" style="background-color: #dcfce7; color: #15803d; font-size: 0.7rem; padding: 2px 6px;">Sedang Dibuka</span>
                                    @elseif($isUpcoming)
                                        <span class="badge" style="background-color: #f1f5f9; color: #64748b; font-size: 0.7rem; padding: 2px 6px;">Akan Datang</span>
                                    @else
                                        <span class="badge" style="background-color: #fee2e2; color: #991b1b; font-size: 0.7rem; padding: 2px 6px;">Ditutup</span>
                                    @endif
                                </div>
                                @if($g->keterangan)
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">{{ $g->keterangan }}</div>
                                @endif
                            </div>
                            <div style="font-weight: 700; color: var(--text-primary); text-align: right;">
                                {{ $g->tanggal_mulai->translatedFormat('d M Y') }} &ndash; {{ $g->tanggal_selesai->translatedFormat('d M Y') }}
                            </div>
                        </li>
                    @empty
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
                    @endforelse
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
