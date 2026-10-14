@extends('layouts.admin')

@section('title', 'Master Persyaratan Berkas')
@section('header-title', 'Master Persyaratan Berkas Dokumen')
@section('header-subtitle', 'Atur daftar dokumen yang wajib diunggah per jalur (Semua Jalur, Khusus Prestasi, atau Opsional). Terhubung langsung ke formulir unggah portal siswa dan verifikasi loket piket.')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; align-items: start;">
    {{-- Form Tambah Jenis Berkas --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Tambah Persyaratan Berkas</h2>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.jenis-berkas.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="nama" class="form-label">Nama Dokumen Persyaratan <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Contoh: Ijazah / SKL SMP" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;" class="mb-3">
                    <div>
                        <label for="aturan_wajib" class="form-label">Aturan Kewajiban <span style="color: #ef4444;">*</span></label>
                        <select name="aturan_wajib" id="aturan_wajib" class="form-select" required>
                            <option value="semua">Wajib untuk Semua Jalur</option>
                            <option value="khusus_prestasi">Wajib Khusus Jalur Prestasi</option>
                            <option value="opsional">Opsional (Jika Ada / Keringanan)</option>
                        </select>
                    </div>

                    <div>
                        <label for="urutan" class="form-label">Nomor Urut Tampil <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="urutan" id="urutan" class="form-control" value="1" min="1" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="deskripsi" class="form-label">Petunjuk & Format File</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2" placeholder="Panduan scan/foto dokumen untuk calon siswa..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Simpan Persyaratan Berkas
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar Master Berkas --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Persyaratan Berkas PPDB</h2>
        </div>

        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">Urut</th>
                        <th>Nama Berkas</th>
                        <th>Kewajiban Jalur</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisBerkasList as $b)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $b->urutan }}</td>
                            <td>
                                <strong style="color: #092347;">{{ $b->nama }}</strong>
                                <div style="font-size: 0.725rem; color: #64748b; margin-top: 2px;">{{ $b->deskripsi }}</div>
                            </td>
                            <td>
                                @if($b->aturan_wajib === 'semua')
                                    <span class="badge-status badge-status-danger">Semua Jalur (Wajib)</span>
                                @elseif($b->aturan_wajib === 'khusus_prestasi')
                                    <span class="badge-status badge-status-warning">Khusus Prestasi (Wajib)</span>
                                @else
                                    <span class="badge-status" style="background-color: #f1f5f9; color: #64748b;">Opsional</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-status {{ $b->is_aktif ? 'badge-status-success' : '' }}" style="{{ !$b->is_aktif ? 'background-color: #f1f5f9; color: #64748b;' : '' }}">
                                    @if($b->is_aktif) <span class="badge-status-dot"></span> @endif
                                    {{ $b->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <form action="{{ route('admin.jenis-berkas.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis berkas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b; padding: 2rem;">
                                Belum ada master berkas terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
