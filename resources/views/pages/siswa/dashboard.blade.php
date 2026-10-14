@extends('layouts.siswa')

@section('title', 'Dashboard Calon Siswa')

@section('content')
<div style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 0.25rem 0;">
                Selamat Datang, {{ $siswa->nama_lengkap }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0;">
                Nomor Pendaftaran: <strong>{{ $siswa->nomor_pendaftaran }}</strong> &bull; Jalur {{ ucfirst($siswa->jalur_pendaftaran) }} &bull; {{ $siswa->jurusan->nama_jurusan }}
            </p>
        </div>

        <a href="{{ route('siswa.bukti') }}" target="_blank" class="btn btn-secondary btn-sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Cetak Ulang Bukti Pendaftaran</span>
        </a>
    </div>
</div>

{{-- Banner Clearance Seragam (FR-09 & BR-05) --}}
@if($isClearanceSeragam)
    <div class="clearance-banner">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <div>
            <div class="clearance-title">
                Clearance Seragam: Seluruh Berkas Wajib Telah Disetujui!
            </div>
            <div class="clearance-desc">
                @if($siswa->pengambilanSeragam)
                    <strong>Status Pengambilan Seragam:</strong> Seragam telah resmi diambil pada 
                    {{ $siswa->pengambilanSeragam->waktu_ambil->translatedFormat('l, d F Y - H:i') }} WIB 
                    dilayani oleh petugas loket <strong>{{ $siswa->pengambilanSeragam->petugas->name ?? 'Tata Usaha' }}</strong>.
                @else
                    Selamat! Seluruh berkas wajib Anda telah terverifikasi dan memenuhi syarat kelulusan berkas. Anda dipersilakan datang ke ruang Tata Usaha (TU) SMK Muhammadiyah Paguyangan untuk melakukan pengukuran dan pengambilan seragam sekolah dengan membawa lembar Bukti Pendaftaran fisik.
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Countdown Timer Box (FR-08 & FR-10) --}}
<div class="countdown-box {{ $isLockdown ? 'is-lockdown' : '' }}" id="countdownBox">
    <div>
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $isLockdown ? 'var(--status-danger-text)' : 'var(--primary-base)' }};">
            {{ $isLockdown ? 'Akses Berkas Terkunci (Lockdown)' : 'Batas Waktu Pengunggahan Berkas' }}
        </div>
        <div style="font-size: 1.125rem; font-weight: 800; color: var(--text-heading); margin-top: 0.25rem;">
            @if($isLockdown)
                Tenggat Waktu Berkas Telah Berakhir
            @else
                Tenggat: {{ $pengaturan->batas_waktu_berkas ? $pengaturan->batas_waktu_berkas->translatedFormat('d F Y, H:i') . ' WIB' : 'Ditentukan Panitia' }}
            @endif
        </div>
        <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">
            @if($isLockdown)
                Pengunggahan berkas dinonaktifkan otomatis oleh server (BR-08). Silakan hubungi Panitia PPDB / Super Admin untuk permohonan pembukaan akses.
            @else
                Pastikan seluruh berkas wajib diunggah sebelum hitung mundur di samping habis.
            @endif
        </div>
    </div>

    @if(!$isLockdown && $siswa->sisa_waktu_detik > 0)
        <div class="countdown-timer-units" id="timerContainer" data-seconds="{{ $siswa->sisa_waktu_detik }}">
            <div class="timer-unit">
                <div class="timer-val" id="daysVal">00</div>
                <div class="timer-lbl">Hari</div>
            </div>
            <div class="timer-unit">
                <div class="timer-val" id="hoursVal">00</div>
                <div class="timer-lbl">Jam</div>
            </div>
            <div class="timer-unit">
                <div class="timer-val" id="minutesVal">00</div>
                <div class="timer-lbl">Menit</div>
            </div>
            <div class="timer-unit">
                <div class="timer-val" id="secondsVal">00</div>
                <div class="timer-lbl">Detik</div>
            </div>
        </div>
    @endif
</div>

