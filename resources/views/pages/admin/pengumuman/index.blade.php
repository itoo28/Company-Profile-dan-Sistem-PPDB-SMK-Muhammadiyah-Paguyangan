@extends('layouts.admin')

@section('title', 'Manajemen Pengumuman')
@section('header-title', 'Manajemen Pengumuman Resmi')
@section('header-subtitle', 'Kelola agenda kegiatan sekolah, pemberitahuan resmi civitas akademika, dan surat edaran.')

@section('subnav')
    @include('partials.admin.warta-tabs')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Form Tambah Pengumuman Baru --}}
    <div class="lg:col-span-5">
        <flux:card class="p-6">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 m-0">Buat Pengumuman Baru</h2>
            </div>

            <form action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Pengumuman <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control" placeholder="Contoh: Jadwal Daftar Ulang PPDB Gelombang 1" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="tanggal" class="form-label">Tanggal Terbit <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div>
                        <label for="status" class="form-label">Status Publikasi <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="terbit">Terbit (Publik)</option>
                            <option value="draf">Draf (Disimpan)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="isi" class="form-label">Isi Lengkap Pengumuman <span class="text-rose-500">*</span></label>
                    <textarea name="isi" id="isi" class="form-control" rows="5" placeholder="Tuliskan isi pengumuman atau instruksi resmi..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_lampiran" class="form-label">File Lampiran / Surat Edaran (Opsional)</label>
                    <input type="file" name="file_lampiran" id="file_lampiran" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <div class="text-[11px] text-slate-500 mt-1">Format: PDF, Word, atau Gambar (Maks. 5 MB).</div>
                </div>

                <flux:button type="submit" variant="primary" class="w-full">
                    Publikasikan Pengumuman
                </flux:button>
            </form>
        </flux:card>
    </div>

    {{-- Tabel Daftar Pengumuman --}}
    <div class="lg:col-span-7">
        <flux:card class="p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Pengumuman ({{ $pengumumans->total() }})</h2>
            </div>

            <div class="admin-desktop-table overflow-x-auto">
                <table class="admin-table w-full">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Tanggal</th>
                            <th>Judul & Ringkasan</th>
                            <th style="width: 90px; text-align: center;">Status</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengumumans as $p)
                            <tr>
                                <td class="text-xs font-semibold text-slate-600 whitespace-nowrap">
                                    {{ $p->tanggal->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900 text-sm line-clamp-1">{{ $p->judul }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-2 mt-0.5">{{ Str::limit(strip_tags($p->isi), 100) }}</div>
                                    @if($p->file_lampiran)
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/' . $p->file_lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline">
                                                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                Unduh Lampiran
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($p->status === 'terbit')
                                        <flux:badge color="green" size="sm">Terbit</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Draf</flux:badge>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <flux:button size="xs" variant="ghost" onclick="bukaModalEditPengumuman({{ json_encode($p) }})" title="Edit Pengumuman">
                                            Edit
                                        </flux:button>
                                        <form action="{{ route('admin.pengumuman.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini secara permanen?')">
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
                                    Belum ada data pengumuman yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengumumans->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $pengumumans->links() }}
                </div>
            @endif
        </flux:card>
    </div>
</div>

{{-- Dialog Modal Edit Pengumuman --}}
<div id="modalEditPengumuman" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Edit Pengumuman</h3>
            <button type="button" onclick="tutupModalEditPengumuman()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formEditPengumuman" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="edit_judul" class="form-label">Judul Pengumuman <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="edit_judul" class="form-control" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="edit_tanggal" class="form-label">Tanggal Terbit <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                </div>
                <div>
                    <label for="edit_status" class="form-label">Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="edit_status" class="form-select" required>
                        <option value="terbit">Terbit (Publik)</option>
                        <option value="draf">Draf (Disimpan)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="edit_isi" class="form-label">Isi Pengumuman <span class="text-rose-500">*</span></label>
                <textarea name="isi" id="edit_isi" class="form-control" rows="5" required></textarea>
            </div>

            <div class="mb-4">
                <label for="edit_file_lampiran" class="form-label">Ganti File Lampiran (Opsional)</label>
                <input type="file" name="file_lampiran" id="edit_file_lampiran" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                <div class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah lampiran yang ada.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <flux:button type="button" variant="outline" onclick="tutupModalEditPengumuman()">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bukaModalEditPengumuman(data) {
    const modal = document.getElementById('modalEditPengumuman');
    const form = document.getElementById('formEditPengumuman');
    form.action = "{{ url('admin/pengumuman') }}/" + data.id;

    document.getElementById('edit_judul').value = data.judul;
    document.getElementById('edit_tanggal').value = data.tanggal ? data.tanggal.substring(0, 10) : '';
    document.getElementById('edit_status').value = data.status;
    document.getElementById('edit_isi').value = data.isi;

    modal.classList.remove('hidden');
}

function tutupModalEditPengumuman() {
    document.getElementById('modalEditPengumuman').classList.add('hidden');
}
</script>
@endpush
@endsection

