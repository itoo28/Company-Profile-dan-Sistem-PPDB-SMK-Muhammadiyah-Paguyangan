@extends('layouts.admin')

@section('title', 'Master Persyaratan Berkas')
@section('header-title', 'Master Persyaratan Berkas Dokumen')
@section('header-subtitle', 'Atur daftar dokumen yang wajib diunggah per jalur (Semua Jalur, Khusus Prestasi, atau Opsional). Terhubung langsung ke formulir unggah portal siswa dan verifikasi loket piket.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    {{-- Form Tambah Jenis Berkas --}}
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 m-0">Tambah Persyaratan Berkas</h2>
        </div>
        <form action="{{ route('admin.jenis-berkas.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Dokumen Persyaratan <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control" placeholder="Contoh: Ijazah / SKL SMP" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="aturan_wajib" class="form-label">Aturan Kewajiban <span class="text-rose-500">*</span></label>
                    <select name="aturan_wajib" id="aturan_wajib" class="form-select" required>
                        <option value="semua">Wajib untuk Semua Jalur</option>
                        <option value="khusus_prestasi">Wajib Khusus Jalur Prestasi</option>
                        <option value="opsional">Opsional (Jika Ada)</option>
                    </select>
                </div>

                <div>
                    <label for="urutan" class="form-label">Nomor Urut Tampil <span class="text-rose-500">*</span></label>
                    <input type="number" name="urutan" id="urutan" class="form-control" value="1" min="1" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="form-label">Petunjuk & Format File</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2" placeholder="Panduan scan/foto dokumen untuk calon siswa..."></textarea>
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Simpan Persyaratan Berkas
            </flux:button>
        </form>
    </flux:card>

    {{-- Daftar Master Berkas --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Persyaratan Berkas PPDB ({{ $jenisBerkasList->count() }})</h2>
        </div>

        <div class="admin-desktop-table overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">Urut</th>
                        <th>Nama Berkas</th>
                        <th>Kewajiban Jalur</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisBerkasList as $b)
                        <tr>
                            <td style="text-align: center;" class="font-bold text-slate-500">{{ $b->urutan }}</td>
                            <td>
                                <strong class="text-slate-900">{{ $b->nama }}</strong>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $b->deskripsi }}</div>
                            </td>
                            <td>
                                @if($b->aturan_wajib === 'semua')
                                    <flux:badge color="rose" size="sm">Semua Jalur (Wajib)</flux:badge>
                                @elseif($b->aturan_wajib === 'khusus_prestasi')
                                    <flux:badge color="amber" size="sm">Khusus Prestasi (Wajib)</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Opsional</flux:badge>
                                @endif
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <flux:badge color="{{ $b->is_aktif ? 'emerald' : 'zinc' }}" size="sm">
                                    {{ $b->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </flux:badge>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div class="flex items-center justify-end gap-1.5">
                                    <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editBerkasModal-{{ $b->id }}').style.display='flex'">
                                        Edit
                                    </flux:button>
                                    <form action="{{ route('admin.jenis-berkas.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis berkas ini?')" class="inline-flex m-0 p-0 items-center">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="danger" size="xs" icon="trash">
                                            Hapus
                                        </flux:button>
                                    </form>
                                </div>

                                <!-- Modal Edit Persyaratan Berkas -->
                                <div id="editBerkasModal-{{ $b->id }}" style="display: none;" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-4">
                                    <div class="bg-white rounded-xl max-w-lg w-full p-6 text-left shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                                            <h3 class="text-base font-bold text-slate-900 m-0">Edit Persyaratan Berkas</h3>
                                            <button type="button" onclick="document.getElementById('editBerkasModal-{{ $b->id }}').style.display='none'" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 leading-none">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.jenis-berkas.update', $b->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-3">
                                                <label class="form-label">Nama Dokumen Persyaratan <span class="text-rose-500">*</span></label>
                                                <input type="text" name="nama" class="form-control" value="{{ $b->nama }}" required>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                <div>
                                                    <label class="form-label">Aturan Kewajiban <span class="text-rose-500">*</span></label>
                                                    <select name="aturan_wajib" class="form-select" required>
                                                        <option value="semua" {{ $b->aturan_wajib === 'semua' ? 'selected' : '' }}>Wajib untuk Semua Jalur</option>
                                                        <option value="khusus_prestasi" {{ $b->aturan_wajib === 'khusus_prestasi' ? 'selected' : '' }}>Wajib Khusus Jalur Prestasi</option>
                                                        <option value="opsional" {{ $b->aturan_wajib === 'opsional' ? 'selected' : '' }}>Opsional (Jika Ada)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="form-label">Nomor Urut Tampil <span class="text-rose-500">*</span></label>
                                                    <input type="number" name="urutan" class="form-control" value="{{ $b->urutan }}" min="1" required>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label">Petunjuk & Format File</label>
                                                <textarea name="deskripsi" class="form-control" rows="2">{{ $b->deskripsi }}</textarea>
                                            </div>

                                            <div class="mb-5">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                                                    <input type="checkbox" name="is_aktif" value="1" {{ $b->is_aktif ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                                                    <span>Status Berkas Aktif (Wajib diisi pada portal siswa)</span>
                                                </label>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                                <flux:button type="button" variant="ghost" size="sm" onclick="document.getElementById('editBerkasModal-{{ $b->id }}').style.display='none'">
                                                    Batal
                                                </flux:button>
                                                <flux:button type="submit" variant="primary" size="sm">
                                                    Simpan Perubahan
                                                </flux:button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate-500 py-8">
                                Belum ada master berkas terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tampilan Kartu Mobile Android -->
        <div class="admin-mobile-cards p-3 flex flex-col gap-3">
            @forelse($jenisBerkasList as $b)
                <div class="admin-data-card p-3.5 bg-white rounded-lg border border-slate-200 shadow-xs">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="text-xs text-slate-400 font-mono">Urut #{{ $b->urutan }}</span>
                            <strong class="text-sm text-slate-900 block mt-0.5">{{ $b->nama }}</strong>
                            <div class="text-xs text-slate-500">{{ $b->deskripsi }}</div>
                        </div>
                        <flux:badge color="{{ $b->is_aktif ? 'emerald' : 'zinc' }}" size="sm">
                            {{ $b->is_aktif ? 'Aktif' : 'Nonaktif' }}
                        </flux:badge>
                    </div>

                    <div class="py-2 border-t border-slate-100">
                        @if($b->aturan_wajib === 'semua')
                            <flux:badge color="rose" size="sm">Semua Jalur (Wajib)</flux:badge>
                        @elseif($b->aturan_wajib === 'khusus_prestasi')
                            <flux:badge color="amber" size="sm">Khusus Prestasi (Wajib)</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">Opsional</flux:badge>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editBerkasModal-{{ $b->id }}').style.display='flex'">
                            Edit
                        </flux:button>
                        <form action="{{ route('admin.jenis-berkas.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis berkas ini?')" class="inline-flex m-0 p-0 items-center">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" variant="danger" size="xs" icon="trash">
                                Hapus
                            </flux:button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-slate-500 py-6 text-xs">
                    Belum ada master berkas terdaftar.
                </div>
            @endforelse
        </div>
    </flux:card>
</div>
@endsection