{{-- Grid Informasi Siswa & Progres Berkas --}}
<div class="card-grid-2" style="margin-bottom: 1.5rem;">
    {{-- Card Biodata Siswa --}}
    <div class="portal-card">
        <div class="portal-card-header">
            <div class="portal-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Informasi Pendaftaran</span>
            </div>
            <span class="badge badge-primary">
                {{ $siswa->gelombang->nama_gelombang ?? 'Gelombang Reguler' }}
            </span>
        </div>

        <table class="dense-table" style="font-size: 0.875rem;">
            <tbody>
                <tr>
                    <td style="color: var(--text-muted); width: 38%;">Nama Siswa</td>
                    <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">NIK / NISN</td>
                    <td>{{ $siswa->nik }} / {{ $siswa->nisn }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Asal Sekolah</td>
                    <td>{{ $siswa->asal_sekolah }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Jurusan Pilihan</td>
                    <td><strong>{{ $siswa->jurusan->nama_jurusan }}</strong> ({{ $siswa->jurusan->kode }})</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Benefit Bebas SPP</td>
                    <td>
                        @if($siswa->status_benefit === 'kandidat_100_pertama')
                            <span class="badge" style="background-color: var(--status-warning-bg); color: var(--status-warning-text); border-color: var(--status-warning-border);">
                                Kandidat 100 Pendaftar Pertama (Gratis SPP 3 Bulan Bersyarat)
                            </span>
                        @elseif($siswa->status_benefit === 'klaim_aktif')
                            <span class="badge badge-success">
                                Resmi Aktif (Bebas Biaya SPP 3 Bulan)
                            </span>
                        @else
                            <span class="badge" style="background-color: #f1f5f9; color: var(--text-muted); border-color: var(--border-default);">
                                Jalur Reguler
                            </span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Card Ringkasan Progres Berkas --}}
    <div class="portal-card">
        <div class="portal-card-header">
            <div class="portal-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                <span>Progres Kelengkapan Berkas</span>
            </div>
            <span style="font-size: 0.875rem; font-weight: 700; color: var(--primary-base);">
                {{ $uploadedWajibCount }} dari {{ $totalWajib }} Berkas Wajib
            </span>
        </div>

        <div style="margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">
                <span>Kelengkapan Berkas Wajib</span>
                <span>{{ $persentaseProgres }}%</span>
            </div>
            <div style="background-color: var(--surface-subdued); height: 10px; border-radius: var(--radius-pill); overflow: hidden;">
                <div style="background-color: var(--primary-base); height: 100%; width: {{ $persentaseProgres }}%; border-radius: var(--radius-pill); transition: width 0.3s ease;"></div>
            </div>
        </div>

        <div style="background-color: var(--canvas-bg); padding: 0.875rem; border-radius: var(--radius-md); font-size: 0.8125rem; color: var(--text-body); border: 1px solid var(--border-default);">
            <strong>Ketentuan Unggah Berkas:</strong>
            <ul style="padding-left: 1.25rem; margin: 0.35rem 0 0 0; display: flex; flex-direction: column; gap: 0.25rem;">
                <li>Format file yang diizinkan: <strong>PDF, JPG, JPEG, PNG</strong>.</li>
                <li>Ukuran maksimal tiap file: <strong>{{ $pengaturan->max_file_size_mb }} MB</strong>.</li>
                <li>Berkas dapat dicicil dan diunggah bertahap kapan saja sebelum batas waktu.</li>
            </ul>
        </div>
    </div>
</div>

