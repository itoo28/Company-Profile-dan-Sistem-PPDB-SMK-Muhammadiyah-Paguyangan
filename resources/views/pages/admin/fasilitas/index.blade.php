@extends('layouts.admin')

@section('title', 'Manajemen Sarana & Fasilitas')
@section('header-title', 'Manajemen Sarana & Fasilitas Sekolah')
@section('header-subtitle', 'Kelola data gedung, bengkel praktik kejuruan, laboratorium komputer, dan sarana penunjang.')

@section('subnav')
    @include('partials.admin.profil-tabs')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Form Tambah Fasilitas --}}
    <div class="lg:col-span-5">
        <flux:card class="p-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 m-0">Tambah Sarana / Fasilitas Baru</h2>
            </div>

            <form action="{{ route('admin.fasilitas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Fasilitas / Sarana <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Contoh: Bengkel Praktik Otomotif TKR" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="kategori" class="form-label">Kategori Sarana <span class="text-rose-500">*</span></label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="urutan" class="form-label">Urutan Tampil <span class="text-rose-500">*</span></label>
                        <input type="number" name="urutan" id="urutan" class="form-control" value="{{ $fasilitas->count() + 1 }}" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi & Spesifikasi Alat <span class="text-rose-500">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" placeholder="Jelaskan fasilitas dan peralatan praktikum yang tersedia..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="foto" class="form-label">Foto Sarana / Fasilitas</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, atau WebP (Maks. 2 MB).</div>
                </div>

                <flux:button type="submit" variant="primary" class="w-full">
                    Simpan Sarana / Fasilitas
                </flux:button>
            </form>
        </flux:card>
    </div>

    {{-- Tabel Daftar Fasilitas --}}
    <div class="lg:col-span-7">
        <flux:card class="p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Sarana & Fasilitas ({{ $fasilitas->count() }})</h2>
            </div>

            <div class="admin-desktop-table overflow-x-auto">
                <table class="admin-table w-full">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Foto</th>
                            <th>Nama Fasilitas</th>
                            <th style="width: 140px;">Kategori</th>
                            <th style="width: 60px; text-align: center;">Urut</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fasilitas as $f)
                            <tr>
                                <td>
                                    <img src="{{ $f->foto_url }}" alt="{{ $f->nama }}" class="w-12 h-10 rounded object-cover border border-slate-200">
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900 text-sm">{{ $f->nama }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ Str::limit($f->deskripsi, 60) }}</div>
                                </td>
                                <td>
                                    <span class="inline-block text-[11px] font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">
                                        {{ $f->kategori }}
                                    </span>
                                </td>
                                <td style="text-align: center;" class="font-mono text-xs font-bold text-slate-600">
                                    {{ $f->urutan }}
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <flux:button size="xs" variant="ghost" onclick="bukaModalEditFasilitas({{ json_encode($f) }})" title="Edit Fasilitas">
                                            Edit
                                        </flux:button>
                                        <form action="{{ route('admin.fasilitas.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Hapus fasilitas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="xs" variant="danger">
                                                Hapus
                                            </flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400 text-sm">
                                    Belum ada data fasilitas sekolah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</div>

{{-- Dialog Modal Edit Fasilitas --}}
<div id="modalEditFasilitas" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Sarana / Fasilitas</h3>
            <button type="button" onclick="tutupModalEditFasilitas()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formEditFasilitas" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="edit_nama" class="form-label">Nama Fasilitas <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="edit_kategori" class="form-label">Kategori <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="edit_kategori" class="form-select" required>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_urutan" class="form-label">Urutan Tampil <span class="text-rose-500">*</span></label>
                    <input type="number" name="urutan" id="edit_urutan" class="form-control" min="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="edit_deskripsi" class="form-label">Deskripsi Fasilitas <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-4">
                <label for="edit_foto" class="form-label">Ganti Foto Sarana (Opsional)</label>
                <input type="file" name="foto" id="edit_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah foto fasilitas.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <flux:button type="button" variant="outline" onclick="tutupModalEditFasilitas()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bukaModalEditFasilitas(data) {
    const modal = document.getElementById('modalEditFasilitas');
    const form = document.getElementById('formEditFasilitas');
    form.action = "{{ url('admin/fasilitas') }}/" + data.id;

    document.getElementById('edit_nama').value = data.nama;
    document.getElementById('edit_kategori').value = data.kategori;
    document.getElementById('edit_urutan').value = data.urutan;
    document.getElementById('edit_deskripsi').value = data.deskripsi;

    modal.classList.remove('hidden');
}

function tutupModalEditFasilitas() {
    document.getElementById('modalEditFasilitas').classList.add('hidden');
}
</script>
@endpush
@endsection

