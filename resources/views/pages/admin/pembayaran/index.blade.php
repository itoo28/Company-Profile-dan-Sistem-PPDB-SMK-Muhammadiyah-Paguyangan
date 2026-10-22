@extends('layouts.admin')

@section('title', 'Verifikasi & Rekapitulasi Pembayaran PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Manajemen & Verifikasi Pembayaran PPDB')
@section('header-subtitle', 'Pencatatan kas tunai loket sekolah & verifikasi transfer pembayaran formulir pendaftaran')

@section('content')

{{-- KPI Ringkasan Keuangan --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <flux:card class="p-4 bg-white border border-slate-200">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Pendaftar</div>
        <div class="text-2xl font-extrabold text-slate-800">{{ number_format($totalPendaftar) }} Siswa</div>
        <div class="text-xs text-slate-400 mt-1">Tahun Ajaran {{ $tahunAjaran->nama ?? '-' }}</div>
    </flux:card>

    <flux:card class="p-4 bg-white border border-emerald-200 bg-emerald-50/20">
        <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">Pembayaran Lunas</div>
        <div class="text-2xl font-extrabold text-emerald-700">{{ number_format($totalLunas) }} Siswa</div>
        <div class="text-xs text-emerald-600/80 mt-1">Total: Rp {{ number_format($totalNominalMasuk, 0, ',', '.') }}</div>
    </flux:card>

    <flux:card class="p-4 bg-white border border-blue-200 bg-blue-50/20">
        <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Menunggu Verifikasi</div>
        <div class="text-2xl font-extrabold text-blue-700">{{ number_format($totalMenunggu) }} Transaksi</div>
        <div class="text-xs text-blue-600/80 mt-1">Perlu ditinjau bendahara</div>
    </flux:card>

    <flux:card class="p-4 bg-white border border-amber-200 bg-amber-50/20">
        <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-1">Belum Bayar / Ditolak</div>
        <div class="text-2xl font-extrabold text-amber-700">{{ number_format($totalBelumBayar + $totalDitolak) }} Siswa</div>
        <div class="text-xs text-amber-600/80 mt-1">{{ $totalBelumBayar }} belum bayar, {{ $totalDitolak }} ditolak</div>
    </flux:card>
</div>

{{-- Tombol Aksi & Filter Bar --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
    <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="admin-filter-bar admin-filter-form m-0 flex-1">
        <div class="admin-filter-group flex-wrap">
            <div class="search-input-wrapper" style="min-width: 220px;">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       class="form-control text-xs h-9" 
                       placeholder="Cari No. Pendaftaran, Nama, Kode...">
            </div>

            <div class="filter-select-wrapper">
                <select name="status" class="form-select text-xs h-9" onchange="this.form.submit()">
                    <option value="">Semua Status Bayar</option>
                    <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>⏳ Menunggu Verifikasi ({{ $totalMenunggu }})</option>
                    <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>✓ Lunas ({{ $totalLunas }})</option>
                    <option value="menunggu_pembayaran" {{ request('status') === 'menunggu_pembayaran' ? 'selected' : '' }}>⚠ Belum Dibayar ({{ $totalBelumBayar }})</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>✕ Ditolak ({{ $totalDitolak }})</option>
                </select>
            </div>

            <div class="filter-select-wrapper">
                <select name="gelombang_id" class="form-select text-xs h-9" onchange="this.form.submit()">
                    <option value="">Semua Gelombang</option>
                    @foreach($gelombangs as $g)
                        <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_gelombang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-select-wrapper">
                <select name="metode" class="form-select text-xs h-9" onchange="this.form.submit()">
                    <option value="">Semua Metode</option>
                    <option value="transfer" {{ request('metode') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="tunai" {{ request('metode') === 'tunai' ? 'selected' : '' }}>Tunai di Loket</option>
                </select>
            </div>

            <div class="admin-filter-actions">
                <flux:button type="submit" variant="primary" size="sm">Cari</flux:button>
                @if(request()->hasAny(['q', 'status', 'gelombang_id', 'metode']))
                    <flux:button href="{{ route('admin.pembayaran.index') }}" variant="subtle" size="sm">Reset</flux:button>
                @endif
            </div>
        </div>
    </form>

    <div>
        <button type="button" onclick="bukaModalTunai()" class="btn btn-primary btn-sm flex items-center gap-1.5 whitespace-nowrap" style="height: 36px; font-weight: 700;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>+ Catat Pembayaran Tunai (Loket)</span>
        </button>
    </div>
</div>

{{-- Tabel Data Pembayaran --}}
<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Transaksi Pembayaran Siswa ({{ $pembayaranList->total() }})</h3>
        <span class="text-xs text-slate-500">Biaya Standar PPDB: <strong>Rp {{ number_format($pengaturan->biaya_pendaftaran ?? 100000, 0, ',', '.') }}</strong></span>
    </div>

    <div class="admin-desktop-table">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Kode / Tanggal</th>
                        <th>Calon Siswa</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Bukti Transfer</th>
                        <th style="text-align: right; width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaranList as $index => $item)
                        @php
                            $siswa = $item->calonSiswa;
                        @endphp
                        <tr>
                            <td class="text-center font-bold text-slate-400">
                                {{ $pembayaranList->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $item->kode_transaksi }}</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $item->tanggal_bayar ? $item->tanggal_bayar->translatedFormat('d M Y') : $item->created_at->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td>
                                @if($siswa)
                                    <div class="font-bold text-slate-900 hover:text-blue-600">
                                        <a href="{{ route('admin.pendaftar.show', $siswa->id) }}">
                                            {{ $siswa->nama_lengkap }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $siswa->nomor_pendaftaran }} &bull; {{ $siswa->jurusan->kode ?? '-' }}</div>
                                @else
                                    <span class="text-slate-400 italic">Data siswa tidak ditemukan</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-extrabold text-slate-800 text-xs">
                                    {{ $item->formatted_nominal }}
                                </div>
                            </td>
                            <td>
                                @if($item->metode_pembayaran === 'tunai')
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        Tunai (Loket)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                        Transfer Bank
                                    </span>
                                    @if($item->bank_pengirim)
                                        <div class="text-[11px] text-slate-500 mt-0.5">a.n {{ $item->nama_pengirim }} ({{ $item->bank_pengirim }})</div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($item->isLunas())
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-1 rounded-full">
                                        ✓ Lunas
                                    </span>
                                    @if($item->diverifikasi_pada)
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $item->diverifikasi_pada->translatedFormat('d/m/y H:i') }}</div>
                                    @endif
                                @elseif($item->isMenungguVerifikasi())
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-800 bg-blue-100 px-2.5 py-1 rounded-full animate-pulse">
                                        ⏳ Menunggu Verifikasi
                                    </span>
                                @elseif($item->isDitolak())
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-800 bg-rose-100 px-2.5 py-1 rounded-full" title="{{ $item->catatan_admin }}">
                                        ✕ Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-800 bg-amber-100 px-2.5 py-1 rounded-full">
                                        ⚠ Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($item->bukti_transfer)
                                    <a href="{{ route('admin.pembayaran.bukti', $item->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 underline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <span>Lihat Struk</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Tidak ada file</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($item->isMenungguVerifikasi())
                                        <button type="button" 
                                                onclick="bukaModalVerifikasi({{ $item->id }}, '{{ addslashes($siswa->nama_lengkap ?? '') }}', '{{ $item->kode_transaksi }}', '{{ $item->formatted_nominal }}', '{{ addslashes($item->nama_pengirim ?? '') }}', '{{ addslashes($item->bank_pengirim ?? '') }}', '{{ route('admin.pembayaran.bukti', $item->id) }}')" 
                                                class="btn btn-primary btn-sm text-xs py-1 px-2.5">
                                            Verifikasi
                                        </button>
                                    @endif

                                    @if(!$item->isLunas())
                                        <button type="button" 
                                                onclick="bukaModalTunaiLangsung({{ $siswa->id ?? 0 }}, '{{ addslashes($siswa->nama_lengkap ?? '') }}', {{ $item->nominal }})" 
                                                class="btn btn-secondary btn-sm text-xs py-1 px-2" title="Terima Tunai di Loket">
                                            Terima Kas
                                        </button>
                                    @endif

                                    @if($item->isLunas())
                                        <a href="{{ route('admin.pembayaran.kuitansi', $item->id) }}" target="_blank" class="btn btn-secondary btn-sm text-xs py-1 px-2.5 flex items-center gap-1" title="Cetak Kuitansi Resmi">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                                <rect x="6" y="14" width="12" height="8"></rect>
                                            </svg>
                                            <span>Kuitansi</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Tidak ada data transaksi pembayaran yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pembayaranList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $pembayaranList->links() }}
        </div>
    @endif
</flux:card>

{{-- Modal Verifikasi Pembayaran Transfer --}}
<div id="modalVerifikasi" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="px-5 py-4 bg-[#092347] text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-sm m-0">Verifikasi Pembayaran Transfer</h3>
                <div class="text-xs text-blue-200" id="mvSub">Transaksi Siswa</div>
            </div>
            <button type="button" onclick="tutupModalVerifikasi()" class="text-white/70 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form id="formVerifikasi" method="POST" action="">
            @csrf
            <div class="p-5">
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 mb-4 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Siswa:</span>
                        <strong class="text-slate-800" id="mvNama">-</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nominal Transfer:</span>
                        <strong class="text-blue-700" id="mvNominal">-</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pengirim:</span>
                        <span class="text-slate-800" id="mvPengirim">-</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200">
                        <span class="text-slate-500">Berkas Bukti:</span>
                        <a id="mvLinkBukti" href="#" target="_blank" class="font-bold text-blue-600 hover:underline">
                            Buka File Struk di Tab Baru &rarr;
                        </a>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-xs font-bold text-slate-800 mb-1.5 block">Keputusan Verifikasi <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-emerald-50/50 border-slate-200" id="optLunas">
                            <input type="radio" name="status" value="lunas" required checked onchange="toggleCatatan(false)" class="text-emerald-600">
                            <div>
                                <div class="text-xs font-bold text-emerald-800">Setujui (Lunas)</div>
                                <div class="text-[10px] text-slate-500">Mutasi rekening valid & sesuai</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-rose-50/50 border-slate-200" id="optTolak">
                            <input type="radio" name="status" value="ditolak" required onchange="toggleCatatan(true)" class="text-rose-600">
                            <div>
                                <div class="text-xs font-bold text-rose-800">Tolak Pembayaran</div>
                                <div class="text-[10px] text-slate-500">Struk palsu/buram/kurang</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="catatanBox" class="mb-2 hidden">
                    <label class="form-label text-xs font-bold text-slate-800 mb-1 block">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="catatan_admin" id="catatanInput" rows="2" class="form-control text-xs" placeholder="Contoh: Nominal transfer kurang atau bukti tidak terbaca dengan jelas."></textarea>
                    <small class="text-[11px] text-slate-400">Catatan ini akan tampil di dashboard portal calon siswa.</small>
                </div>
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="tutupModalVerifikasi()" class="btn btn-secondary btn-sm text-xs">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm text-xs font-bold">Simpan Keputusan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Catat Pembayaran Tunai Loket --}}
<div id="modalTunai" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="px-5 py-4 bg-[#092347] text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-sm m-0">Catat Pembayaran Tunai (Loket PPDB)</h3>
                <div class="text-xs text-blue-200">Penerimaan kas langsung dari calon siswa / wali murid</div>
            </div>
            <button type="button" onclick="tutupModalTunai()" class="text-white/70 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.pembayaran.tunai') }}">
            @csrf
            <div class="p-5 space-y-3.5">
                <div>
                    <label class="form-label text-xs font-bold text-slate-800 mb-1 block">Pilih Calon Siswa <span class="text-rose-500">*</span></label>
                    <select name="calon_siswa_id" id="tunaiSiswaSelect" class="form-select text-xs w-full" required>
                        <option value="">-- Pilih Siswa Pendaftar --</option>
                        @foreach($siswaBelumLunas as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->nomor_pendaftaran }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs font-bold text-slate-800 mb-1 block">Tanggal Terima Kas <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_bayar" class="form-control text-xs" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="form-label text-xs font-bold text-slate-800 mb-1 block">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal" id="tunaiNominal" class="form-control text-xs" value="{{ $pengaturan->biaya_pendaftaran ?? 100000 }}" min="0" step="1000" required>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-slate-800 mb-1 block">Catatan Loket / No. Kuitansi Fisik</label>
                    <input type="text" name="catatan" class="form-control text-xs" placeholder="Contoh: Diterima lunas di loket kasir sekolah">
                </div>
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="tutupModalTunai()" class="btn btn-secondary btn-sm text-xs">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm text-xs font-bold">Simpan Penerimaan Kas Lunas</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalVerifikasi(id, nama, kode, nominal, pengirim, bank, linkBukti) {
    document.getElementById('formVerifikasi').action = "{{ url('admin/pembayaran') }}/" + id + "/verifikasi";
    document.getElementById('mvSub').innerText = "No. Transaksi: " + kode;
    document.getElementById('mvNama').innerText = nama;
    document.getElementById('mvNominal').innerText = nominal;
    document.getElementById('mvPengirim').innerText = pengirim + (bank ? " (" + bank + ")" : "");
    document.getElementById('mvLinkBukti').href = linkBukti;
    
    // Reset radio
    document.querySelector('input[name="status"][value="lunas"]').checked = true;
    toggleCatatan(false);
    
    document.getElementById('modalVerifikasi').classList.remove('hidden');
}

