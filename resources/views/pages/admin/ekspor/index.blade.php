@extends('layouts.admin')

@section('title', 'Ekspor & Cetak Laporan PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ekspor & Laporan PPDB')
@section('header-subtitle', 'Unduh berkas spreadsheet CSV atau cetak rekapitulasi pendaftar tahun ajaran aktif')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
    
    <!-- Opsi Ekspor CSV -->
    <flux:card class="p-6 flex flex-col justify-between">
        <div>
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 m-0">Ekspor Lembar Kerja (Format CSV)</h3>
            </div>
            <p class="text-slate-600 text-xs leading-relaxed mb-5">
                Unduh seluruh atribut pendaftar lengkap (termasuk NIK, NISN, status benefit promo SPP, dan status seragam) dalam format file <strong>.csv</strong> standar dengan encoding UTF-8 BOM yang langsung kompatibel dengan Microsoft Excel dan Google Sheets.
            </p>

            <form id="formEksporCsv" action="{{ route('admin.ekspor.csv') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control bg-slate-50 text-slate-600" value="{{ $tahunAjaran->tahun ?? '-' }} ({{ $tahunAjaran->nama_periode ?? 'Aktif' }})" readonly>
                </div>

                <div class="mb-3">
                    <label for="gelombang_id" class="form-label">Pilih Gelombang (Opsional)</label>
                    <select name="gelombang_id" id="gelombang_id" class="form-select">
                        <option value="">Semua Gelombang</option>
                        @foreach($gelombangs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gelombang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="jalur_pendaftaran" class="form-label">Pilih Jalur Pendaftaran (Opsional)</label>
                    <select name="jalur_pendaftaran" id="jalur_pendaftaran" class="form-select">
                        <option value="">Semua Jalur</option>
                        <option value="reguler">Jalur Reguler</option>
                        <option value="afirmasi">Jalur Afirmasi / KIP</option>
                        <option value="prestasi">Jalur Prestasi</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <flux:button type="submit" form="formEksporCsv" variant="primary" class="w-full" icon="arrow-down-tray">
                Unduh Data CSV
            </flux:button>
        </div>
    </flux:card>

    <!-- Opsi Cetak Laporan Format Dokumen -->
    <flux:card class="p-6 flex flex-col justify-between">
        <div>
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 m-0">Cetak Rekapitulasi Resmi (Format A4)</h3>
            </div>
            <p class="text-slate-600 text-xs leading-relaxed mb-5">
                Menampilkan laporan tabel pendaftar dengan kop surat resmi SMK Muhammadiyah Paguyangan yang telah dioptimalkan untuk cetak print A4 Landscape atau penyimpanan PDF resmi sekolah beserta kolom pengesahan tanda tangan.
            </p>

            <form id="formCetakA4" action="{{ route('admin.ekspor.cetak') }}" method="GET" target="_blank">
                <div class="mb-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control bg-slate-50 text-slate-600" value="{{ $tahunAjaran->tahun ?? '-' }}" readonly>
                </div>

                <div class="mb-5">
                    <label for="cetak_gelombang_id" class="form-label">Filter Gelombang</label>
                    <select name="gelombang_id" id="cetak_gelombang_id" class="form-select">
                        <option value="">Semua Gelombang</option>
                        @foreach($gelombangs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gelombang }}</option>
                        @endforeach
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
