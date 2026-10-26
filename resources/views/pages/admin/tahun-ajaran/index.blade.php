@extends('layouts.admin')

@section('title', 'Manajemen Tahun Ajaran — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Manajemen Tahun Ajaran PPDB')
@section('header-subtitle', 'Tahun Ajaran Aktif Saat Ini: ' . ($tahunAktif->nama ?? '-') . ' • Atur siklus pendaftaran tahunan tanpa menghapus arsip lama')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    {{-- Form Tambah Tahun Ajaran --}}
    <flux:card class="p-6 lg:col-span-1">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 m-0">Tambah Tahun Ajaran Baru</h2>
        </div>

        <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="nama" class="form-label">
                    Nama Tahun Ajaran <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="nama" 
                       id="nama" 
                       class="form-control @error('nama') is-invalid @enderror" 
                       placeholder="Contoh: 2027/2028" 
                       value="{{ old('nama') }}" 
                       required>
                <small class="form-help">Format: TAHUN/TAHUN (4 digit angka, contoh: 2027/2028).</small>
                @error('nama')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                    <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif') ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                    <span>Langsung jadikan sebagai Tahun Ajaran Aktif</span>
                </label>
                <small class="text-slate-500 text-xs block mt-1 ml-5">
                    Jika dicentang, periode pendaftaran publik akan langsung beralih ke tahun ajaran baru ini.
                </small>
            </div>

            <flux:button type="submit" variant="primary" class="w-full" icon="plus">
                Simpan Tahun Ajaran
            </flux:button>
        </form>
    </flux:card>

    {{-- Daftar Tahun Ajaran & Status Arsip --}}
    <flux:card class="p-0 overflow-hidden lg:col-span-2">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Periode & Arsip Tahun Ajaran</h2>
            <span class="text-xs text-slate-500">Total: {{ $tahunAjarans->count() }} Periode</span>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Status Operasional</th>
                        <th>Pendaftar Tersimpan</th>
                        <th>Gelombang</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjarans as $index => $item)
                        <tr class="{{ $item->is_aktif ? 'bg-amber-50/30' : '' }}">
                            <td class="text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td>
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $item->nama }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    Dibuat: {{ $item->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                @if($item->is_aktif)
                                    <flux:badge color="emerald" size="sm" icon="check-circle">
                                        Aktif (Periode Berjalan)
                                    </flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">
                                        Arsip (Non-Aktif)
                                    </flux:badge>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pendaftar.index', ['tahun_ajaran_id' => $item->id]) }}" 
                                   class="inline-flex items-center gap-1 font-semibold text-xs text-blue-600 hover:underline"
                                   title="Buka Data Pendaftar Tahun {{ $item->nama }}">
                                    <span>{{ number_format($item->calon_siswa_count) }} Siswa</span>
                                    <flux:icon name="arrow-top-right-on-square" class="size-3" />
                                </a>
                            </td>
                            <td>
                                <span class="text-xs text-slate-600 font-medium">
                                    {{ $item->gelombang_count }} Gelombang
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    @if(!$item->is_aktif)
                                        <form action="{{ route('admin.tahun-ajaran.aktifkan', $item->id) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan Tahun Ajaran {{ $item->nama }}? Periode tahun sebelumnya akan otomatis beralih menjadi arsip.');" 
                                              style="margin: 0;">
                                            @csrf
                                            <flux:button type="submit" variant="primary" size="xs" icon="arrow-path">
                                                Aktifkan
                                            </flux:button>
                                        </form>
                                    @endif

                                    <flux:button type="button" variant="outline" size="xs" icon="pencil" onclick="document.getElementById('editModal-{{ $item->id }}').showModal()">
                                        Ubah
                                    </flux:button>

                                    @if(!$item->is_aktif && $item->calon_siswa_count == 0 && $item->gelombang_count == 0)
                                        <form action="{{ route('admin.tahun-ajaran.destroy', $item->id) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Tahun Ajaran {{ $item->nama }}?');" 
                                              style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" variant="danger" size="xs" icon="trash">
                                                Hapus
                                            </flux:button>
                                        </form>
                                    @endif
                                </div>

                                {{-- Modal Edit Nama Tahun Ajaran --}}
                                <dialog id="editModal-{{ $item->id }}" class="p-6 rounded-xl border border-slate-200 shadow-xl backdrop:bg-slate-900/40 max-w-sm w-full">
                                    <div class="mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                                        <h3 class="font-bold text-sm text-slate-900 m-0">Ubah Nama Tahun Ajaran</h3>
                                        <button type="button" onclick="document.getElementById('editModal-{{ $item->id }}').close()" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
                                    </div>
                                    <form action="{{ route('admin.tahun-ajaran.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-4 text-left">
                                            <label class="form-label">Nama Tahun Ajaran <span class="text-rose-500">*</span></label>
                                            <input type="text" name="nama" class="form-control" value="{{ $item->nama }}" placeholder="Contoh: 2027/2028" required>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <flux:button type="button" variant="ghost" size="sm" onclick="document.getElementById('editModal-{{ $item->id }}').close()">Batal</flux:button>
                                            <flux:button type="submit" variant="primary" size="sm">Simpan Perubahan</flux:button>
                                        </div>
                                    </form>
                                </dialog>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400 text-xs">
                                Belum ada data tahun ajaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>
</div>
@endsection
