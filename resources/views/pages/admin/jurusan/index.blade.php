@extends('layouts.admin')

@section('title', 'Manajemen Jurusan Kejuruan')
@section('header-title', 'Manajemen Konsentrasi Keahlian / Jurusan')
@section('header-subtitle', 'Kelola konsentrasi keahlian vokasi, deskripsi kompetensi, daya tampung penerimaan, dan status aktif.')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; align-items: start;">
    {{-- Form Tambah Jurusan --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Tambah Konsentrasi Keahlian Baru</h2>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.jurusan.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;" class="mb-3">
                    <div>
                        <label for="kode" class="form-label">Kode Singkat <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control" placeholder="Contoh: TKR, RPL" required>
                    </div>

                    <div>
                        <label for="urutan" class="form-label">Urutan Tampil <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="urutan" id="urutan" class="form-control" value="1" min="1" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="nama_jurusan" class="form-label">Nama Lengkap Jurusan <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_jurusan" id="nama_jurusan" class="form-control" placeholder="Contoh: Teknik Kendaraan Ringan" required>
                </div>

                <div class="form-group mb-3">
                    <label for="bidang_keahlian" class="form-label">Bidang Keahlian <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="bidang_keahlian" id="bidang_keahlian" class="form-control" placeholder="Contoh: Teknologi Manufaktur dan Rekayasa" required>
                </div>

                <div class="form-group mb-3">
                    <label for="kuota" class="form-label">Daya Tampung Siswa (Kuota) <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="kuota" id="kuota" class="form-control" value="72" min="1" required>
                </div>

                <div class="form-group mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Kompetensi <span style="color: #ef4444;">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2" placeholder="Kompetensi kejuruan yang dipelajari siswa..." required></textarea>
                </div>

                <div class="form-group mb-4">
                    <label for="prospek_kerja" class="form-label">Prospek Karir & Dunia Kerja</label>
                    <textarea name="prospek_kerja" id="prospek_kerja" class="form-control" rows="2" placeholder="Peluang profesi lulusan di industri..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Simpan Konsentrasi Keahlian
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel Daftar Jurusan --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Jurusan Kejuruan Aktif</h2>
        </div>

        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Jurusan</th>
                        <th style="text-align: center;">Kuota</th>
                        <th style="text-align: center;">Pendaftar</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurusans as $j)
                        <tr>
                            <td>
                                <strong style="color: #0b57d0; font-family: monospace; font-size: 0.95rem;">{{ $j->kode }}</strong>
                            </td>
                            <td>
                                <strong style="color: #092347;">{{ $j->nama_jurusan }}</strong>
                                <div style="font-size: 0.725rem; color: #64748b; margin-top: 2px;">{{ $j->bidang_keahlian }}</div>
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ $j->kuota }}</td>
                            <td style="text-align: center; font-weight: 700; color: #0b57d0;">{{ $j->calon_siswa_count }}</td>
                            <td style="text-align: right;">
                                <form action="{{ route('admin.jurusan.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete" {{ $j->calon_siswa_count > 0 ? 'disabled title=Tidak_bisa_dihapus_karena_ada_pendaftar style=opacity:0.5;cursor:not-allowed;' : '' }}>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b; padding: 2rem;">
                                Belum ada jurusan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
