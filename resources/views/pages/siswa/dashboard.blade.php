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

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" onclick="document.getElementById('modalGantiPassword').style.display='flex'" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; background: #ffffff; border: 1px solid var(--border-default); color: var(--text-body);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Ganti Kata Sandi</span>
            </button>

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
</div>

{{-- Banner Peringatan Kata Sandi Default (Rekomendasi Ubah Password) --}}
@if($siswa->isMenggunakanPasswordDefault())
    <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            <div>
                <div style="font-weight: 700; color: #92400e; font-size: 0.9375rem; margin-bottom: 0.25rem;">
                    Tingkatkan Keamanan Akun Anda
                </div>
                <div style="font-size: 0.8125rem; color: #78350f; line-height: 1.4;">
                    Saat ini Anda masih menggunakan kata sandi bawaan (tanggal lahir). Disarankan untuk segera mengganti kata sandi dengan kombinasi baru yang aman.
                </div>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('modalGantiPassword').style.display='flex'" class="btn btn-sm" style="background-color: #d97706; color: #ffffff; border: none; font-weight: 600; white-space: nowrap; cursor: pointer;">
            Ganti Kata Sandi Sekarang
        </button>
    </div>
@endif

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
                Pengunggahan berkas dinonaktifkan otomatis oleh server. Silakan hubungi Panitia PPDB / Super Admin untuk permohonan pembukaan akses.
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

