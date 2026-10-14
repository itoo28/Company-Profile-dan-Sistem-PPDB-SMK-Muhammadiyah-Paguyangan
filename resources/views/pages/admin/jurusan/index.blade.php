@extends('layouts.admin')

@section('title', 'Manajemen Jurusan Kejuruan')
@section('header-title', 'Manajemen Konsentrasi Keahlian / Jurusan')
@section('header-subtitle', 'Kelola konsentrasi keahlian vokasi, deskripsi kompetensi, daya tampung penerimaan, dan status aktif.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    {{-- Form Tambah Jurusan --}}
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 m-0">Tambah Konsentrasi Keahlian Baru</h2>
        </div>
        <form action="{{ route('admin.jurusan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="kode" class="form-label">Kode Singkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode" id="kode" class="form-control" placeholder="Contoh: TKR, RPL" required>
                </div>

                <div>
                    <label for="urutan" class="form-label">Urutan Tampil <span class="text-rose-500">*</span></label>
                    <input type="number" name="urutan" id="urutan" class="form-control" value="1" min="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="nama_jurusan" class="form-label">Nama Lengkap Jurusan <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_jurusan" id="nama_jurusan" class="form-control" placeholder="Contoh: Teknik Kendaraan Ringan" required>
            </div>

            <div class="mb-3">
                <label for="bidang_keahlian" class="form-label">Bidang Keahlian <span class="text-rose-500">*</span></label>
                <input type="text" name="bidang_keahlian" id="bidang_keahlian" class="form-control" placeholder="Contoh: Teknologi Manufaktur dan Rekayasa" required>
            </div>

            <div class="mb-3">
                <label for="kuota" class="form-label">Daya Tampung Siswa (Kuota) <span class="text-rose-500">*</span></label>
                <input type="number" name="kuota" id="kuota" class="form-control" value="72" min="1" required>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Kompetensi <span class="text-rose-500">*</span></label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2" placeholder="Kompetensi kejuruan yang dipelajari siswa..." required></textarea>
            </div>

            <div class="mb-4">
                <label for="prospek_kerja" class="form-label">Prospek Karir & Dunia Kerja</label>
                <textarea name="prospek_kerja" id="prospek_kerja" class="form-control" rows="2" placeholder="Peluang profesi lulusan di industri..."></textarea>
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Simpan Konsentrasi Keahlian
            </flux:button>
        </form>
    </flux:card>

    {{-- Tabel Daftar Jurusan --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-900 m-0">Daftar Jurusan Kejuruan ({{ $jurusans->count() }})</h2>
        </div>

        <div class="admin-desktop-table overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 80px;">Kode</th>
                        <th>Nama Jurusan</th>
                        <th style="text-align: center; width: 80px;">Kuota</th>
                        <th style="text-align: center; width: 100px;">Pendaftar</th>
                        <th style="text-align: right; width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurusans as $j)
                        <tr>
                            <td>
                                <strong class="text-blue-600 font-mono text-xs">{{ $j->kode }}</strong>
                                <div class="text-[11px] text-slate-400">Urut: {{ $j->urutan }}</div>
                            </td>
                            <td>
                                <strong class="text-slate-900">{{ $j->nama_jurusan }}</strong>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $j->bidang_keahlian }}</div>
                                <div class="mt-1">
                                    <flux:badge color="{{ $j->is_aktif ? 'emerald' : 'zinc' }}" size="sm">
                                        {{ $j->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </flux:badge>
                                </div>
                            </td>
                            <td style="text-align: center;" class="font-semibold text-slate-700">{{ $j->kuota }}</td>
                            <td style="text-align: center;">
                                <flux:badge color="blue" size="sm">{{ $j->calon_siswa_count }} Siswa</flux:badge>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div class="flex items-center justify-end gap-1.5">
                                    <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editJurusanModal-{{ $j->id }}').style.display='flex'">
                                        Edit
                                    </flux:button>
                                    @if($j->calon_siswa_count > 0)
                                        <flux:button type="button" variant="subtle" size="xs" icon="trash" disabled title="Tidak bisa dihapus karena sudah ada pendaftar">
                                            Hapus
                                        </flux:button>
                                    @else
                                        <form action="{{ route('admin.jurusan.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')" class="inline-flex m-0 p-0 items-center">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" variant="danger" size="xs" icon="trash">
                                                Hapus
                                            </flux:button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Modal Edit Jurusan -->
                                <div id="editJurusanModal-{{ $j->id }}" style="display: none;" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-4">
                                    <div class="bg-white rounded-xl max-w-lg w-full p-6 text-left shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                                            <h3 class="text-base font-bold text-slate-900 m-0">Edit Jurusan / Konsentrasi Keahlian</h3>
                                            <button type="button" onclick="document.getElementById('editJurusanModal-{{ $j->id }}').style.display='none'" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 leading-none">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.jurusan.update', $j->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                <div>
                                                    <label class="form-label">Kode Singkat <span class="text-rose-500">*</span></label>
                                                    <input type="text" name="kode" class="form-control" value="{{ $j->kode }}" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Urutan Tampil <span class="text-rose-500">*</span></label>
                                                    <input type="number" name="urutan" class="form-control" value="{{ $j->urutan }}" min="1" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap Jurusan <span class="text-rose-500">*</span></label>
                                                <input type="text" name="nama_jurusan" class="form-control" value="{{ $j->nama_jurusan }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Bidang Keahlian <span class="text-rose-500">*</span></label>
                                                <input type="text" name="bidang_keahlian" class="form-control" value="{{ $j->bidang_keahlian }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Daya Tampung Siswa (Kuota) <span class="text-rose-500">*</span></label>
                                                <input type="number" name="kuota" class="form-control" value="{{ $j->kuota }}" min="1" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi Kompetensi <span class="text-rose-500">*</span></label>
                                                <textarea name="deskripsi" class="form-control" rows="2" required>{{ $j->deskripsi }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Prospek Karir & Dunia Kerja</label>
                                                <textarea name="prospek_kerja" class="form-control" rows="2">{{ $j->prospek_kerja }}</textarea>
                                            </div>

                                            <div class="mb-5">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                                                    <input type="checkbox" name="is_aktif" value="1" {{ $j->is_aktif ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                                                    <span>Status Jurusan Aktif (Tampil di PPDB & Publik)</span>
                                                </label>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                                <flux:button type="button" variant="ghost" size="sm" onclick="document.getElementById('editJurusanModal-{{ $j->id }}').style.display='none'">
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
                                Belum ada jurusan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tampilan Kartu Mobile Android -->
        <div class="admin-mobile-cards p-3 flex flex-col gap-3">
            @forelse($jurusans as $j)
                <div class="admin-data-card p-3.5 bg-white rounded-lg border border-slate-200 shadow-xs">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-blue-600">{{ $j->kode }}</span>
                                <span class="text-xs text-slate-400">&bull; Urut #{{ $j->urutan }}</span>
                            </div>
                            <strong class="text-sm text-slate-900 block mt-0.5">{{ $j->nama_jurusan }}</strong>
                            <div class="text-xs text-slate-500">{{ $j->bidang_keahlian }}</div>
                        </div>
                        <flux:badge color="{{ $j->is_aktif ? 'emerald' : 'zinc' }}" size="sm">
                            {{ $j->is_aktif ? 'Aktif' : 'Nonaktif' }}
                        </flux:badge>
                    </div>

                    <div class="flex items-center justify-between text-xs py-2 border-t border-slate-100">
                        <span class="text-slate-500">Daya Tampung / Terisi:</span>
                        <span class="font-bold text-slate-800">{{ $j->calon_siswa_count }} / {{ $j->kuota }} Siswa</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editJurusanModal-{{ $j->id }}').style.display='flex'">
                            Edit
                        </flux:button>
                        @if($j->calon_siswa_count > 0)
                            <flux:button type="button" variant="subtle" size="xs" icon="trash" disabled title="Tidak bisa dihapus karena sudah ada pendaftar">
                                Hapus
                            </flux:button>
                        @else
                            <form action="{{ route('admin.jurusan.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')" class="inline-flex m-0 p-0 items-center">
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
                    Belum ada jurusan yang terdaftar.
                </div>
            @endforelse
        </div>
    </flux:card>
</div>
@endsection