function tutupModalVerifikasi() {
    document.getElementById('modalVerifikasi').classList.add('hidden');
}

function toggleCatatan(isTolak) {
    const box = document.getElementById('catatanBox');
    const input = document.getElementById('catatanInput');
    if (isTolak) {
        box.classList.remove('hidden');
        input.setAttribute('required', 'required');
    } else {
        box.classList.add('hidden');
        input.removeAttribute('required');
    }
}

function bukaModalTunai() {
    document.getElementById('modalTunai').classList.remove('hidden');
}

function bukaModalTunaiLangsung(siswaId, nama, nominal) {
    const select = document.getElementById('tunaiSiswaSelect');
    if (siswaId) {
        // Cek apakah ada opsi siswaId
        let found = false;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value == siswaId) {
                select.selectedIndex = i;
                found = true;
                break;
            }
        }
        if (!found) {
            const opt = document.createElement('option');
            opt.value = siswaId;
            opt.innerText = nama;
            opt.selected = true;
            select.appendChild(opt);
        }
    }
    if (nominal) {
        document.getElementById('tunaiNominal').value = nominal;
    }
    document.getElementById('modalTunai').classList.remove('hidden');
}

function tutupModalTunai() {
    document.getElementById('modalTunai').classList.add('hidden');
}
</script>

@endsection
