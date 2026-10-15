@extends('layouts.admin')

@section('title', 'Ekspor & Cetak Laporan PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ekspor & Laporan PPDB')
@section('header-subtitle', 'Unduh berkas spreadsheet CSV atau cetak rekapitulasi pendaftar tahun ajaran aktif')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
    
    <!-- Opsi Ekspor CSV & Excel -->
    <flux:card class="p-6 flex flex-col justify-between">
        <div>
            <div class="mb-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 m-0">Ekspor Lembar Kerja Spreadsheet</h3>
                    <p class="text-xs text-slate-500 m-0 mt-0.5">Format CSV Rapi & Terstruktur atau Berkas Excel (.xls)</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    UTF-8 BOM
                </span>
            </div>

            <p class="text-slate-600 text-xs leading-relaxed mb-5">
                Unduh seluruh atribut pendaftar lengkap (termasuk NIK 16-digit, NISN, status benefit promo SPP, status verifikasi berkas, dan status seragam). Format telah dioptimalkan agar kolom terpisah rapi tanpa teks terpotong atau format berantakan di Microsoft Excel maupun Google Sheets.
            </p>

            <form id="formEksporSpreadsheet" action="{{ route('admin.ekspor.csv') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label text-xs font-semibold text-slate-700">Tahun Ajaran</label>
                    <input type="text" class="form-control bg-slate-50 text-slate-600 text-xs" value="{{ $tahunAjaran->tahun ?? '-' }} ({{ $tahunAjaran->nama_periode ?? 'Aktif' }})" readonly>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="gelombang_id" class="form-label text-xs font-semibold text-slate-700">Filter Gelombang</label>
                        <select name="gelombang_id" id="gelombang_id" class="form-select text-xs">
                            <option value="">Semua Gelombang</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_gelombang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="jalur_pendaftaran" class="form-label text-xs font-semibold text-slate-700">Filter Jalur Pendaftaran</label>
                        <select name="jalur_pendaftaran" id="jalur_pendaftaran" class="form-select text-xs">
                            <option value="">Semua Jalur</option>
                            <option value="umum">Jalur Umum / Reguler</option>
                            <option value="prestasi">Jalur Prestasi</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status_berkas" class="form-label text-xs font-semibold text-slate-700">Filter Status Berkas</label>
                    <select name="status_berkas" id="status_berkas" class="form-select text-xs">
                        <option value="">Semua Status Berkas</option>
                        <option value="disetujui">Lengkap & Disetujui</option>
                        <option value="menunggu">Menunggu Verifikasi</option>
                        <option value="ditolak">Ada Berkas Ditolak</option>
                        <option value="belum_lengkap">Belum Lengkap</option>
                    </select>
                </div>

                <!-- Pengaturan Format CSV -->
                <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200 mb-5">
                    <div class="text-xs font-bold text-slate-800 mb-2.5 flex items-center gap-1.5">
                        <flux:icon name="cog-6-tooth" class="size-3.5 text-slate-500" />
                        Pengaturan Format Kolom & Angka CSV
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="delimiter" class="form-label text-[11px] font-semibold text-slate-600">Pemisah Kolom (Delimiter)</label>
                            <select name="delimiter" id="delimiter" class="form-select text-xs bg-white">
                                <option value="semicolon" selected>Titik Koma (;) — Standar Excel Indonesia</option>
                                <option value="comma">Koma (,) — Google Sheets / Internasional</option>
                                <option value="tab">Tab (\t) — Format TSV</option>
                            </select>
                            <span class="text-[10px] text-slate-500 block mt-1">Gunakan Titik Koma (;) agar langsung terpisah kolom saat dibuka di Windows Excel.</span>
                        </div>

                        <div>
                            <label for="format_angka" class="form-label text-[11px] font-semibold text-slate-600">Format Angka NIK & HP</label>
                            <select name="format_angka" id="format_angka" class="form-select text-xs bg-white">
                                <option value="excel_formula" selected>Formula Teks (='...') — Aman 16-Digit</option>
                                <option value="kutip">Kutip (')</option>
                                <option value="raw">Angka Polos (Import Dapodik / DB)</option>
                            </select>
                            <span class="text-[10px] text-slate-500 block mt-1">Formula teks mencegah NIK 16 digit berubah menjadi scientific (3.3E+15).</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-2.5">
            <flux:button type="submit" form="formEksporSpreadsheet" variant="primary" class="w-full sm:flex-1" icon="arrow-down-tray">
                Unduh Data CSV (Rapi)
            </flux:button>
            <flux:button type="submit" form="formEksporSpreadsheet" formaction="{{ route('admin.ekspor.excel') }}" variant="outline" class="w-full sm:flex-1 border-emerald-300 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-400" icon="table-cells">
                Unduh Excel (.xls)
            </flux:button>
        </div>
    </flux:card>

    <!-- Opsi Cetak Laporan Format Dokumen -->
    <flux:card class="p-6 flex flex-col justify-between">
        <div>
            <div class="mb-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 m-0">Cetak Rekapitulasi Resmi (Format A4)</h3>
                    <p class="text-xs text-slate-500 m-0 mt-0.5">Dokumen Cetak Fisik atau Ekspor PDF Berkop Sekolah</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    A4 Landscape
                </span>
            </div>

            <p class="text-slate-600 text-xs leading-relaxed mb-5">
                Menampilkan laporan tabel pendaftar dengan kop surat resmi SMK Muhammadiyah Paguyangan yang telah dioptimalkan untuk cetak print A4 Landscape atau penyimpanan PDF resmi sekolah beserta kolom pengesahan tanda tangan Kepala Sekolah dan Ketua Panitia PPDB.
            </p>

            <form id="formCetakA4" action="{{ route('admin.ekspor.cetak') }}" method="GET" target="_blank">
                <div class="mb-3">
                    <label class="form-label text-xs font-semibold text-slate-700">Tahun Ajaran</label>
                    <input type="text" class="form-control bg-slate-50 text-slate-600 text-xs" value="{{ $tahunAjaran->tahun ?? '-' }}" readonly>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="cetak_gelombang_id" class="form-label text-xs font-semibold text-slate-700">Filter Gelombang</label>
                        <select name="gelombang_id" id="cetak_gelombang_id" class="form-select text-xs">
                            <option value="">Semua Gelombang</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_gelombang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="cetak_jalur_pendaftaran" class="form-label text-xs font-semibold text-slate-700">Filter Jalur Pendaftaran</label>
                        <select name="jalur_pendaftaran" id="cetak_jalur_pendaftaran" class="form-select text-xs">
                            <option value="">Semua Jalur</option>
                            <option value="umum">Jalur Umum / Reguler</option>
                            <option value="prestasi">Jalur Prestasi</option>
                        </select>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="cetak_status_berkas" class="form-label text-xs font-semibold text-slate-700">Filter Status Berkas</label>
                    <select name="status_berkas" id="cetak_status_berkas" class="form-select text-xs">
                        <option value="">Semua Status Berkas</option>
                        <option value="disetujui">Lengkap & Disetujui</option>
                        <option value="menunggu">Menunggu Verifikasi</option>
                        <option value="ditolak">Ada Berkas Ditolak</option>
                        <option value="belum_lengkap">Belum Lengkap</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <flux:button type="submit" form="formCetakA4" variant="outline" class="w-full" icon="printer">
                Buka Lembar Cetak / PDF
            </flux:button>
        </div>
    </flux:card>
</div>
@endsection

