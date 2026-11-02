@extends('layouts.admin')

@section('title', 'Kelola Ekstrakurikuler — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ekstrakurikuler & Pengembangan Bakat')
@section('header-subtitle', 'Kelola kegiatan pengembangan bakat minat, kepanduan Hizbul Wathan, beladiri Tapak Suci, dan organisasi kesiswaan.')

@section('header-action')
    <flux:button type="button" onclick="bukaModalTambahEkskul()" variant="primary" icon="plus">
        Tambah Ekskul Baru
    </flux:button>
@endsection

@section('subnav')
    @include('partials.admin.profil-tabs')
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Filter & Pencarian Bar -->
    <div class="admin-filter-bar">
        <form action="{{ route('admin.ekstrakurikuler.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-group">
                <div class="search-input-wrapper">
                    <input type="text" name="q" class="form-control form-control-search text-xs h-9" placeholder="Cari nama ekskul, pembina, deskripsi..." value="{{ $cari ?? '' }}">
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
                        <flux:button href="{{ route('admin.ekstrakurikuler.index') }}" variant="subtle" size="sm">
                            Reset
                        </flux:button>
                    @endif
                </div>
            </div>

            <div class="admin-filter-total text-xs text-slate-500 font-semibold">
                Total: <span class="text-slate-900 font-bold">{{ $ekskul->total() }}</span> Ekstrakurikuler
            </div>
        </form>
    </div>

    <!-- Data Table Card Menggunakan Flux Card -->
    <flux:card class="p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Kegiatan Ekstrakurikuler</h3>
        </div>
        <div>
            @if($ekskul->count() > 0)
                <div class="overflow-x-auto">
                    <table class="admin-table w-full">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: center;">Foto</th>
                                <th>Nama Ekstrakurikuler</th>
                                <th style="width: 170px;">Kategori</th>
                                <th style="width: 220px;">Pembina & Jadwal</th>
                                <th style="width: 180px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ekskul as $item)
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
                                    <td>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem;">
                                            {{ $item->pembina ?? 'Belum ditentukan' }}
                                        </div>
                                        <div style="font-size: 0.775rem; color: #64748b; margin-top: 2px;">
                                            {{ $item->jadwal ?? 'Jadwal fleksibel' }}
                                        </div>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <flux:button type="button" variant="subtle" size="sm" icon="pencil-square" onclick="bukaModalEditEkskul({{ json_encode($item) }})">
                                                Edit
                                            </flux:button>
                                            <form action="{{ route('admin.ekstrakurikuler.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ekstrakurikuler &quot;{{ addslashes($item->nama) }}&quot;?');" class="inline-flex m-0 p-0 items-center">
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
                    {{ $ekskul->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: #64748b;">
                    <div style="font-weight: 700; color: #0f172a; font-size: 1rem;">Tidak ada ekstrakurikuler ditemukan</div>
                    <p style="font-size: 0.85rem; margin: 4px 0 16px;">Silakan sesuaikan kata kunci pencarian atau tambah kegiatan ekskul baru.</p>
                    <flux:button type="button" onclick="bukaModalTambahEkskul()" variant="primary" icon="plus">
                        Tambah Ekskul Pertama
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:card>
</div>

{{-- Dialog Modal Tambah Ekstrakurikuler --}}
<div id="modalTambahEkskul" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="text-sm font-bold text-slate-900 m-0">Tambah Ekstrakurikuler Baru</h3>
            <button type="button" onclick="tutupModalTambahEkskul()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>
        <form action="{{ route('admin.ekstrakurikuler.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="mb-3.5">
                <label for="tambah_nama" class="form-label">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="tambah_nama" class="form-control" placeholder="Contoh: Kepanduan Hizbul Wathan (HW)" required>
            </div>

            <div class="mb-3.5">
                <label for="tambah_kategori" class="form-label">Kategori Kegiatan <span class="text-rose-500">*</span></label>
                <select name="kategori" id="tambah_kategori" class="form-select" required>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3.5">
                <div>
                    <label for="tambah_pembina" class="form-label">Nama Pembina / Pelatih</label>
                    <input type="text" name="pembina" id="tambah_pembina" class="form-control" placeholder="Contoh: Ust. Ahmad Fauzi">
                </div>

                <div>
                    <label for="tambah_jadwal" class="form-label">Jadwal Latihan</label>
                    <input type="text" name="jadwal" id="tambah_jadwal" class="form-control" placeholder="Contoh: Jumat, 14.00 - 16.30 WIB">
                </div>
            </div>

            <div class="mb-3.5">
                <label for="tambah_deskripsi" class="form-label">Deskripsi Kegiatan <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="tambah_deskripsi" class="form-control" rows="3" placeholder="Tuliskan tujuan dan kegiatan utama ekskul..." required></textarea>
            </div>

            <div class="mb-5">
                <label for="tambah_foto" class="form-label">Foto Dokumentasi Kegiatan</label>
                <input type="file" name="foto" id="tambah_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, atau WebP (Maks. 2 MB).</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <flux:button type="button" variant="subtle" onclick="tutupModalTambahEkskul()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Ekstrakurikuler</flux:button>
            </div>
        </form>
    </div>
</div>

{{-- Dialog Modal Edit Ekstrakurikuler --}}
<div id="modalEditEkskul" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Ekstrakurikuler</h3>
            <button type="button" onclick="tutupModalEditEkskul()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>
        <form id="formEditEkskul" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-3.5">
                <label for="edit_nama" class="form-label">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>

            <div class="mb-3.5">
                <label for="edit_kategori" class="form-label">Kategori <span class="text-rose-500">*</span></label>
                <select name="kategori" id="edit_kategori" class="form-select" required>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3.5">
                <div>
                    <label for="edit_pembina" class="form-label">Nama Pembina</label>
                    <input type="text" name="pembina" id="edit_pembina" class="form-control">
                </div>
                <div>
                    <label for="edit_jadwal" class="form-label">Jadwal</label>
                    <input type="text" name="jadwal" id="edit_jadwal" class="form-control">
                </div>
            </div>

            <div class="mb-3.5">
                <label for="edit_deskripsi" class="form-label">Deskripsi Kegiatan <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-5">
                <label for="edit_foto" class="form-label">Ganti Foto Ekskul (Opsional)</label>
                <input type="file" name="foto" id="edit_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah foto dokumentasi.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <flux:button type="button" variant="subtle" onclick="tutupModalEditEkskul()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bukaModalTambahEkskul() {
    document.getElementById('modalTambahEkskul').classList.remove('hidden');
}

function tutupModalTambahEkskul() {
    document.getElementById('modalTambahEkskul').classList.add('hidden');
}

function bukaModalEditEkskul(data) {
    const modal = document.getElementById('modalEditEkskul');
    const form = document.getElementById('formEditEkskul');
    form.action = "{{ url('admin/ekstrakurikuler') }}/" + data.id;

    document.getElementById('edit_nama').value = data.nama;
    document.getElementById('edit_kategori').value = data.kategori;
    document.getElementById('edit_pembina').value = data.pembina || '';
    document.getElementById('edit_jadwal').value = data.jadwal || '';
    document.getElementById('edit_deskripsi').value = data.deskripsi;

    modal.classList.remove('hidden');
}

function tutupModalEditEkskul() {
    document.getElementById('modalEditEkskul').classList.add('hidden');
}

// Close modal on click backdrop
window.addEventListener('click', function(e) {
    const modalTambah = document.getElementById('modalTambahEkskul');
    const modalEdit = document.getElementById('modalEditEkskul');
    if (e.target === modalTambah) tutupModalTambahEkskul();
    if (e.target === modalEdit) tutupModalEditEkskul();
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupModalTambahEkskul();
        tutupModalEditEkskul();
    }
});
</script>
@endpush
@endsection
