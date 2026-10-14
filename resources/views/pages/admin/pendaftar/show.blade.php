@extends('layouts.admin')

@section('title', 'Detail & Verifikasi — ' . $siswa->nama_lengkap)
@section('header-title', $siswa->nama_lengkap . ' (' . $siswa->nomor_pendaftaran . ')')
@section('header-subtitle', 'Jalur ' . ucfirst($siswa->jalur_pendaftaran) . ' • ' . $siswa->jurusan->nama_jurusan . ' • Terdaftar: ' . $siswa->created_at->format('d/m/Y H:i'))

@section('header-action')
<div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
    <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
        &larr; <span>Kembali ke Daftar</span>
    </a>

    {{-- Tombol Unlock Lockdown (Super Admin Only - FR-17) --}}
    @if(auth('web')->user()->isSuperAdmin())
        <form action="{{ route('admin.pendaftar.unlock', $siswa->id) }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-sm {{ $siswa->is_unlocked ? 'btn-secondary' : 'btn-accent' }}" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    @if($siswa->is_unlocked)
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    @else
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                    @endif
                </svg>
                <span>{{ $siswa->is_unlocked ? 'Kunci Akses Upload' : 'Bypass Unlock Upload' }}</span>
            </button>
        </form>
    @endif

    <a href="{{ route('ppdb.bukti', $siswa->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <span>Cetak Bukti Fisik</span>
    </a>
</div>
@endsection

