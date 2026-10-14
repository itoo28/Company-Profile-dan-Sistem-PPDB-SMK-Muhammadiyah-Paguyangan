@extends('layouts.admin')

@section('title', 'Ekspor & Cetak Laporan PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ekspor & Laporan PPDB')
@section('header-subtitle', 'Unduh berkas spreadsheet CSV atau cetak rekapitulasi pendaftar tahun ajaran aktif')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; align-items: start;">
    
    <!-- Opsi Ekspor CSV -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Ekspor Lembar Kerja (Format CSV)</h3>
        </div>
        <div class="admin-card-body">
            <p style="color: #475569; font-size: 0.875rem; line-height: 1.5; margin-bottom: 20px;">
                Unduh seluruh atribut pendaftar lengkap (termasuk NIK, NISN, status benefit promo SPP, dan status seragam) dalam format file <strong>.csv</strong> standar dengan encoding UTF-8 BOM yang langsung kompatibel dengan Microsoft Excel dan Google Sheets.
            </p>

            <form action="{{ route('admin.ekspor.csv') }}" method="GET">
                <div class="form-group mb-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control" value="{{ $tahunAjaran->tahun ?? '-' }} ({{ $tahunAjaran->nama_periode ?? 'Aktif' }})" readonly style="background-color: #f8fafc; color: #475569;">
                </div>

                <div class="form-group mb-3">
                    <label for="gelombang_id" class="form-label">Pilih Gelombang (Opsional)</label>
                    <select name="gelombang_id" id="gelombang_id" class="form-select">
                        <option value="">Semua Gelombang</option>
                        @foreach($gelombangs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gelombang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="jalur_pendaftaran" class="form-label">Pilih Jalur Pendaftaran (Opsional)</label>
                    <select name="jalur_pendaftaran" id="jalur_pendaftaran" class="form-select">
                        <option value="">Semua Jalur</option>
                        <option value="reguler">Jalur Reguler</option>
                        <option value="afirmasi">Jalur Afirmasi / KIP</option>
                        <option value="prestasi">Jalur Prestasi</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Unduh Data CSV</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Opsi Cetak Laporan Format Dokumen -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Cetak Rekapitulasi Resmi (Format A4)</h3>
        </div>
        <div class="admin-card-body">
            <p style="color: #475569; font-size: 0.875rem; line-height: 1.5; margin-bottom: 20px;">
                Menampilkan laporan tabel pendaftar dengan kop surat resmi SMK Muhammadiyah Paguyangan yang telah dioptimalkan untuk cetak print A4 Landscape atau penyimpanan PDF resmi sekolah beserta kolom pengesahan tanda tangan.
            </p>

            <form action="{{ route('admin.ekspor.cetak') }}" method="GET" target="_blank">
                <div class="form-group mb-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control" value="{{ $tahunAjaran->tahun ?? '-' }}" readonly style="background-color: #f8fafc; color: #475569;">
                </div>

                <div class="form-group mb-3">
                    <label for="cetak_gelombang_id" class="form-label">Filter Gelombang</label>
                    <select name="gelombang_id" id="cetak_gelombang_id" class="form-select">
                        <option value="">Semua Gelombang</option>
                        @foreach($gelombangs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gelombang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4" style="visibility: hidden;">
                    <label class="form-label">&nbsp;</label>
                    <input type="text" class="form-control" readonly>
                </div>

                <button type="submit" class="btn btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    <span>Buka Lembar Cetak / PDF</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
