@extends('layouts.admin')

@section('title', 'Manajemen Gelombang PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Manajemen Gelombang PPDB')
@section('header-subtitle', 'Tahun Ajaran Aktif: ' . $tahunAjaran->nama . ' • Atur jadwal gelombang pendaftaran, status aktif, dan alokasi Bidikmisi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    {{-- Form Tambah Gelombang --}}
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 m-0">Tambah Gelombang Baru</h2>
        </div>
        <form action="{{ route('admin.gelombang.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="nama_gelombang" class="form-label">Nama Gelombang <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_gelombang" id="nama_gelombang" class="form-control" placeholder="Contoh: Gelombang 1 (Bidikmisi)" required>
            </div>

            <div class="mb-4">
                <label for="kode_gelombang" class="form-label">Kode Singkat</label>
                <input type="text" name="kode_gelombang" id="kode_gelombang" class="form-control" placeholder="Contoh: G1, G2, G3">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                </div>

                <div>
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="form-label">Keterangan / Alokasi</label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Catatan khusus untuk gelombang ini..."></textarea>
            </div>

            <div class="mb-5">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                    <input type="checkbox" name="is_bidikmisi" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                    <span>Tandai sebagai Gelombang Khusus Program Bidikmisi</span>
                </label>
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Simpan Gelombang Baru
            </flux:button>
        </form>
    </flux:card>

    {{-- Daftar Gelombang --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Gelombang PPDB</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table w-full">
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
                                <strong class="text-slate-900">{{ $g->nama_gelombang }}</strong>
                                @if($g->is_bidikmisi)
                                    <div class="mt-1">
                                        <flux:badge color="amber" size="sm">Alokasi Bidikmisi</flux:badge>
                                    </div>
                                @endif
                            </td>
                            <td class="text-xs text-slate-500 whitespace-nowrap">
                                {{ $g->tanggal_mulai->format('d/m/Y') }} s.d. {{ $g->tanggal_selesai->format('d/m/Y') }}
                            </td>
                            <td style="text-align: center;" class="font-bold text-blue-600">
                                {{ $g->calon_siswa_count }}
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                @if($g->isSedangBerjalan())
                                    <flux:badge color="emerald" size="sm">Aktif Berjalan</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">
                                        {{ $g->is_aktif ? 'Terjadwal' : 'Nonaktif' }}
                                    </flux:badge>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($g->calon_siswa_count > 0)
                                    <flux:button type="button" variant="subtle" size="xs" icon="trash" disabled title="Tidak dapat dihapus karena ada pendaftar">
                                        Hapus
                                    </flux:button>
                                @else
                                    <form action="{{ route('admin.gelombang.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gelombang ini?')" class="inline-flex m-0 p-0 items-center">
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
                                Belum ada gelombang pendaftaran yang terkonfigurasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>
</div>
@endsection