@section('content')
{{-- Banner Status Clearance Seragam & Kuota Benefit --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="admin-card" style="margin-bottom: 0;">
        <div class="admin-card-body" style="padding: 1.25rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Status Clearance Seragam (BR-05)</div>
            <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                @if($siswa->pengambilanSeragam)
                    <span class="badge-status badge-status-success">
                        <span class="badge-status-dot"></span>
                        Sudah Diambil
                    </span>
                    <span style="font-size: 0.8rem; color: #64748b;">
                        pada {{ $siswa->pengambilanSeragam->waktu_ambil->format('d/m/Y H:i') }} (Oleh: {{ $siswa->pengambilanSeragam->petugas->name ?? 'Staf' }})
                    </span>
                @elseif($isClearanceSeragam)
                    <span class="badge-status badge-status-success">
                        <span class="badge-status-dot"></span>
                        Memenuhi Syarat (Clearance Disetujui)
                    </span>
                    <span style="font-size: 0.8rem; color: #334155;">Siswa berhak mengambil seragam di loket.</span>
                @else
                    <span class="badge-status badge-status-danger">
                        <span class="badge-status-dot"></span>
                        Belum Memenuhi Syarat
                    </span>
                    <span style="font-size: 0.8rem; color: #64748b;">Masih ada berkas wajib yang belum disetujui.</span>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-card" style="margin-bottom: 0;">
        <div class="admin-card-body" style="padding: 1.25rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Benefit Bebas SPP 3 Bulan (FR-43)</div>
            <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                @if($siswa->status_benefit === 'klaim_aktif')
                    <span class="badge-status badge-status-success">
                        <span class="badge-status-dot"></span>
                        Resmi Berhak (Aktif)
                    </span>
                    <span style="font-size: 0.8rem; color: #334155;">Seluruh berkas lolos verifikasi Disetujui.</span>
                @elseif($siswa->status_benefit === 'kandidat_100_pertama')
                    <span class="badge-status badge-status-warning">
                        <span class="badge-status-dot"></span>
                        Kandidat 100 Pendaftar Pertama
                    </span>
                    <span style="font-size: 0.8rem; color: #64748b;">Menunggu verifikasi seluruh berkas wajib.</span>
                @else
                    <span class="badge-status" style="background-color: #f1f5f9; color: #64748b;">Reguler</span>
                    <span style="font-size: 0.8rem; color: #64748b;">Tidak masuk alokasi 100 pendaftar pertama.</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Panel Verifikasi Berkas (FR-13 & BR-04) --}}
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Pemeriksaan dan Verifikasi Berkas Dokumen (FR-13)</h2>
    </div>

    <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Persyaratan</th>
                    <th>Sifat</th>
                    <th>Status Terkini</th>
                    <th>Pratinjau Dokumen</th>
                    <th>Verifikator Terakhir</th>
                    <th style="text-align: right; width: 260px;">Keputusan Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jenisBerkasList as $idx => $jenis)
                    @php
                        $isWajib = $jenis->isWajibUntukJalur($siswa->jalur_pendaftaran);
                        $berkas = $berkasMap->get($jenis->id);
                    @endphp
                    <tr>
                        <td style="color: #64748b; font-weight: 600;">{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color: #092347;">{{ $jenis->nama }}</strong>
                            @if($berkas && $berkas->status === 'ditolak' && $berkas->catatan_penolakan)
                                <div style="background-color: #fee2e2; color: #b91c1c; padding: 0.35rem 0.6rem; border-radius: 4px; font-size: 0.775rem; margin-top: 0.35rem;">
                                    <strong>Alasan Penolakan:</strong> {{ $berkas->catatan_penolakan }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($isWajib)
                                <span class="badge-status badge-status-danger">Wajib</span>
                            @else
                                <span class="badge-status" style="background-color: #f1f5f9; color: #64748b;">Opsional</span>
                            @endif
                        </td>
                        <td>
                            @if($berkas)
                                @if($berkas->status === 'disetujui')
                                    <span class="badge-status badge-status-success">
                                        <span class="badge-status-dot"></span>
                                        Disetujui
                                    </span>
                                @elseif($berkas->status === 'menunggu')
                                    <span class="badge-status badge-status-warning">
                                        <span class="badge-status-dot"></span>
                                        Menunggu
                                    </span>
                                @elseif($berkas->status === 'ditolak')
                                    <span class="badge-status badge-status-danger">
                                        <span class="badge-status-dot"></span>
                                        Ditolak
                                    </span>
                                @endif
                            @else
                                <span class="badge-status" style="background-color: #f1f5f9; color: #64748b;">Belum Unggah</span>
                            @endif
                        </td>
                        <td>
                            @if($berkas)
                                <a href="{{ route('admin.berkas.lihat', $berkas->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.775rem; padding: 4px 8px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span>Buka File ({{ $berkas->file_size_kb }} KB)</span>
                                </a>
                            @else
                                <span style="font-size: 0.8rem; color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td style="font-size: 0.775rem; color: #64748b;">
                            @if($berkas && $berkas->verifikator)
                                <strong style="color: #092347;">{{ $berkas->verifikator->name }}</strong><br>
                                <span>{{ $berkas->waktu_verifikasi?->format('d/m/Y H:i') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: right;">
                            @if($berkas)
                                {{-- Form Approve & Reject --}}
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                    {{-- Tombol Approve --}}
                                    <form action="{{ route('admin.pendaftar.berkas.verifikasi', $berkas->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="status" value="disetujui">
                                        <button type="submit" class="btn btn-sm btn-outline-success" style="padding: 4px 10px; font-size: 0.775rem; font-weight: 600;" {{ $berkas->status === 'disetujui' ? 'disabled' : '' }}>
                                            Setujui
                                        </button>
                                    </form>

                                    {{-- Tombol Reject dengan Catatan Wajib (BR-04) --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            style="padding: 4px 10px; font-size: 0.775rem; font-weight: 600;"
                                            onclick="bukaModalTolak({{ $berkas->id }}, '{{ addslashes($jenis->nama) }}')">
                                        Tolak
                                    </button>
                                </div>
                            @else
                                <span style="font-size: 0.775rem; color: #94a3b8; font-style: italic;">
                                    Menunggu unggahan siswa
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Penolakan Berkas --}}
<div id="modalTolak" style="display: none; position: fixed; inset: 0; background: rgba(9, 35, 71, 0.6); z-index: 1060; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #ffffff; width: 100%; max-width: 480px; border-radius: 10px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 style="font-size: 1.125rem; font-weight: 800; color: #b91c1c; margin: 0 0 0.5rem 0;">
            Tolak Berkas Persyaratan
        </h3>
        <p style="font-size: 0.85rem; color: #475569; margin: 0 0 1rem 0; line-height: 1.4;">
            Berkas: <strong id="modalBerkasNama" style="color: #092347;">-</strong>. Sesuai aturan bisnis <strong>BR-04</strong>, penolakan berkas <strong>WAJIB disertai catatan alasan/perbaikan</strong> agar pendaftar dapat memperbaikinya.
        </p>

        <form id="formTolakBerkas" method="POST" action="">
            @csrf
            <input type="hidden" name="status" value="ditolak">
            
            <div class="form-group mb-4">
                <label for="catatan_penolakan" class="form-label">Catatan Alasan Penolakan <span style="color: #ef4444;">*</span></label>
                <textarea name="catatan_penolakan" id="catatan_penolakan" class="form-control" rows="3" placeholder="Contoh: Foto dokumen buram dan terpotong. Harap scan ulang secara utuh." required></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="tutupModalTolak()">Batal</button>
                <button type="submit" class="btn btn-sm btn-outline-danger">Simpan Penolakan</button>
            </div>
        </form>
    </div>
</div>

{{-- Biodata Lengkap Calon Siswa & Form Koreksi Super Admin (FR-29) --}}
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Biodata Lengkap Calon Siswa</h2>
        @if(auth('web')->user()->isSuperAdmin())
            <span class="badge-status badge-status-info" style="font-size: 0.75rem;">Mode Koreksi Super Admin (FR-29)</span>
        @endif
    </div>

    <div class="admin-card-body">
        @if(auth('web')->user()->isSuperAdmin())
            {{-- Form Koreksi Data Siswa Super Admin --}}
            <form action="{{ route('admin.pendaftar.koreksi', $siswa->id) }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Pilihan Jurusan</label>
                        <select name="jurusan_id" class="form-select" required>
                            @foreach($allJurusans as $j)
                                <option value="{{ $j->id }}" {{ $siswa->jurusan_id == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }} ({{ $j->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label class="form-label">NIK (16 Digit)</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik', $siswa->nik) }}" maxlength="16" required>
                    </div>
                    <div>
                        <label class="form-label">NISN (10 Digit)</label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" maxlength="10" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Tanggal Lahir (Password akan sinkron otomatis jika diubah)</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ $siswa->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $siswa->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" class="form-control" value="{{ old('asal_sekolah', $siswa->asal_sekolah) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label class="form-label">Jalur Pendaftaran</label>
                        <select name="jalur_pendaftaran" class="form-select" required>
                            <option value="umum" {{ $siswa->jalur_pendaftaran === 'umum' ? 'selected' : '' }}>Umum</option>
                            <option value="prestasi" {{ $siswa->jalur_pendaftaran === 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Gelombang</label>
                        <select name="gelombang_id" class="form-select" required>
                            @foreach($allGelombangs as $g)
                                <option value="{{ $g->id }}" {{ $siswa->gelombang_id == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama_gelombang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="mb-3">
                    <div>
                        <label class="form-label">Nama Orang Tua / Wali</label>
                        <input type="text" name="nama_orang_tua" class="form-control" value="{{ old('nama_orang_tua', $siswa->nama_orang_tua) }}" required>
                    </div>
                    <div>
                        <label class="form-label">No. HP Orang Tua / Wali</label>
                        <input type="text" name="no_hp_orang_tua" class="form-control" value="{{ old('no_hp_orang_tua', $siswa->no_hp_orang_tua) }}" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $siswa->alamat) }}</textarea>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 8px 20px;">
                        Simpan Koreksi Data Siswa
                    </button>
                </div>
            </form>
        @else
            {{-- Tampilan Readonly untuk Admin Piket --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                <div style="background-color: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Nama Lengkap</div>
                    <div style="font-weight: 700; color: #092347; font-size: 1rem; margin-top: 2px;">{{ $siswa->nama_lengkap }}</div>
                    <div style="margin-top: 0.5rem; color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">NIK & NISN</div>
                    <div style="font-weight: 600; color: #334155; font-family: monospace;">{{ $siswa->nik }} / {{ $siswa->nisn }}</div>
                </div>

                <div style="background-color: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Konsentrasi Keahlian</div>
                    <div style="font-weight: 700; color: #0b57d0; font-size: 1rem; margin-top: 2px;">{{ $siswa->jurusan->nama_jurusan }} ({{ $siswa->jurusan->kode }})</div>
                    <div style="margin-top: 0.5rem; color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Jalur & Gelombang</div>
                    <div style="font-weight: 600; color: #334155;">{{ ucfirst($siswa->jalur_pendaftaran) }} &bull; {{ $siswa->gelombang->nama_gelombang ?? '-' }}</div>
                </div>

                <div style="background-color: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Tempat, Tanggal Lahir</div>
                    <div style="font-weight: 600; color: #092347; margin-top: 2px;">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }} ({{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }})</div>
                    <div style="margin-top: 0.5rem; color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Asal Sekolah</div>
                    <div style="font-weight: 600; color: #334155;">{{ $siswa->asal_sekolah }}</div>
                </div>

                <div style="background-color: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Orang Tua / Wali</div>
                    <div style="font-weight: 600; color: #092347; margin-top: 2px;">{{ $siswa->nama_orang_tua }} (HP: {{ $siswa->no_hp_orang_tua }})</div>
                    <div style="margin-top: 0.5rem; color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Alamat Tempat Tinggal</div>
                    <div style="color: #334155;">{{ $siswa->alamat }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bukaModalTolak(berkasId, namaBerkas) {
        var modal = document.getElementById('modalTolak');
        var form = document.getElementById('formTolakBerkas');
        var label = document.getElementById('modalBerkasNama');
        var catatanInput = document.getElementById('catatan_penolakan');

        form.action = "{{ url('/admin/pendaftar/berkas') }}/" + berkasId + "/verifikasi";
        label.textContent = namaBerkas;
        catatanInput.value = '';

        modal.style.display = 'flex';
        catatanInput.focus();
    }

    function tutupModalTolak() {
        document.getElementById('modalTolak').style.display = 'none';
    }
</script>
@endpush