{{-- Card Status Pembayaran Biaya Pendaftaran --}}
<div class="portal-card" style="margin-bottom: 1.5rem; border-left: 4px solid {{ $pembayaran->isLunas() ? 'var(--status-success-base)' : ($pembayaran->isMenungguVerifikasi() ? 'var(--primary-base)' : ($pembayaran->isDitolak() ? 'var(--status-danger-base)' : 'var(--accent-warm)')) }};">
    <div class="portal-card-header" style="flex-wrap: wrap; gap: 0.75rem;">
        <div class="portal-card-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ $pembayaran->isLunas() ? 'var(--status-success-base)' : 'var(--primary-base)' }};">
                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                <line x1="2" y1="10" x2="22" y2="10"></line>
                <circle cx="7" cy="15" r="1"></circle>
                <line x1="12" y1="15" x2="17" y2="15"></line>
            </svg>
            <div>
                <span style="display: block; font-size: 1rem; font-weight: 800; color: var(--text-heading);">Biaya Pendaftaran Siswa Baru</span>
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">No. Transaksi: <strong>{{ $pembayaran->kode_transaksi }}</strong></span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @if($pembayaran->isLunas())
                <span class="badge badge-success" style="font-size: 0.8125rem; padding: 0.35rem 0.75rem;">
                    ✓ Lunas &amp; Terverifikasi
                </span>
                <a href="{{ route('siswa.pembayaran.kuitansi') }}" target="_blank" class="btn btn-secondary btn-sm" style="font-weight: 700;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Unduh Kuitansi (PDF)</span>
                </a>
            @elseif($pembayaran->isMenungguVerifikasi())
                <span class="badge" style="background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.8125rem; padding: 0.35rem 0.75rem;">
                    ⏳ Menunggu Verifikasi Panitia
                </span>
            @elseif($pembayaran->isDitolak())
                <span class="badge badge-danger" style="font-size: 0.8125rem; padding: 0.35rem 0.75rem;">
                    ✕ Bukti Transfer Ditolak
                </span>
            @else
                <span class="badge" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 0.8125rem; padding: 0.35rem 0.75rem;">
                    ⚠ Belum Dibayar
                </span>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1rem; align-items: start;">
        {{-- Sisi Kiri: Rincian Nominal & Rekening --}}
        <div style="background-color: var(--canvas-bg); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-default);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem;">
                Jumlah yang Harus Dibayar
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: {{ $pembayaran->isLunas() ? 'var(--status-success-base)' : 'var(--primary-deep)' }}; margin-bottom: 0.75rem;">
                {{ $pembayaran->formatted_nominal }}
            </div>

            <div style="font-size: 0.8125rem; color: var(--text-body); line-height: 1.6; display: flex; flex-direction: column; gap: 0.35rem;">
                <div>
                    <span style="color: var(--text-muted); display: inline-block; width: 110px;">Metode Transfer:</span>
                    <strong>{{ $pengaturan->nama_bank ?? 'Bank BSI' }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: inline-block; width: 110px;">No. Rekening:</span>
                    <strong style="font-size: 0.9375rem; letter-spacing: 0.03em; color: var(--primary-base);">{{ $pengaturan->nomor_rekening ?? '-' }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: inline-block; width: 110px;">Atas Nama:</span>
                    <strong>{{ $pengaturan->atas_nama_rekening ?? 'SMK Muhammadiyah Paguyangan' }}</strong>
                </div>
            </div>

            <div style="margin-top: 0.85rem; padding-top: 0.85rem; border-top: 1px dashed var(--border-default); font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;">
                <div style="font-weight: 700; color: var(--text-heading); margin-bottom: 0.2rem;">Opsi Pembayaran Tunai (Offline):</div>
                Calon siswa atau orang tua dapat datang langsung ke loket keuangan sekolah dengan menyebutkan Nomor Pendaftaran <strong>{{ $siswa->nomor_pendaftaran }}</strong>.
            </div>
        </div>

        {{-- Sisi Kanan: Status / Form Konfirmasi --}}
        <div>
            @if($pembayaran->isLunas())
                <div style="background-color: var(--status-success-bg); border: 1px solid var(--status-success-border); padding: 1.25rem; border-radius: var(--radius-md);">
                    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--status-success-base); shrink: 0;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div>
                            <div style="font-weight: 800; color: var(--status-success-text); font-size: 0.9375rem; margin-bottom: 0.25rem;">
                                Pembayaran Resmi Terverifikasi Lunas
                            </div>
                            <div style="font-size: 0.8125rem; color: var(--status-success-text); opacity: 0.9; line-height: 1.5;">
                                Metode: <strong>{{ $pembayaran->metode_pembayaran === 'tunai' ? 'Tunai di Loket Sekolah' : 'Transfer Bank' }}</strong> &bull;
                                Tanggal: <strong>{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->translatedFormat('d F Y') : '-' }}</strong>.<br>
                                Diverifikasi oleh: <strong>{{ $pembayaran->verifikator->name ?? 'Panitia PPDB' }}</strong>.
                            </div>

                            @if($pembayaran->bukti_transfer)
                                <div style="margin-top: 0.75rem;">
                                    <a href="{{ route('siswa.pembayaran.bukti') }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                        Lihat Bukti Transfer Tersimpan &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($pembayaran->isMenungguVerifikasi())
                <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 1.25rem; border-radius: var(--radius-md);">
                    <div style="font-weight: 700; color: #1e3a8a; font-size: 0.9375rem; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>Bukti Pembayaran Sedang Diverifikasi</span>
                    </div>
                    <p style="font-size: 0.8125rem; color: #1e40af; margin: 0 0 0.75rem 0; line-height: 1.5;">
                        Terima kasih! Bukti transfer atas nama <strong>{{ $pembayaran->nama_pengirim }}</strong> ({{ $pembayaran->bank_pengirim }}) tanggal <strong>{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->translatedFormat('d F Y') : '-' }}</strong> telah diterima sistem. Petugas keuangan sekolah akan memeriksa mutasi bank dan memvalidasi pembayaran Anda.
                    </p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="{{ route('siswa.pembayaran.bukti') }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                            Lihat Bukti yang Telah Diunggah
                        </a>
                    </div>
                </div>
            @else
                {{-- Form Konfirmasi Transfer (Belum Bayar atau Ditolak) --}}
                @if($pembayaran->isDitolak())
                    <div style="background-color: var(--status-danger-bg); border: 1px solid var(--status-danger-border); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                        <div style="font-weight: 700; color: var(--status-danger-text); font-size: 0.875rem;">
                            Bukti Transfer Sebelumnya Ditolak Panitia:
                        </div>
                        <div style="font-size: 0.8125rem; color: var(--status-danger-text); margin-top: 0.25rem;">
                            {{ $pembayaran->catatan_admin ?: 'Bukti pembayaran tidak terbaca atau nominal tidak sesuai. Silakan unggah bukti yang benar.' }}
                        </div>
                    </div>
                @endif

                <form action="{{ route('siswa.pembayaran.konfirmasi') }}" method="POST" enctype="multipart/form-data" style="background-color: #ffffff; border: 1px solid var(--border-default); padding: 1.25rem; border-radius: var(--radius-md);">
                    @csrf
                    <div style="font-weight: 700; font-size: 0.875rem; color: var(--text-heading); margin-bottom: 0.75rem;">
                        Form Konfirmasi Transfer Bank
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div>
                            <label class="form-label" style="font-size: 0.75rem;">Nama Pengirim / Pemilik Rekening <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pengirim" class="form-control" placeholder="Contoh: Budi Santoso" value="{{ old('nama_pengirim', $pembayaran->nama_pengirim) }}" required style="font-size: 0.8125rem; padding: 0.4rem 0.6rem;">
                            @error('nama_pengirim') <div style="color: #ef4444; font-size: 0.7rem; margin-top: 0.2rem;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.75rem;">Bank / E-Wallet Pengirim <span class="text-rose-500">*</span></label>
                            <input type="text" name="bank_pengirim" class="form-control" placeholder="Contoh: BRI / BCA / Mandiri / Dana" value="{{ old('bank_pengirim', $pembayaran->bank_pengirim) }}" required style="font-size: 0.8125rem; padding: 0.4rem 0.6rem;">
                            @error('bank_pengirim') <div style="color: #ef4444; font-size: 0.7rem; margin-top: 0.2rem;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div>
                            <label class="form-label" style="font-size: 0.75rem;">Tanggal Transfer <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_bayar" class="form-control" max="{{ date('Y-m-d') }}" value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('Y-m-d') : date('Y-m-d')) }}" required style="font-size: 0.8125rem; padding: 0.4rem 0.6rem;">
                            @error('tanggal_bayar') <div style="color: #ef4444; font-size: 0.7rem; margin-top: 0.2rem;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.75rem;">Foto Bukti Struk Transfer <span class="text-rose-500">*</span></label>
                            <input type="file" name="bukti_transfer" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="font-size: 0.75rem; padding: 0.35rem 0.5rem;">
                            <small style="color: var(--text-muted); font-size: 0.6875rem;">PDF, JPG, PNG (Maks {{ $pengaturan->max_file_size_mb }} MB)</small>
                            @error('bukti_transfer') <div style="color: #ef4444; font-size: 0.7rem; margin-top: 0.2rem;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; font-weight: 700; margin-top: 0.25rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Kirim Bukti Pembayaran</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
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

    {{-- Tampilan Desktop & Tablet: Tabel Terstruktur --}}
    <div class="berkas-desktop-view">
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

    {{-- Tampilan Mobile (Smartphone / Android): Kartu Dokumen Mandiri Touch-Friendly --}}
    <div class="berkas-mobile-cards">
        @foreach($jenisBerkasList as $index => $jenis)
            @php
                $isWajib = $jenis->isWajibUntukJalur($siswa->jalur_pendaftaran);
                $berkasUpload = $uploadedBerkasMap->get($jenis->id);
                $status = $berkasUpload ? $berkasUpload->status : 'belum_diunggah';
                $canUpload = in_array($status, ['belum_diunggah', 'ditolak']) && !$isLockdown;

                $cardStatusClass = match($status) {
                    'disetujui' => 'status-disetujui',
                    'menunggu' => 'status-menunggu',
                    'ditolak' => 'status-ditolak',
                    default => 'status-belum',
                };
            @endphp
            <div class="berkas-mobile-card {{ $cardStatusClass }}">
                <div class="berkas-card-header">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Berkas #{{ $index + 1 }}</span>
                        <div class="berkas-card-title">{{ $jenis->nama }}</div>
                    </div>
                </div>

                <div class="berkas-card-badges">
                    @if($isWajib)
                        <span class="badge" style="background-color: #fee2e2; color: #b91c1c; border-color: #fecaca; font-size: 0.7rem; padding: 2px 7px;">
                            Wajib
                        </span>
                    @else
                        <span class="badge" style="background-color: #f1f5f9; color: #475569; border-color: #e2e8f0; font-size: 0.7rem; padding: 2px 7px;">
                            Opsional
                        </span>
                    @endif

                    @if($status === 'disetujui')
                        <span class="badge badge-success" style="font-size: 0.7rem; padding: 2px 7px;">
                            Disetujui
                        </span>
                    @elseif($status === 'menunggu')
                        <span class="badge badge-accent" style="font-size: 0.7rem; padding: 2px 7px;">
                            Menunggu Verifikasi
                        </span>
                    @elseif($status === 'ditolak')
                        <span class="badge" style="background-color: var(--status-danger-bg); color: var(--status-danger-text); border-color: var(--status-danger-border); font-size: 0.7rem; padding: 2px 7px;">
                            Ditolak (Perlu Perbaikan)
                        </span>
                    @else
                        <span class="badge" style="background-color: #f1f5f9; color: var(--text-muted); border-color: var(--border-default); font-size: 0.7rem; padding: 2px 7px;">
                            Belum Diunggah
                        </span>
                    @endif
                </div>

                @if($jenis->deskripsi)
                    <div class="berkas-card-desc">{{ $jenis->deskripsi }}</div>
                @endif

                {{-- Catatan Penolakan Panitia --}}
                @if($berkasUpload && $berkasUpload->status === 'ditolak' && $berkasUpload->catatan_penolakan)
                    <div class="berkas-reject-note" style="margin-bottom: 0.875rem;">
                        <strong>Catatan Panitia:</strong> {{ $berkasUpload->catatan_penolakan }}
                    </div>
                @endif

                {{-- File yang sudah terunggah --}}
                @if($berkasUpload)
                    <div class="berkas-card-preview">
                        <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: var(--primary-base);">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            <div style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <div style="font-weight: 700; color: var(--text-heading); font-size: 0.8125rem;">File Terunggah</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $berkasUpload->file_size_kb }} KB &bull; {{ $berkasUpload->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <a href="{{ route('siswa.berkas.lihat', $berkasUpload->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="flex-shrink: 0; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            Buka File
                        </a>
                    </div>
                @endif

                {{-- Tombol & Formulir Aksi Unggah --}}
                <div class="berkas-card-action">
                    @if($canUpload)
                        <form action="{{ route('siswa.berkas.upload') }}" method="POST" enctype="multipart/form-data" class="berkas-upload-form">
                            @csrf
                            <input type="hidden" name="jenis_berkas_id" value="{{ $jenis->id }}">
                            <div class="mobile-file-wrapper">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-heading); margin-bottom: 0.25rem;">
                                    Pilih Dokumen Foto / PDF:
                                </label>
                                <input type="file" name="berkas" required accept=".pdf,.jpg,.jpeg,.png" class="mobile-file-input">
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%; min-height: 42px; font-weight: 700;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span>{{ $status === 'ditolak' ? 'Unggah Ulang Dokumen Perbaikan' : 'Unggah Dokumen Ini' }}</span>
                            </button>
                        </form>
                    @elseif($isLockdown)
                        <div style="font-size: 0.8125rem; color: var(--status-danger-text); font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <span>Akses Terkunci (Batas Waktu Telah Berakhir)</span>
                        </div>
                    @else
                        <div style="font-size: 0.8125rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span>Dokumen sudah berstatus: <strong>{{ ucfirst($status) }}</strong></span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modal Ganti Kata Sandi Siswa --}}
<div id="modalGantiPassword" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; border-radius: 12px; max-width: 480px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-default); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; color: var(--primary-deep); font-size: 1.125rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Ubah Kata Sandi Akun</span>
            </div>
            <button type="button" onclick="document.getElementById('modalGantiPassword').style.display='none'" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0.25rem; font-size: 1.25rem; line-height: 1;">
                &times;
            </button>
        </div>

        <form action="{{ route('siswa.password.update') }}" method="POST" style="padding: 1.5rem;">
            @csrf

            @if($errors->has('password_saat_ini') || $errors->has('password_baru'))
                <div class="smk-alert smk-alert-danger" style="margin-bottom: 1rem;">
                    <div>{{ $errors->first('password_saat_ini') ?? $errors->first('password_baru') }}</div>
                </div>
            @endif

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="password_saat_ini" class="form-label" style="font-size: 0.875rem; font-weight: 600;">
                    Kata Sandi Saat Ini <span style="color: #ef4444;">*</span>
                </label>
                <input type="password" 
                       name="password_saat_ini" 
                       id="password_saat_ini" 
                       class="form-control" 
                       placeholder="Sandi saat ini atau tanggal lahir (DDMMYYYY)" 
                       required>
                <span class="form-help" style="font-size: 0.75rem;">Jika belum pernah ganti, ketik tanggal lahir format <strong>DDMMYYYY</strong> (contoh: <em>21072008</em>).</span>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="password_baru" class="form-label" style="font-size: 0.875rem; font-weight: 600;">
                    Kata Sandi Baru <span style="color: #ef4444;">*</span>
                </label>
                <input type="password" 
                       name="password_baru" 
                       id="password_baru" 
                       class="form-control" 
                       placeholder="Minimal 6 karakter" 
                       minlength="6" 
                       required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password_baru_confirmation" class="form-label" style="font-size: 0.875rem; font-weight: 600;">
                    Ulangi Kata Sandi Baru <span style="color: #ef4444;">*</span>
                </label>
                <input type="password" 
                       name="password_baru_confirmation" 
                       id="password_baru_confirmation" 
                       class="form-control" 
                       placeholder="Ketik ulang kata sandi baru" 
                       minlength="6" 
                       required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalGantiPassword').style.display='none'" class="btn btn-secondary btn-sm">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    Simpan Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Timer Hitung Mundur Server
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

    @if($errors->has('password_saat_ini') || $errors->has('password_baru'))
        var modalPass = document.getElementById('modalGantiPassword');
        if (modalPass) modalPass.style.display = 'flex';
    @endif
</script>
@endpush