{{-- Tabel Berkas Persyaratan (FR-07, FR-13, FR-25) --}}
<div class="portal-card">
    <div class="portal-card-header">
        <div class="portal-card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 11 12 14 22 4"></polyline>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
            <span>Daftar Berkas Dokumen Persyaratan</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="berkas-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Persyaratan Berkas</th>
                    <th style="width: 120px;">Sifat</th>
                    <th style="width: 180px;">Status Verifikasi</th>
                    <th style="width: 220px;">File Dokumen</th>
                    <th style="width: 220px; text-align: right;">Aksi Unggah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jenisBerkasList as $index => $jenis)
                    @php
                        $isWajib = $jenis->isWajibUntukJalur($siswa->jalur_pendaftaran);
                        $berkasUpload = $uploadedBerkasMap->get($jenis->id);
                        $status = $berkasUpload ? $berkasUpload->status : 'belum_diunggah';
                        $canUpload = in_array($status, ['belum_diunggah', 'ditolak']) && !$isLockdown;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 700; color: var(--primary-deep);">{{ $jenis->nama }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">{{ $jenis->deskripsi }}</div>

                            {{-- Catatan Penolakan (FR-25 & BR-04) --}}
                            @if($berkasUpload && $berkasUpload->status === 'ditolak' && $berkasUpload->catatan_penolakan)
                                <div class="berkas-reject-note">
                                    <strong>Catatan Panitia:</strong> {{ $berkasUpload->catatan_penolakan }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($isWajib)
                                <span class="badge" style="background-color: #fee2e2; color: #b91c1c; border-color: #fecaca;">
                                    Wajib
                                </span>
                            @else
                                <span class="badge" style="background-color: #f1f5f9; color: #475569; border-color: #e2e8f0;">
                                    Opsional
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($status === 'disetujui')
                                <span class="badge badge-success">
                                    Disetujui
                                </span>
                            @elseif($status === 'menunggu')
                                <span class="badge badge-accent">
                                    Menunggu Verifikasi
                                </span>
                            @elseif($status === 'ditolak')
                                <span class="badge" style="background-color: var(--status-danger-bg); color: var(--status-danger-text); border-color: var(--status-danger-border);">
                                    Ditolak (Perlu Perbaikan)
                                </span>
                            @else
                                <span class="badge" style="background-color: #f1f5f9; color: var(--text-muted); border-color: var(--border-default);">
                                    Belum Diunggah
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($berkasUpload)
                                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                    <a href="{{ route('siswa.berkas.lihat', $berkasUpload->id) }}" target="_blank" style="font-weight: 600; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <span>Lihat Berkas</span>
                                    </a>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                        Ukuran: {{ $berkasUpload->file_size_kb }} KB &bull; {{ $berkasUpload->updated_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            @else
                                <span style="font-size: 0.8125rem; color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            @if($canUpload)
                                <form action="{{ route('siswa.berkas.upload') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                    @csrf
                                    <input type="hidden" name="jenis_berkas_id" value="{{ $jenis->id }}">
                                    <input type="file" name="berkas" required accept=".pdf,.jpg,.jpeg,.png" style="font-size: 0.75rem; max-width: 140px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        {{ $status === 'ditolak' ? 'Unggah Ulang' : 'Unggah' }}
                                    </button>
                                </form>
                            @elseif($isLockdown)
                                <span style="font-size: 0.75rem; color: var(--status-danger-text); font-weight: 600;">
                                    Terkunci (Lockdown)
                                </span>
                            @else
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">
                                    Terkunci (Sudah {{ ucfirst($status) }})
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Timer Hitung Mundur Server (FR-08)
    (function () {
        var container = document.getElementById('timerContainer');
        if (!container) return;

        var totalSeconds = parseInt(container.getAttribute('data-seconds'), 10);
        if (isNaN(totalSeconds) || totalSeconds <= 0) return;

        var daysEl = document.getElementById('daysVal');
        var hoursEl = document.getElementById('hoursVal');
        var minsEl = document.getElementById('minutesVal');
        var secsEl = document.getElementById('secondsVal');

        function pad(n) { return n < 10 ? '0' + n : n; }

        function updateTimer() {
            if (totalSeconds <= 0) {
                container.innerHTML = '<span style="color: var(--status-danger-text); font-weight: 700;">Waktu Habis (Lockdown)</span>';
                clearInterval(interval);
                return;
            }

            var days = Math.floor(totalSeconds / 86400);
            var hours = Math.floor((totalSeconds % 86400) / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = totalSeconds % 60;

            if (daysEl) daysEl.textContent = pad(days);
            if (hoursEl) hoursEl.textContent = pad(hours);
            if (minsEl) minsEl.textContent = pad(minutes);
            if (secsEl) secsEl.textContent = pad(seconds);

            totalSeconds--;
        }

        updateTimer();
        var interval = setInterval(updateTimer, 1000);
    })();
</script>
@endpush
