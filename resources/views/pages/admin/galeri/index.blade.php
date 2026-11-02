@extends('layouts.admin')

@section('title', 'Kelola Galeri Dokumentasi Foto — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Galeri Dokumentasi Foto')
@section('header-subtitle', 'Unggah dan kelola arsip foto kegiatan praktikum bengkel, acara sekolah, kepanduan HW, dan sarana.')

@section('header-action')
    <flux:button type="button" onclick="bukaModalTambahGaleri()" variant="primary" icon="plus">
        Unggah Foto Baru
    </flux:button>
@endsection

@section('subnav')
    @include('partials.admin.profil-tabs')
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Filter & Pencarian Bar -->
    <div class="admin-filter-bar">
        <form action="{{ route('admin.galeri.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-group">
                <div class="search-input-wrapper">
                    <input type="text" name="q" class="form-control form-control-search text-xs h-9" placeholder="Cari judul foto, keterangan..." value="{{ $cari ?? '' }}">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                <div class="filter-select-wrapper">
                    <select name="kategori" class="form-select text-xs h-9" onchange="this.form.submit()">
                        <option value="">Semua Kategori Album</option>
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
                        <flux:button href="{{ route('admin.galeri.index') }}" variant="subtle" size="sm">
                            Reset
                        </flux:button>
                    @endif
                </div>
            </div>

            <div class="admin-filter-total text-xs text-slate-500 font-semibold">
                Total: <span class="text-slate-900 font-bold">{{ $galeris->total() }}</span> Foto
            </div>
        </form>
    </div>

    <!-- Data Table Card Menggunakan Flux Card -->
    <flux:card class="p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Koleksi Dokumentasi Foto Galeri</h3>
        </div>
        <div>
            @if($galeris->count() > 0)
                <div class="overflow-x-auto">
                    <table class="admin-table w-full">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: center;">Foto</th>
                                <th>Judul Foto & Dokumentasi</th>
                                <th style="width: 180px;">Kategori Album</th>
                                <th style="width: 140px;">Tgl Kegiatan</th>
                                <th style="width: 180px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($galeris as $item)
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-thumb-box">
                                            @if($item->foto_url)
                                                <img src="{{ $item->foto_url }}" alt="{{ $item->judul }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'table-thumb-placeholder\'>Foto</div>';">
                                            @else
                                                <div class="table-thumb-placeholder">Foto</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 4px; line-height: 1.35;">
                                            {{ $item->judul }}
                                        </div>
                                        <div style="font-size: 0.8125rem; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $item->keterangan ?? 'Dokumentasi kegiatan resmi SMK Muhammadiyah Paguyangan.' }}
                                        </div>
                                    </td>
                                    <td>
                                        <flux:badge color="sky" size="sm">{{ $item->kategori }}</flux:badge>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <div style="font-weight: 600; color: #0f172a; font-size: 0.85rem;">
                                            {{ $item->tanggal ? $item->tanggal->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <flux:button type="button" variant="subtle" size="sm" icon="pencil-square" onclick="bukaModalEditGaleri({{ json_encode($item) }})">
                                                Edit
                                            </flux:button>
                                            <form action="{{ route('admin.galeri.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto &quot;{{ addslashes($item->judul) }}&quot;?');" class="inline-flex m-0 p-0 items-center">
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
                    {{ $galeris->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: #64748b;">
                    <div style="font-weight: 700; color: #0f172a; font-size: 1rem;">Tidak ada foto galeri ditemukan</div>
                    <p style="font-size: 0.85rem; margin: 4px 0 16px;">Silakan sesuaikan kata kunci pencarian atau unggah foto dokumentasi baru.</p>
                    <flux:button type="button" onclick="bukaModalTambahGaleri()" variant="primary" icon="plus">
                        Unggah Foto Pertama
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:card>
</div>

{{-- Dialog Modal Unggah Foto Galeri --}}
<div id="modalTambahGaleri" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="text-sm font-bold text-slate-900 m-0">Unggah Foto Galeri Baru</h3>
            <button type="button" onclick="tutupModalTambahGaleri()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>
        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="mb-3.5">
                <label for="tambah_judul" class="form-label">Judul Foto / Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="tambah_judul" class="form-control" placeholder="Contoh: Praktik Tune Up Mesin Siswa TKR" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3.5">
                <div>
                    <label for="tambah_kategori" class="form-label">Kategori Album <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="tambah_kategori" class="form-select" required>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tambah_tanggal" class="form-label">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" id="tambah_tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="mb-3.5">
                <label for="tambah_keterangan" class="form-label">Keterangan Singkat (Opsional)</label>
                <textarea name="keterangan" id="tambah_keterangan" class="form-control" rows="3" placeholder="Deskripsi ringkas aktivitas pada foto..."></textarea>
            </div>

            <div class="mb-5">
                <label for="tambah_foto" class="form-label">File Foto Dokumentasi <span class="text-rose-500">*</span></label>
                <input type="file" name="foto" id="tambah_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                <div class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, atau WebP (Maks. 3 MB).</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <flux:button type="button" variant="subtle" onclick="tutupModalTambahGaleri()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Unggah ke Galeri</flux:button>
            </div>
        </form>
    </div>
</div>

{{-- Dialog Modal Edit Foto Galeri --}}
<div id="modalEditGaleri" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Foto Galeri</h3>
            <button type="button" onclick="tutupModalEditGaleri()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>
        <form id="formEditGaleri" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-3.5">
                <label for="edit_judul" class="form-label">Judul Foto <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="edit_judul" class="form-control" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3.5">
                <div>
                    <label for="edit_kategori" class="form-label">Kategori Album <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="edit_kategori" class="form-select" required>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_tanggal" class="form-label">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control">
                </div>
            </div>

            <div class="mb-3.5">
                <label for="edit_keterangan" class="form-label">Keterangan Singkat</label>
                <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-5">
                <label for="edit_foto" class="form-label">Ganti File Foto (Opsional)</label>
                <input type="file" name="foto" id="edit_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah file foto.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <flux:button type="button" variant="subtle" onclick="tutupModalEditGaleri()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bukaModalTambahGaleri() {
    document.getElementById('modalTambahGaleri').classList.remove('hidden');
}

function tutupModalTambahGaleri() {
    document.getElementById('modalTambahGaleri').classList.add('hidden');
}

function bukaModalEditGaleri(data) {
    const modal = document.getElementById('modalEditGaleri');
    const form = document.getElementById('formEditGaleri');
    form.action = "{{ url('admin/galeri') }}/" + data.id;

    document.getElementById('edit_judul').value = data.judul;
    document.getElementById('edit_kategori').value = data.kategori;
    if (data.tanggal) {
        document.getElementById('edit_tanggal').value = data.tanggal.substring(0, 10);
    } else {
        document.getElementById('edit_tanggal').value = '';
    }
    document.getElementById('edit_keterangan').value = data.keterangan || '';

    modal.classList.remove('hidden');
}

function tutupModalEditGaleri() {
    document.getElementById('modalEditGaleri').classList.add('hidden');
}

// Close modal on click backdrop
window.addEventListener('click', function(e) {
    const modalTambah = document.getElementById('modalTambahGaleri');
    const modalEdit = document.getElementById('modalEditGaleri');
    if (e.target === modalTambah) tutupModalTambahGaleri();
    if (e.target === modalEdit) tutupModalEditGaleri();
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupModalTambahGaleri();
        tutupModalEditGaleri();
    }
});
</script>
@endpush
@endsection
