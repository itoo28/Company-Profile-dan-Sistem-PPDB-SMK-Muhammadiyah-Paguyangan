@extends('layouts.admin')

@section('title', 'Ekspor & Cetak Laporan PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ekspor & Laporan PPDB')
@section('header-subtitle', 'Unduh berkas spreadsheet Excel atau cetak rekapitulasi pendaftar tahun ajaran aktif')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
    
    <!-- Opsi Ekspor Excel -->
    <flux:card class="p-6 flex flex-col justify-between">
        <div>
            <div class="mb-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 m-0">Ekspor Lembar Kerja Spreadsheet</h3>
                    <p class="text-xs text-slate-500 m-0 mt-0.5">Berkas Microsoft Excel (.xls) Otomatis Rapi & Lengkap</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Microsoft Excel (.xls)
                </span>
            </div>

            <p class="text-slate-600 text-xs leading-relaxed mb-5">
                Unduh rekapitulasi atribut pendaftar lengkap (termasuk NIK 16-digit, NISN, status benefit promo SPP, status verifikasi berkas, dan status seragam). Berkas Excel telah dioptimalkan otomatis dengan kop resmi, format angka aman tanpa notasi ilmiah, dan kolom yang rapi.
            </p>

            <form id="formEksporSpreadsheet" action="{{ route('admin.ekspor.excel') }}" method="GET">
                <div class="mb-3">
                    <label for="excel_tahun_ajaran_id" class="form-label text-xs font-semibold text-slate-700">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" id="excel_tahun_ajaran_id" class="form-select text-xs font-medium" onchange="window.location.href = '{{ route('admin.ekspor.index') }}?tahun_ajaran_id=' + this.value">
                        @foreach($semuaTahunAjaran as $ta)
                            <option value="{{ $ta->id }}" {{ $tahunAjaran && $tahunAjaran->id == $ta->id ? 'selected' : '' }}>
                                Periode {{ $ta->nama }} {{ $ta->is_aktif ? '✓ (Aktif)' : '(Arsip)' }}
                            </option>
                        @endforeach
                    </select>
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

                <div class="mb-5">
                    <label for="status_berkas" class="form-label text-xs font-semibold text-slate-700">Filter Status Berkas</label>
                    <select name="status_berkas" id="status_berkas" class="form-select text-xs">
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
            <flux:button type="submit" form="formEksporSpreadsheet" variant="primary" class="w-full" icon="table-cells">
                Unduh Berkas Excel (.xls)
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
                    <label for="cetak_tahun_ajaran_id" class="form-label text-xs font-semibold text-slate-700">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" id="cetak_tahun_ajaran_id" class="form-select text-xs font-medium" onchange="window.location.href = '{{ route('admin.ekspor.index') }}?tahun_ajaran_id=' + this.value">
                        @foreach($semuaTahunAjaran as $ta)
                            <option value="{{ $ta->id }}" {{ $tahunAjaran && $tahunAjaran->id == $ta->id ? 'selected' : '' }}>
                                Periode {{ $ta->nama }} {{ $ta->is_aktif ? '✓ (Aktif)' : '(Arsip)' }}
                            </option>
                        @endforeach
                    </select>
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

