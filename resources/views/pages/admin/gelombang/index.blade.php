@extends('layouts.admin')

@section('title', 'Manajemen Gelombang PPDB')
@section('header-title', 'Manajemen Gelombang PPDB')
@section('header-subtitle', 'Tahun Ajaran Aktif: ' . $tahunAjaran->nama . ' • Atur jadwal gelombang pendaftaran, status aktif, dan alokasi Bidikmisi.')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; align-items: start;">
    {{-- Form Tambah Gelombang --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Tambah Gelombang Baru</h2>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.gelombang.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="nama_gelombang" class="form-label">Nama Gelombang <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_gelombang" id="nama_gelombang" class="form-control" placeholder="Contoh: Gelombang 1 (Bidikmisi)" required>
                </div>

                <div class="form-group mb-3">
                    <label for="kode_gelombang" class="form-label">Kode Singkat</label>
                    <input type="text" name="kode_gelombang" id="kode_gelombang" class="form-control" placeholder="Contoh: G1, G2, G3">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;" class="mb-3">
                    <div>
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="keterangan" class="form-label">Keterangan / Alokasi</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Catatan khusus untuk gelombang ini..."></textarea>
                </div>

                <div class="mb-4">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600; color: #092347; cursor: pointer;">
                        <input type="checkbox" name="is_bidikmisi" value="1" style="width: 16px; height: 16px;">
                        <span>Tandai sebagai Gelombang Khusus Program Bidikmisi</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Simpan Gelombang Baru
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar Gelombang --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Gelombang PPDB</h2>
        </div>

        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Gelombang</th>
                        <th>Jadwal Pelaksanaan</th>
                        <th style="text-align: center;">Pendaftar</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gelombangs as $g)
                        <tr>
                            <td>
                                <strong style="color: #092347;">{{ $g->nama_gelombang }}</strong>
                                @if($g->is_bidikmisi)
                                    <div style="font-size: 0.7rem; color: #b45309; font-weight: 700; margin-top: 2px;">Alokasi Bidikmisi</div>
                                @endif
                            </td>
                            <td style="font-size: 0.775rem; color: #64748b; white-space: nowrap;">
                                {{ $g->tanggal_mulai->format('d/m/Y') }} s.d. {{ $g->tanggal_selesai->format('d/m/Y') }}
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #0b57d0;">
                                {{ $g->calon_siswa_count }}
                            </td>
                            <td style="text-align: center;">
                                @if($g->isSedangBerjalan())
                                    <span class="badge-status badge-status-success">
                                        <span class="badge-status-dot"></span>
                                        Aktif Berjalan
                                    </span>
                                @else
                                    <span class="badge-status {{ $g->is_aktif ? 'badge-status-info' : '' }}" style="{{ !$g->is_aktif ? 'background-color: #f1f5f9; color: #64748b;' : '' }}">
                                        @if($g->is_aktif) <span class="badge-status-dot"></span> @endif
                                        {{ $g->is_aktif ? 'Terjadwal' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <form action="{{ route('admin.gelombang.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gelombang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete" {{ $g->calon_siswa_count > 0 ? 'disabled title=Tidak_dapat_dihapus_karena_ada_pendaftar style=opacity:0.5;cursor:not-allowed;' : '' }}>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b; padding: 2rem;">
                                Belum ada gelombang pendaftaran yang terkonfigurasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
