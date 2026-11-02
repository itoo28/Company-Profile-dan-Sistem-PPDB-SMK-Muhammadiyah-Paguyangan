@extends('layouts.admin')

@section('title', 'Kelola Sarana & Fasilitas — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Sarana & Fasilitas Sekolah')
@section('header-subtitle', 'Kelola gedung, bengkel praktik kejuruan, laboratorium komputer, dan sarana penunjang pembelajaran')

@section('header-action')
    <flux:button type="button" onclick="bukaModalTambahFasilitas()" variant="primary" icon="plus">
        Tambah Fasilitas Baru
    </flux:button>
@endsection

@section('subnav')
    @include('partials.admin.profil-tabs')
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Filter & Pencarian Bar -->
    <div class="admin-filter-bar">
        <form action="{{ route('admin.fasilitas.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-group">
                <div class="search-input-wrapper">
                    <input type="text" name="q" class="form-control form-control-search text-xs h-9" placeholder="Cari nama fasilitas, deskripsi..." value="{{ $cari ?? '' }}">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                <div class="filter-select-wrapper">
                    <select name="kategori" class="form-select text-xs h-9" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}" {{ request('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-filter-actions">
                    <flux:button type="submit" variant="primary" size="sm">
                        Cari
                    </flux:button>

                    @if(!empty($cari) || request('kategori'))
                        <flux:button href="{{ route('admin.fasilitas.index') }}" variant="subtle" size="sm">
                            Reset
                        </flux:button>
                    @endif
                </div>
            </div>

            <div class="admin-filter-total text-xs text-slate-500 font-semibold">
                Total: <span class="text-slate-900 font-bold">{{ $fasilitas->total() }}</span> Fasilitas
            </div>
        </form>
    </div>

    <!-- Data Table Card Menggunakan Flux Card -->
    <flux:card class="p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Sarana & Fasilitas Sekolah</h3>
        </div>
        <div>
            @if($fasilitas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="admin-table w-full">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: center;">Foto</th>
                                <th>Nama Fasilitas / Sarana</th>
                                <th style="width: 200px;">Kategori</th>
                                <th style="width: 100px; text-align: center;">Urutan</th>
                                <th style="width: 180px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fasilitas as $item)
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-thumb-box">
                                            @if($item->foto_url)
                                                <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'table-thumb-placeholder\'>Foto</div>';">
                                            @else
                                                <div class="table-thumb-placeholder">Foto</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 4px; line-height: 1.35;">
                                            {{ $item->nama }}
                                        </div>
                                        <div style="font-size: 0.8125rem; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $item->deskripsi }}
                                        </div>
                                    </td>
                                    <td>
                                        <flux:badge color="sky" size="sm">{{ $item->kategori }}</flux:badge>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="inline-block font-mono text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                            #{{ $item->urutan }}
                                        </span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <flux:button type="button" variant="subtle" size="sm" icon="pencil-square" onclick="bukaModalEditFasilitas({{ json_encode($item) }})">
                                                Edit
                                            </flux:button>
                                            <form action="{{ route('admin.fasilitas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sarana &quot;{{ addslashes($item->nama) }}&quot;?');" class="inline-flex m-0 p-0 items-center">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" variant="danger" size="sm" icon="trash">
                                                    Hapus
                                                </flux:button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9;">
                    {{ $fasilitas->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: #64748b;">
                    <div style="font-weight: 700; color: #0f172a; font-size: 1rem;">Tidak ada sarana atau fasilitas ditemukan</div>
                    <p style="font-size: 0.85rem; margin: 4px 0 16px;">Silakan sesuaikan kata kunci pencarian atau tambah sarana fasilitas baru.</p>
                    <flux:button type="button" onclick="bukaModalTambahFasilitas()" variant="primary" icon="plus">
                        Tambah Fasilitas Pertama
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:card>
</div>

{{-- Dialog Modal Tambah Fasilitas --}}
<div id="modalTambahFasilitas" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="text-sm font-bold text-slate-900 m-0">Tambah Sarana / Fasilitas Baru</h3>
            <button type="button" onclick="tutupModalTambahFasilitas()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>
        <form action="{{ route('admin.fasilitas.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="mb-3.5">
                <label for="tambah_nama" class="form-label">Nama Fasilitas / Sarana <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="tambah_nama" class="form-control" placeholder="Contoh: Bengkel Praktik Otomotif TKR" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3.5">
                <div>
                    <label for="tambah_kategori" class="form-label">Kategori Sarana <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="tambah_kategori" class="form-select" required>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tambah_urutan" class="form-label">Urutan Tampil <span class="text-rose-500">*</span></label>
                    <input type="number" name="urutan" id="tambah_urutan" class="form-control" value="{{ $fasilitas->total() + 1 }}" min="1" required>
                </div>
            </div>

            <div class="mb-3.5">
                <label for="tambah_deskripsi" class="form-label">Deskripsi & Spesifikasi Alat <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="tambah_deskripsi" class="form-control" rows="4" placeholder="Jelaskan fasilitas dan peralatan praktikum yang tersedia..." required></textarea>
            </div>

            <div class="mb-5">
                <label for="tambah_foto" class="form-label">Foto Sarana / Fasilitas</label>
                <input type="file" name="foto" id="tambah_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, atau WebP (Maks. 2 MB).</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <flux:button type="button" variant="subtle" onclick="tutupModalTambahFasilitas()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Fasilitas</flux:button>
            </div>
        </form>
    </div>
</div>

{{-- Dialog Modal Edit Fasilitas --}}
<div id="modalEditFasilitas" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Sarana / Fasilitas</h3>
            <button type="button" onclick="tutupModalEditFasilitas()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>
        <form id="formEditFasilitas" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-3.5">
                <label for="edit_nama" class="form-label">Nama Fasilitas <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3.5">
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

            <div class="mb-3.5">
                <label for="edit_deskripsi" class="form-label">Deskripsi Fasilitas <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-5">
                <label for="edit_foto" class="form-label">Ganti Foto Sarana (Opsional)</label>
                <input type="file" name="foto" id="edit_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah foto fasilitas.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <flux:button type="button" variant="subtle" onclick="tutupModalEditFasilitas()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bukaModalTambahFasilitas() {
    document.getElementById('modalTambahFasilitas').classList.remove('hidden');
}

function tutupModalTambahFasilitas() {
    document.getElementById('modalTambahFasilitas').classList.add('hidden');
}

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

// Close modal on click backdrop
window.addEventListener('click', function(e) {
    const modalTambah = document.getElementById('modalTambahFasilitas');
    const modalEdit = document.getElementById('modalEditFasilitas');
    if (e.target === modalTambah) tutupModalTambahFasilitas();
    if (e.target === modalEdit) tutupModalEditFasilitas();
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupModalTambahFasilitas();
        tutupModalEditFasilitas();
    }
});
</script>
@endpush
@endsection
