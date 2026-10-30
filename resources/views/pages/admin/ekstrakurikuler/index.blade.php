@extends('layouts.admin')

@section('title', 'Manajemen Ekstrakurikuler')
@section('header-title', 'Manajemen Ekstrakurikuler')
@section('header-subtitle', 'Kelola kegiatan pengembangan bakat minat, kepanduan Hizbul Wathan, beladiri, dan organisasi kesiswaan.')

@section('subnav')
    @include('partials.admin.profil-tabs')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Form Tambah Ekstrakurikuler --}}
    <div class="lg:col-span-5">
        <flux:card class="p-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 m-0">Tambah Ekstrakurikuler Baru</h2>
            </div>

            <form action="{{ route('admin.ekstrakurikuler.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Contoh: Kepanduan Hizbul Wathan (HW)" required>
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori Kegiatan <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="kategori" class="form-select" required>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="pembina" class="form-label">Nama Pembina / Pelatih</label>
                        <input type="text" name="pembina" id="pembina" class="form-control" placeholder="Contoh: Ust. Ahmad Fauzi">
                    </div>

                    <div>
                        <label for="jadwal" class="form-label">Jadwal Latihan</label>
                        <input type="text" name="jadwal" id="jadwal" class="form-control" placeholder="Contoh: Jumat, 14.00 - 16.30 WIB">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Kegiatan <span class="text-rose-500">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Tuliskan tujuan dan kegiatan utama ekskul..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="foto" class="form-label">Foto Dokumentasi Kegiatan</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, atau WebP (Maks. 2 MB).</div>
                </div>

                <flux:button type="submit" variant="primary" class="w-full">
                    Simpan Ekstrakurikuler
                </flux:button>
            </form>
        </flux:card>
    </div>

    {{-- Tabel Daftar Ekstrakurikuler --}}
    <div class="lg:col-span-7">
        <flux:card class="p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Ekstrakurikuler ({{ $ekskul->count() }})</h2>
            </div>

            <div class="admin-desktop-table overflow-x-auto">
                <table class="admin-table w-full">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Foto</th>
                            <th>Nama Ekskul & Kategori</th>
                            <th>Pembina & Jadwal</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ekskul as $e)
                            <tr>
                                <td>
                                    <img src="{{ $e->foto_url }}" alt="{{ $e->nama }}" class="w-12 h-10 rounded object-cover border border-slate-200">
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900 text-sm">{{ $e->nama }}</div>
                                    <div class="text-xs text-blue-600 font-semibold mt-0.5">{{ $e->kategori }}</div>
                                </td>
                                <td>
                                    <div class="text-xs text-slate-700"><strong>Pembina:</strong> {{ $e->pembina ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $e->jadwal ?? 'Jadwal menyesuaikan' }}</div>
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <flux:button size="xs" variant="ghost" onclick="bukaModalEditEkskul({{ json_encode($e) }})" title="Edit Ekskul">
                                            Edit
                                        </flux:button>
                                        <form action="{{ route('admin.ekstrakurikuler.destroy', $e->id) }}" method="POST" onsubmit="return confirm('Hapus ekstrakurikuler ini?')">
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
                                    Belum ada data ekstrakurikuler.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</div>

{{-- Dialog Modal Edit Ekstrakurikuler --}}
<div id="modalEditEkskul" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Ekstrakurikuler</h3>
            <button type="button" onclick="tutupModalEditEkskul()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formEditEkskul" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="edit_nama" class="form-label">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="edit_kategori" class="form-label">Kategori <span class="text-rose-500">*</span></label>
                <select name="kategori" id="edit_kategori" class="form-select" required>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="edit_pembina" class="form-label">Nama Pembina</label>
                    <input type="text" name="pembina" id="edit_pembina" class="form-control">
                </div>
                <div>
                    <label for="edit_jadwal" class="form-label">Jadwal</label>
                    <input type="text" name="jadwal" id="edit_jadwal" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label for="edit_deskripsi" class="form-label">Deskripsi <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-4">
                <label for="edit_foto" class="form-label">Ganti Foto Kegiatan (Opsional)</label>
                <input type="file" name="foto" id="edit_foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah foto.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <flux:button type="button" variant="outline" onclick="tutupModalEditEkskul()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
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
</script>
@endpush
@endsection

