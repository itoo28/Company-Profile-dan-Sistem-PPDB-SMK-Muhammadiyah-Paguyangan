@extends('layouts.admin')

@section('title', 'Manajemen Galeri Dokumentasi')
@section('header-title', 'Manajemen Galeri Dokumentasi Foto')
@section('header-subtitle', 'Unggah dan kelola foto kegiatan praktikum bengkel, acara sekolah, kepanduan HW, dan sarana.')

@section('subnav')
    @include('partials.admin.profil-tabs')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Form Tambah Galeri Foto --}}
    <div class="lg:col-span-5">
        <flux:card class="p-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 m-0">Unggah Foto Galeri Baru</h2>
            </div>

            <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Foto / Kegiatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control" placeholder="Contoh: Praktik Tune Up Mesin Siswa TKR" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="kategori" class="form-label">Kategori Album <span class="text-rose-500">*</span></label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tanggal" class="form-label">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan Singkat (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Deskripsi ringkas aktivitas pada foto..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="foto" class="form-label">File Foto <span class="text-rose-500">*</span></label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                    <div class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, atau WebP (Maks. 3 MB).</div>
                </div>

                <flux:button type="submit" variant="primary" class="w-full">
                    Unggah ke Galeri
                </flux:button>
            </form>
        </flux:card>
    </div>

    {{-- Daftar Galeri Foto --}}
    <div class="lg:col-span-7">
        <flux:card class="p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 m-0">Koleksi Foto Galeri ({{ $galeris->total() }})</h2>
            </div>

            <div class="admin-desktop-table overflow-x-auto">
                <table class="admin-table w-full">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Foto</th>
                            <th>Judul & Kategori</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($galeris as $g)
                            <tr>
                                <td>
                                    <img src="{{ $g->foto_url }}" alt="{{ $g->judul }}" class="w-14 h-10 rounded object-cover border border-slate-200 shadow-sm">
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900 text-sm line-clamp-1">{{ $g->judul }}</div>
                                    <div class="text-xs text-blue-600 font-semibold mt-0.5">{{ $g->kategori }}</div>
                                    @if($g->keterangan)
                                        <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $g->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="text-xs text-slate-500 whitespace-nowrap">
                                    {{ $g->tanggal ? $g->tanggal->format('d/m/Y') : '-' }}
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <flux:button size="xs" variant="ghost" onclick="bukaModalEditGaleri({{ json_encode($g) }})" title="Edit Galeri">
                                            Edit
                                        </flux:button>
                                        <form action="{{ route('admin.galeri.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini dari galeri?')">
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
                                <td colspan="4" class="text-center py-8 text-slate-400 text-sm">
                                    Belum ada foto yang diunggah ke galeri.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($galeris->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $galeris->links() }}
                </div>
            @endif
        </flux:card>
    </div>
</div>

{{-- Dialog Modal Edit Galeri --}}
<div id="modalEditGaleri" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Foto Galeri</h3>
            <button type="button" onclick="tutupModalEditGaleri()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formEditGaleri" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="edit_judul" class="form-label">Judul Foto <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="edit_judul" class="form-control" required>
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
                    <label for="edit_tanggal" class="form-label">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label for="edit_keterangan" class="form-label">Keterangan Singkat</label>
                <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-4">
                <label for="edit_foto" class="form-label">Ganti File Foto (Opsional)</label>
                <input type="file" name="foto" id="edit_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengganti file foto.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <flux:button type="button" variant="outline" onclick="tutupModalEditGaleri()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bukaModalEditGaleri(data) {
    const modal = document.getElementById('modalEditGaleri');
    const form = document.getElementById('formEditGaleri');
    form.action = "{{ url('admin/galeri') }}/" + data.id;

    document.getElementById('edit_judul').value = data.judul;
    document.getElementById('edit_kategori').value = data.kategori;
    document.getElementById('edit_tanggal').value = data.tanggal ? data.tanggal.substring(0, 10) : '';
    document.getElementById('edit_keterangan').value = data.keterangan || '';

    modal.classList.remove('hidden');
}

function tutupModalEditGaleri() {
    document.getElementById('modalEditGaleri').classList.add('hidden');
}
</script>
@endpush
@endsection

