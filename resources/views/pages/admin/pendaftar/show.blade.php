@extends('layouts.admin')

@section('title', 'Detail & Verifikasi — ' . $siswa->nama_lengkap)
@section('header-title', $siswa->nama_lengkap . ' (' . $siswa->nomor_pendaftaran . ')')
@section('header-subtitle', 'Jalur ' . ucfirst($siswa->jalur_pendaftaran) . ' • ' . $siswa->jurusan->nama_jurusan . ' • Terdaftar: ' . $siswa->created_at->format('d/m/Y H:i'))

@section('header-action')
<div class="flex items-center gap-2 flex-wrap">
    <flux:button href="{{ route('admin.pendaftar.index') }}" variant="ghost" size="sm" icon="arrow-left">
        Kembali
    </flux:button>

    {{-- Tombol Unlock Lockdown (Super Admin Only - FR-17) --}}
    @if(auth('web')->user()->isSuperAdmin())
        <form action="{{ route('admin.pendaftar.unlock', $siswa->id) }}" method="POST" style="margin: 0;">
            @csrf
            <flux:button type="submit" variant="{{ $siswa->is_unlocked ? 'outline' : 'primary' }}" size="sm" icon="{{ $siswa->is_unlocked ? 'lock-closed' : 'lock-open' }}">
                {{ $siswa->is_unlocked ? 'Kunci Akses Upload' : 'Bypass Unlock Upload' }}
            </flux:button>
        </form>

        <form action="{{ route('admin.pendaftar.reset-password', $siswa->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset kata sandi akun {{ addslashes($siswa->nama_lengkap) }} kembali ke tanggal lahir default (format: DDMMYYYY)?');" style="margin: 0;">
            @csrf
            <flux:button type="submit" variant="outline" size="sm" icon="key">
                Reset Kata Sandi
            </flux:button>
        </form>
    @endif

    <flux:button href="{{ route('ppdb.bukti', $siswa->id) }}" target="_blank" variant="outline" size="sm" icon="printer">
        Cetak Bukti Fisik
    </flux:button>

    {{-- Tombol Hapus Pendaftar (Super Admin Only) --}}
    @if(auth('web')->user()->isSuperAdmin())
        <form action="{{ route('admin.pendaftar.destroy', $siswa->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus data calon siswa {{ addslashes($siswa->nama_lengkap) }} ({{ $siswa->nomor_pendaftaran }}) beserta seluruh berkas dokumennya? Tindakan ini tidak dapat dibatalkan.');" style="margin: 0;">
            @csrf
            @method('DELETE')
            <flux:button type="submit" variant="danger" size="sm" icon="trash">
                Hapus Pendaftar
            </flux:button>
        </form>
    @endif
</div>
@endsection

