@extends('layouts.admin')

@section('title', 'Manajemen Gelombang PPDB — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Manajemen Gelombang PPDB')
@section('header-subtitle', ($tahunAjaran ? 'Tahun Ajaran: ' . $tahunAjaran->nama . ' • ' : '') . 'Atur jadwal gelombang pendaftaran, status aktif, dan alokasi Bidikmisi')

@section('subnav')
    @include('partials.admin.setup-ppdb-tabs')
@endsection

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
                <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran Target <span class="text-rose-500">*</span></label>
                <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control" required>
                    @foreach($semuaTahunAjaran as $ta)
                        <option value="{{ $ta->id }}" {{ (old('tahun_ajaran_id', $tahunAjaran?->id) == $ta->id) ? 'selected' : '' }}>
                            {{ $ta->nama }} {{ $ta->is_aktif ? '(Tahun Ajaran Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

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
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Gelombang PPDB</h2>
                <p class="text-xs text-slate-500 m-0">
                    Menampilkan gelombang untuk: <strong class="text-slate-800">{{ $tahunAjaran?->nama ?? 'Semua' }}</strong>
                </p>
            </div>
            @if(isset($semuaTahunAjaran) && $semuaTahunAjaran->count() > 0)
                <div class="flex items-center gap-2">
                    <label for="filter_tahun_ajaran" class="text-xs text-slate-500 font-medium whitespace-nowrap">Tahun Ajaran:</label>
                    <select id="filter_tahun_ajaran" onchange="window.location.href = '{{ route('admin.gelombang.index') }}?tahun_ajaran_id=' + this.value" class="form-control text-xs py-1.5 px-2.5 h-auto w-auto min-w-[150px]">
                        @foreach($semuaTahunAjaran as $ta)
                            <option value="{{ $ta->id }}" {{ ($tahunAjaran?->id == $ta->id) ? 'selected' : '' }}>
                                {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="admin-desktop-table overflow-x-auto">
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
                            <td style="text-align: right; white-space: nowrap;">
                                <div class="flex items-center justify-end gap-1.5">
                                    <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editGelombangModal-{{ $g->id }}').style.display='flex'">
                                        Edit
                                    </flux:button>
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
                                </div>

                                <!-- Modal Edit Gelombang -->
                                <div id="editGelombangModal-{{ $g->id }}" style="display: none;" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-4">
                                    <div class="bg-white rounded-xl max-w-lg w-full p-6 text-left shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                                            <h3 class="text-base font-bold text-slate-900 m-0">Edit Gelombang PPDB</h3>
                                            <button type="button" onclick="document.getElementById('editGelombangModal-{{ $g->id }}').style.display='none'" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 leading-none">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.gelombang.update', $g->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-3">
                                                <label class="form-label">Tahun Ajaran Target <span class="text-rose-500">*</span></label>
                                                <select name="tahun_ajaran_id" class="form-control" required>
                                                    @foreach($semuaTahunAjaran as $ta)
                                                        <option value="{{ $ta->id }}" {{ ($g->tahun_ajaran_id == $ta->id) ? 'selected' : '' }}>
                                                            {{ $ta->nama }} {{ $ta->is_aktif ? '(Tahun Ajaran Aktif)' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nama Gelombang <span class="text-rose-500">*</span></label>
                                                <input type="text" name="nama_gelombang" class="form-control" value="{{ $g->nama_gelombang }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Kode Singkat</label>
                                                <input type="text" name="kode_gelombang" class="form-control" value="{{ $g->kode_gelombang }}">
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                <div>
                                                    <label class="form-label">Tanggal Mulai <span class="text-rose-500">*</span></label>
                                                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ $g->tanggal_mulai->format('Y-m-d') }}" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Tanggal Selesai <span class="text-rose-500">*</span></label>
                                                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ $g->tanggal_selesai->format('Y-m-d') }}" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Keterangan / Alokasi</label>
                                                <textarea name="keterangan" class="form-control" rows="2">{{ $g->keterangan }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                                                    <input type="checkbox" name="is_bidikmisi" value="1" {{ $g->is_bidikmisi ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                                                    <span>Tandai sebagai Gelombang Khusus Program Bidikmisi</span>
                                                </label>
                                            </div>

                                            <div class="mb-5">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                                                    <input type="checkbox" name="is_aktif" value="1" {{ $g->is_aktif ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                                                    <span>Status Gelombang Aktif</span>
                                                </label>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                                <flux:button type="button" variant="ghost" size="sm" onclick="document.getElementById('editGelombangModal-{{ $g->id }}').style.display='none'">
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
                                Belum ada gelombang pendaftaran untuk tahun ajaran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tampilan Kartu Mobile Android -->
        <div class="admin-mobile-cards p-3 flex flex-col gap-3">
            @forelse($gelombangs as $g)
                <div class="admin-data-card p-3.5 bg-white rounded-lg border border-slate-200 shadow-xs">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <strong class="text-sm text-slate-900 block">{{ $g->nama_gelombang }}</strong>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $g->tanggal_mulai->format('d/m/Y') }} s.d. {{ $g->tanggal_selesai->format('d/m/Y') }}
                            </div>
                        </div>
                        @if($g->isSedangBerjalan())
                            <flux:badge color="emerald" size="sm">Aktif Berjalan</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">
                                {{ $g->is_aktif ? 'Terjadwal' : 'Nonaktif' }}
                            </flux:badge>
                        @endif
                    </div>

                    @if($g->is_bidikmisi)
                        <div class="mb-2">
                            <flux:badge color="amber" size="sm">Alokasi Bidikmisi</flux:badge>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-xs py-2 border-t border-slate-100">
                        <span class="text-slate-500 font-medium">Total Pendaftar:</span>
                        <span class="font-bold text-blue-600">{{ $g->calon_siswa_count }} Siswa</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editGelombangModal-{{ $g->id }}').style.display='flex'">
                            Edit
                        </flux:button>
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
                    </div>
                </div>
            @empty
                <div class="text-center text-slate-500 py-6 text-xs">
                    Belum ada gelombang pendaftaran untuk tahun ajaran ini.
                </div>
            @endforelse
        </div>
    </flux:card>
</div>
@endsection
