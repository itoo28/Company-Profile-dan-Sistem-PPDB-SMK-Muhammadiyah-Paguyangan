@extends('layouts.admin')

@section('title', 'Manajemen Jurusan Kejuruan')
@section('header-title', 'Manajemen Konsentrasi Keahlian / Jurusan')
@section('header-subtitle', 'Kelola konsentrasi keahlian vokasi, deskripsi kompetensi, daya tampung penerimaan, dan status aktif.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    {{-- Form Tambah Jurusan --}}
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 m-0">Tambah Konsentrasi Keahlian Baru</h2>
        </div>
        <form action="{{ route('admin.jurusan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="kode" class="form-label">Kode Singkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode" id="kode" class="form-control" placeholder="Contoh: TKR, RPL" required>
                </div>

                <div>
                    <label for="urutan" class="form-label">Urutan Tampil <span class="text-rose-500">*</span></label>
                    <input type="number" name="urutan" id="urutan" class="form-control" value="1" min="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="nama_jurusan" class="form-label">Nama Lengkap Jurusan <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_jurusan" id="nama_jurusan" class="form-control" placeholder="Contoh: Teknik Kendaraan Ringan" required>
            </div>

            <div class="mb-3">
                <label for="bidang_keahlian" class="form-label">Bidang Keahlian <span class="text-rose-500">*</span></label>
                <input type="text" name="bidang_keahlian" id="bidang_keahlian" class="form-control" placeholder="Contoh: Teknologi Manufaktur dan Rekayasa" required>
            </div>

            <div class="mb-3">
                <label for="kuota" class="form-label">Daya Tampung Siswa (Kuota) <span class="text-rose-500">*</span></label>
                <input type="number" name="kuota" id="kuota" class="form-control" value="72" min="1" required>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Kompetensi <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2" placeholder="Kompetensi kejuruan yang dipelajari siswa..." required></textarea>
            </div>

            <div class="mb-4">
                <label for="prospek_kerja" class="form-label">Prospek Karir & Dunia Kerja</label>
                <textarea name="prospek_kerja" id="prospek_kerja" class="form-control" rows="2" placeholder="Peluang profesi lulusan di industri..."></textarea>
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Simpan Konsentrasi Keahlian
            </flux:button>
        </form>
    </flux:card>

    {{-- Tabel Daftar Jurusan --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Jurusan Kejuruan Aktif</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 80px;">Kode</th>
                        <th>Nama Jurusan</th>
                        <th style="text-align: center; width: 80px;">Kuota</th>
                        <th style="text-align: center; width: 100px;">Pendaftar</th>
                        <th style="text-align: right; width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurusans as $j)
                        <tr>
                            <td>
                                <strong class="text-blue-600 font-mono text-xs">{{ $j->kode }}</strong>
                            </td>
                            <td>
                                <strong class="text-slate-900">{{ $j->nama_jurusan }}</strong>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $j->bidang_keahlian }}</div>
                            </td>
                            <td style="text-align: center;" class="font-semibold text-slate-700">{{ $j->kuota }}</td>
                            <td style="text-align: center;">
                                <flux:badge color="blue" size="sm">{{ $j->calon_siswa_count }}</flux:badge>
                            </td>
                            <td style="text-align: right;">
                                @if($j->calon_siswa_count > 0)
                                    <flux:button type="button" variant="subtle" size="xs" icon="trash" disabled title="Tidak bisa dihapus karena ada pendaftar">
                                        Hapus
                                    </flux:button>
                                @else
                                    <form action="{{ route('admin.jurusan.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')" class="inline-flex m-0 p-0 items-center">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="danger" size="xs" icon="trash">
                                            Hapus
                                        </flux:button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate-500 py-8">
                                Belum ada jurusan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>
</div>
@endsection