@section('content')
{{-- Banner Status Clearance Seragam, Kuota Benefit & Pembayaran --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <flux:card class="p-5">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Clearance Seragam (BR-05)</div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($siswa->pengambilanSeragam)
                <flux:badge color="emerald" size="sm">Sudah Diambil</flux:badge>
                <span class="text-xs text-slate-500">
                    pada {{ $siswa->pengambilanSeragam->waktu_ambil->format('d/m/Y H:i') }} (Oleh: {{ $siswa->pengambilanSeragam->petugas->name ?? 'Staf' }})
                </span>
            @elseif($isClearanceSeragam)
                <flux:badge color="emerald" size="sm">Memenuhi Syarat (Clearance Disetujui)</flux:badge>
                <span class="text-xs text-slate-600">Siswa berhak mengambil seragam di loket.</span>
            @else
                <flux:badge color="rose" size="sm">Belum Memenuhi Syarat</flux:badge>
                <span class="text-xs text-slate-500">Masih ada berkas wajib yang belum disetujui.</span>
            @endif
        </div>
    </flux:card>

    <flux:card class="p-5">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Benefit Bebas SPP 3 Bulan (FR-43)</div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($siswa->status_benefit === 'klaim_aktif')
                <flux:badge color="emerald" size="sm">Resmi Berhak (Aktif)</flux:badge>
                <span class="text-xs text-slate-600">Seluruh berkas lolos verifikasi Disetujui.</span>
            @elseif($siswa->status_benefit === 'kandidat_100_pertama')
                <flux:badge color="amber" size="sm">Kandidat 100 Pendaftar Pertama</flux:badge>
                <span class="text-xs text-slate-500">Menunggu verifikasi seluruh berkas wajib.</span>
            @else
                <flux:badge color="zinc" size="sm">Reguler</flux:badge>
                <span class="text-xs text-slate-500">Tidak masuk alokasi 100 pendaftar pertama.</span>
            @endif
        </div>
    </flux:card>

    <flux:card class="p-5">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Biaya Pendaftaran PPDB</div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($pembayaran->isLunas())
                <flux:badge color="emerald" size="sm">✓ Lunas ({{ $pembayaran->formatted_nominal }})</flux:badge>
                <a href="{{ route('admin.pembayaran.kuitansi', $pembayaran->id) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline">
                    Lihat Kuitansi &rarr;
                </a>
            @elseif($pembayaran->isMenungguVerifikasi())
                <flux:badge color="blue" size="sm">⏳ Menunggu Verifikasi</flux:badge>
                <a href="{{ route('admin.pembayaran.index', ['q' => $siswa->nomor_pendaftaran]) }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Periksa Bukti &rarr;
                </a>
            @elseif($pembayaran->isDitolak())
                <flux:badge color="rose" size="sm">✕ Ditolak</flux:badge>
                <span class="text-xs text-rose-600">{{ $pembayaran->catatan_admin }}</span>
            @else
                <flux:badge color="amber" size="sm">Belum Dibayar ({{ $pembayaran->formatted_nominal }})</flux:badge>
                <a href="{{ route('admin.pembayaran.index', ['q' => $siswa->nomor_pendaftaran]) }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Catat Tunai &rarr;
                </a>
            @endif
        </div>
    </flux:card>
</div>

{{-- Panel Verifikasi Berkas (FR-13 & BR-04) --}}
<flux:card class="p-0 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h2 class="text-sm font-bold text-slate-900 m-0">Pemeriksaan dan Verifikasi Berkas Dokumen (FR-13)</h2>
    </div>

    {{-- Mode Desktop: Tabel Verifikasi --}}
    <div class="admin-desktop-table">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nama Persyaratan</th>
                        <th>Sifat</th>
                        <th>Status Terkini</th>
                        <th>Pratinjau Dokumen</th>
                        <th>Verifikator Terakhir</th>
                        <th style="text-align: right; width: 220px;">Keputusan Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jenisBerkasList as $idx => $jenis)
                        @php
                            $isWajib = $jenis->isWajibUntukJalur($siswa->jalur_pendaftaran);
                            $berkas = $berkasMap->get($jenis->id);
                        @endphp
                        <tr>
                            <td class="text-slate-500 font-semibold">{{ $idx + 1 }}</td>
                            <td>
                                <strong class="text-slate-900">{{ $jenis->nama }}</strong>
                                @if($berkas && $berkas->status === 'ditolak' && $berkas->catatan_penolakan)
                                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-2 rounded text-xs mt-1.5">
                                        <strong>Alasan Penolakan:</strong> {{ $berkas->catatan_penolakan }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($isWajib)
                                    <flux:badge color="rose" size="sm">Wajib</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Opsional</flux:badge>
                                @endif
                            </td>
                            <td>
                                @if($berkas)
                                    @if($berkas->status === 'disetujui')
                                        <flux:badge color="emerald" size="sm">Disetujui</flux:badge>
                                    @elseif($berkas->status === 'menunggu')
                                        <flux:badge color="amber" size="sm">Menunggu</flux:badge>
                                    @elseif($berkas->status === 'ditolak')
                                        <flux:badge color="rose" size="sm">Ditolak</flux:badge>
                                    @endif
                                @else
                                    <flux:badge color="zinc" size="sm">Belum Unggah</flux:badge>
                                @endif
                            </td>
                            <td>
                                @if($berkas)
                                    <flux:button href="{{ route('admin.berkas.lihat', $berkas->id) }}" target="_blank" variant="subtle" size="xs" icon="eye">
                                        Buka File ({{ $berkas->file_size_kb }} KB)
                                    </flux:button>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-xs text-slate-500">
                                @if($berkas && $berkas->verifikator)
                                    <strong class="text-slate-800">{{ $berkas->verifikator->name }}</strong><br>
                                    <span>{{ $berkas->waktu_verifikasi?->format('d/m/Y H:i') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($berkas)
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($berkas->status === 'disetujui')
                                            <flux:button type="button" variant="subtle" size="xs" icon="check" disabled>
                                                Disetujui
                                            </flux:button>
                                        @else
                                            <form action="{{ route('admin.pendaftar.berkas.verifikasi', $berkas->id) }}" method="POST" class="inline-flex m-0 p-0 items-center">
                                                @csrf
                                                <input type="hidden" name="status" value="disetujui">
                                                <flux:button type="submit" variant="primary" size="xs" icon="check">
                                                    Setujui
                                                </flux:button>
                                            </form>
                                        @endif

                                        <flux:button type="button" variant="danger" size="xs" icon="x-mark" onclick="bukaModalTolak({{ $berkas->id }}, '{{ addslashes($jenis->nama) }}')">
                                            Tolak
                                        </flux:button>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">
                                        Menunggu unggahan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mode Mobile: Card View Verifikasi di Smartphone Android --}}
    <div class="admin-mobile-cards">
        @foreach($jenisBerkasList as $idx => $jenis)
            @php
                $isWajib = $jenis->isWajibUntukJalur($siswa->jalur_pendaftaran);
                $berkas = $berkasMap->get($jenis->id);
            @endphp
            <div class="admin-data-card">
                <div class="admin-data-card-header">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase">Persyaratan #{{ $idx + 1 }}</span>
                        <h4 class="font-bold text-slate-900 text-sm m-0 mt-0.5">{{ $jenis->nama }}</h4>
                    </div>
                </div>

                <div class="admin-data-card-badges">
                    @if($isWajib)
                        <flux:badge color="rose" size="sm">Wajib</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">Opsional</flux:badge>
                    @endif

                    @if($berkas)
                        @if($berkas->status === 'disetujui')
                            <flux:badge color="emerald" size="sm">Disetujui</flux:badge>
                        @elseif($berkas->status === 'menunggu')
                            <flux:badge color="amber" size="sm">Menunggu Verifikasi</flux:badge>
                        @elseif($berkas->status === 'ditolak')
                            <flux:badge color="rose" size="sm">Ditolak</flux:badge>
                        @endif
                    @else
                        <flux:badge color="zinc" size="sm">Belum Unggah</flux:badge>
                    @endif
                </div>

                @if($berkas && $berkas->status === 'ditolak' && $berkas->catatan_penolakan)
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-2.5 rounded text-xs mb-2">
                        <strong>Alasan Penolakan:</strong> {{ $berkas->catatan_penolakan }}
                    </div>
                @endif

                @if($berkas)
                    <div class="bg-slate-50 p-2.5 rounded flex items-center justify-between gap-2 text-xs mb-2">
                        <div>
                            <span class="text-slate-700 font-semibold">File Dokumen:</span>
                            <div class="text-[11px] text-slate-500">Ukuran: {{ $berkas->file_size_kb }} KB</div>
                        </div>
                        <flux:button href="{{ route('admin.berkas.lihat', $berkas->id) }}" target="_blank" variant="subtle" size="xs" icon="eye">
                            Buka File
                        </flux:button>
                    </div>
                @endif

                @if($berkas && $berkas->verifikator)
                    <div class="text-[11px] text-slate-500 mb-2">
                        Diverifikasi oleh: <strong>{{ $berkas->verifikator->name }}</strong> ({{ $berkas->waktu_verifikasi?->format('d/m/Y H:i') }})
                    </div>
                @endif

                <div class="admin-data-card-footer">
                    @if($berkas)
                        <div class="flex items-center gap-2 w-full">
                            @if($berkas->status === 'disetujui')
                                <flux:button type="button" variant="subtle" class="flex-1" size="sm" icon="check" disabled>
                                    Sudah Disetujui
                                </flux:button>
                            @else
                                <form action="{{ route('admin.pendaftar.berkas.verifikasi', $berkas->id) }}" method="POST" class="flex-1 m-0">
                                    @csrf
                                    <input type="hidden" name="status" value="disetujui">
                                    <flux:button type="submit" variant="primary" class="w-full" size="sm" icon="check">
                                        Setujui Berkas
                                    </flux:button>
                                </form>
                            @endif

                            <flux:button type="button" variant="danger" class="flex-1" size="sm" icon="x-mark" onclick="bukaModalTolak({{ $berkas->id }}, '{{ addslashes($jenis->nama) }}')">
                                Tolak Berkas
                            </flux:button>
                        </div>
                    @else
                        <span class="text-xs text-slate-400 italic text-center w-full py-1">
                            Menunggu calon siswa mengunggah berkas
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</flux:card>

{{-- Modal Penolakan Berkas --}}
<div id="modalTolak" style="display: none;" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-4 sm:p-6 text-left shadow-2xl border border-slate-200">
        <div class="flex justify-between items-start gap-2 mb-2">
            <h3 class="text-base font-bold text-rose-600 m-0">
                Tolak Berkas Persyaratan
            </h3>
            <button type="button" onclick="tutupModalTolak()" class="text-slate-400 hover:text-slate-600 p-1 text-lg leading-none cursor-pointer" aria-label="Tutup">&times;</button>
        </div>
        <p class="text-xs text-slate-600 m-0 mb-4 leading-relaxed">
            Berkas: <strong id="modalBerkasNama" class="text-slate-900">-</strong>. Sesuai aturan bisnis <strong>BR-04</strong>, penolakan berkas <strong>wajib disertai catatan alasan/perbaikan</strong> agar pendaftar dapat memperbaikinya.
        </p>

        <form id="formTolakBerkas" method="POST" action="">
            @csrf
            <input type="hidden" name="status" value="ditolak">
            
            <div class="mb-4">
                <label for="catatan_penolakan" class="form-label">Catatan Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="catatan_penolakan" id="catatan_penolakan" class="form-control" rows="3" placeholder="Contoh: Foto dokumen buram dan terpotong. Harap scan ulang secara utuh." required></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" class="w-full sm:w-auto" size="sm" onclick="tutupModalTolak()">Batal</flux:button>
                <flux:button type="submit" variant="danger" class="w-full sm:w-auto" size="sm">Simpan Penolakan</flux:button>
            </div>
        </form>
    </div>
</div>

{{-- Biodata Lengkap Calon Siswa & Form Koreksi Super Admin (FR-29) --}}
<flux:card class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-base font-bold text-slate-900 m-0">Biodata Lengkap Calon Siswa</h2>
        @if(auth('web')->user()->isSuperAdmin())
            <flux:badge color="blue" size="sm">Mode Koreksi Super Admin (FR-29)</flux:badge>
        @endif
    </div>

    @if(auth('web')->user()->isSuperAdmin())
        {{-- Form Koreksi Data Siswa Super Admin --}}
        <form action="{{ route('admin.pendaftar.koreksi', $siswa->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">NIK (16 Digit)</label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik', $siswa->nik) }}" maxlength="16" required>
                </div>
                <div>
                    <label class="form-label">NISN (10 Digit)</label>
                    <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" maxlength="10" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" required>
                </div>
                <div>
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_orang_tua" class="form-control" value="{{ old('nama_orang_tua', $siswa->nama_orang_tua) }}" required>
                </div>
                <div>
                    <label class="form-label">No. HP Orang Tua / Wali</label>
                    <input type="text" name="no_hp_orang_tua" class="form-control" value="{{ old('no_hp_orang_tua', $siswa->no_hp_orang_tua) }}" required>
                </div>
            </div>

            <div class="form-group mb-5">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>

            <div style="text-align: right;">
                <flux:button type="submit" variant="primary">
                    Simpan Koreksi Data Siswa
                </flux:button>
            </div>
        </form>
    @else
        {{-- Tampilan Readonly untuk Admin Piket --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Nama Lengkap</div>
                <div class="font-bold text-slate-900 text-base mt-1">{{ $siswa->nama_lengkap }}</div>
                <div class="mt-3 text-xs text-slate-500 uppercase font-bold tracking-wider">NIK & NISN</div>
                <div class="font-semibold text-slate-700 font-mono text-xs mt-0.5">{{ $siswa->nik }} / {{ $siswa->nisn }}</div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Konsentrasi Keahlian</div>
                <div class="font-bold text-blue-600 text-base mt-1">{{ $siswa->jurusan->nama_jurusan }} ({{ $siswa->jurusan->kode }})</div>
                <div class="mt-3 text-xs text-slate-500 uppercase font-bold tracking-wider">Jalur & Gelombang</div>
                <div class="font-semibold text-slate-700 text-xs mt-0.5">{{ ucfirst($siswa->jalur_pendaftaran) }} &bull; {{ $siswa->gelombang->nama_gelombang ?? '-' }}</div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Tempat, Tanggal Lahir</div>
                <div class="font-semibold text-slate-900 mt-1">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }} ({{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }})</div>
                <div class="mt-3 text-xs text-slate-500 uppercase font-bold tracking-wider">Asal Sekolah</div>
                <div class="font-semibold text-slate-700 text-xs mt-0.5">{{ $siswa->asal_sekolah }}</div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Orang Tua / Wali</div>
                <div class="font-semibold text-slate-900 mt-1">{{ $siswa->nama_orang_tua }}</div>
                <div class="text-xs text-slate-600 mt-0.5">HP: {{ $siswa->no_hp_orang_tua }}</div>
                <div class="mt-3 text-xs text-slate-500 uppercase font-bold tracking-wider">Alamat</div>
                <div class="text-xs text-slate-700 mt-0.5">{{ $siswa->alamat }}</div>
            </div>
        </div>
    @endif
</flux:card>
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
