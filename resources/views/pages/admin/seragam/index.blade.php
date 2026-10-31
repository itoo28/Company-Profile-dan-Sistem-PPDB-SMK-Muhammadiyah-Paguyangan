@extends('layouts.admin')

@section('title', 'Loket Pelayanan Seragam — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Loket Pelayanan & Pencatatan Seragam')
@section('header-subtitle', 'Pemberian seragam hanya untuk siswa yang seluruh berkas wajibnya Disetujui dan hanya dapat diambil sekali')

@section('content')

{{-- Search & Filter --}}
<form method="GET" action="{{ route('admin.seragam.index') }}" class="admin-filter-bar admin-filter-form">
    <div class="admin-filter-group">
        <div class="search-input-wrapper">
            <input type="text" 
                   name="q" 
                   value="{{ request('q') }}" 
                   class="form-control text-xs h-9" 
                   placeholder="Cari Nama Siswa atau Nomor Pendaftaran...">
        </div>

        <div class="filter-select-wrapper">
            <select name="status" class="form-select text-xs h-9" onchange="this.form.submit()">
                <option value="">Semua Status Pengambilan</option>
                <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Ambil</option>
                <option value="sudah" {{ request('status') === 'sudah' ? 'selected' : '' }}>Sudah Ambil</option>
            </select>
        </div>

        <div class="admin-filter-actions">
            <flux:button type="submit" variant="primary" size="sm">
                Cari
            </flux:button>
            @if(request()->hasAny(['q', 'status']))
                <flux:button href="{{ route('admin.seragam.index') }}" variant="subtle" size="sm">
                    Reset
                </flux:button>
            @endif
        </div>
    </div>
</form>

<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 m-0">Antrean & Status Pengambilan Seragam ({{ $siswaList->total() }})</h3>
    </div>
    {{-- Mode Desktop: Tabel Lengkap --}}
    <div class="admin-desktop-table">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Jurusan</th>
                        <th>Jalur & Gelombang</th>
                        <th>Clearance Loket</th>
                        <th>Status Pengambilan</th>
                        <th style="text-align: right; width: 220px;">Aksi Loket TU</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $idx => $s)
                        @php
                            $isClearance = $s->isSeragamClearanceApproved();
                            $isAmbil = $s->pengambilanSeragam !== null;
                            $isLunas = $s->isPembayaranLunas();
                            $isBerkas = $s->isBerkasWajibDisetujui();
                        @endphp
                        <tr>
                            <td class="text-slate-500 font-semibold">{{ $siswaList->firstItem() + $idx }}</td>
                            <td>
                                <strong class="text-blue-600 font-mono text-xs">{{ $s->nomor_pendaftaran }}</strong>
                                <div class="text-xs text-slate-500 mt-0.5">Urut #{{ $s->nomor_urut }}</div>
                            </td>
                            <td>
                                <strong class="text-slate-900">{{ $s->nama_lengkap }}</strong>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $s->asal_sekolah }}</div>
                            </td>
                            <td>
                                <flux:badge color="blue" size="sm">
                                    {{ $s->jurusan->kode ?? '-' }}
                                </flux:badge>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-800 text-xs">{{ ucfirst($s->jalur_pendaftaran) }}</div>
                                <div class="text-xs text-slate-500">{{ $s->gelombang->nama_gelombang ?? '-' }}</div>
                            </td>
                            <td>
                                @if($isClearance)
                                    <flux:badge color="emerald" size="sm">Clearance Disetujui</flux:badge>
                                @else
                                    <div class="flex flex-col gap-1">
                                        @if(!$isLunas)
                                            <flux:badge color="amber" size="sm">Belum Lunas</flux:badge>
                                        @else
                                            <flux:badge color="emerald" size="sm">Biaya Lunas</flux:badge>
                                        @endif
                                        @if(!$isBerkas)
                                            <flux:badge color="rose" size="sm">Berkas Belum Lengkap</flux:badge>
                                        @else
                                            <flux:badge color="emerald" size="sm">Berkas Disetujui</flux:badge>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($isAmbil)
                                    <div class="text-xs font-bold text-emerald-700">
                                        SUDAH DIAMBIL
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        {{ $s->pengambilanSeragam->waktu_ambil->format('d/m/Y H:i') }} &bull; Oleh: {{ $s->pengambilanSeragam->petugas->name ?? 'Staf' }}
                                    </div>
                                @else
                                    <flux:badge color="zinc" size="sm">
                                        Belum Mengambil
                                    </flux:badge>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if(!$isAmbil && $isClearance)
                                    <form action="{{ route('admin.seragam.store') }}" method="POST" onsubmit="return confirm('Konfirmasi: Catat pengambilan seragam untuk {{ addslashes($s->nama_lengkap) }}? Data hanya boleh dicatat 1 kali.')" class="inline-flex m-0 p-0 items-center">
                                        @csrf
                                        <input type="hidden" name="calon_siswa_id" value="{{ $s->id }}">
                                        <flux:button type="submit" variant="primary" size="xs" icon="check">
                                            Catat Pengambilan
                                        </flux:button>
                                    </form>
                                @elseif($isAmbil)
                                    <div class="flex items-center justify-end gap-1.5">
                                        <span class="text-xs text-emerald-600 font-semibold inline-flex items-center gap-1">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Selesai
                                        </span>
                                        @if(auth('web')->user()->isSuperAdmin())
                                            <form action="{{ route('admin.seragam.destroy', $s->pengambilanSeragam->id) }}" method="POST" onsubmit="return confirm('Batalkan pencatatan pengambilan seragam untuk {{ addslashes($s->nama_lengkap) }}? Status akan kembali menjadi Belum Diambil.');" class="inline-flex m-0 p-0 items-center">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700 hover:bg-rose-50" icon="x-mark" title="Batalkan Pencatatan Seragam">
                                                    Batalkan
                                                </flux:button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic" title="{{ !$isLunas && !$isBerkas ? 'Pembayaran belum lunas dan berkas belum lengkap' : (!$isLunas ? 'Pembayaran belum lunas' : 'Berkas wajib belum semua disetujui') }}">
                                        Terkunci
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-slate-500 py-8">
                                Tidak ada data calon siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mode Mobile: Card View untuk Loket Seragam di Smartphone Android --}}
    <div class="admin-mobile-cards">
        @forelse($siswaList as $idx => $s)
            @php
                $isClearance = $s->isSeragamClearanceApproved();
                $isAmbil = $s->pengambilanSeragam !== null;
                $isLunas = $s->isPembayaranLunas();
                $isBerkas = $s->isBerkasWajibDisetujui();
            @endphp
            <div class="admin-data-card">
                <div class="admin-data-card-header">
                    <div>
                        <span class="text-blue-600 font-mono text-xs font-bold">{{ $s->nomor_pendaftaran }}</span>
                        <span class="text-xs text-slate-400">&bull; Urut #{{ $s->nomor_urut }}</span>
                        <h4 class="font-bold text-slate-900 text-sm m-0 mt-0.5">{{ $s->nama_lengkap }}</h4>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $s->asal_sekolah }}</div>
                    </div>
                    <flux:badge color="blue" size="sm">
                        {{ $s->jurusan->kode ?? '-' }}
                    </flux:badge>
                </div>

                <div class="admin-data-card-badges">
                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                        {{ ucfirst($s->jalur_pendaftaran) }} &bull; {{ $s->gelombang->nama_gelombang ?? '-' }}
                    </span>

                    @if($isClearance)
                        <flux:badge color="emerald" size="sm">Clearance Disetujui</flux:badge>
                    @else
                        @if(!$isLunas)
                            <flux:badge color="amber" size="sm">Belum Lunas</flux:badge>
                        @endif
                        @if(!$isBerkas)
                            <flux:badge color="rose" size="sm">Berkas Belum Lengkap</flux:badge>
                        @endif
                    @endif

                    @if($isAmbil)
                        <flux:badge color="emerald" size="sm">Sudah Diambil</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">Belum Ambil</flux:badge>
                    @endif
                </div>

                @if($isAmbil)
                    <div class="text-xs text-slate-500 bg-slate-50 p-2 rounded mt-2">
                        Diambil pada: <strong>{{ $s->pengambilanSeragam->waktu_ambil->format('d/m/Y H:i') }}</strong> (Oleh: {{ $s->pengambilanSeragam->petugas->name ?? 'Staf TU' }})
                    </div>
                @endif

                <div class="admin-data-card-footer">
                    @if(!$isAmbil && $isClearance)
                        <form action="{{ route('admin.seragam.store') }}" method="POST" onsubmit="return confirm('Konfirmasi: Catat pengambilan seragam untuk {{ addslashes($s->nama_lengkap) }}? Data hanya boleh dicatat 1 kali.')" class="w-full m-0">
                            @csrf
                            <input type="hidden" name="calon_siswa_id" value="{{ $s->id }}">
                            <flux:button type="submit" variant="primary" class="w-full" size="sm" icon="check">
                                Catat Pengambilan Seragam
                            </flux:button>
                        </form>
                    @elseif($isAmbil)
                        <div class="flex items-center justify-between gap-2 w-full">
                            <span class="text-xs text-emerald-600 font-semibold inline-flex items-center gap-1 py-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Pelayanan Selesai
                            </span>
                            @if(auth('web')->user()->isSuperAdmin())
                                <form action="{{ route('admin.seragam.destroy', $s->pengambilanSeragam->id) }}" method="POST" onsubmit="return confirm('Batalkan pencatatan pengambilan seragam untuk {{ addslashes($s->nama_lengkap) }}?');" class="inline-flex m-0 p-0 items-center">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="danger" size="xs" icon="x-mark">
                                        Batalkan
                                    </flux:button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="text-xs text-rose-600 italic text-center w-full py-1">
                            Akses Terkunci ({{ !$isLunas && !$isBerkas ? 'Menunggu pembayaran lunas & kelengkapan berkas' : (!$isLunas ? 'Menunggu pembayaran lunas' : 'Menunggu berkas wajib lolos verifikasi') }})
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-slate-500 py-8 px-4 text-xs">
                Tidak ada data calon siswa ditemukan.
            </div>
        @endforelse
    </div>
    <div class="px-5 py-3.5 border-t border-slate-100 bg-white">
        {{ $siswaList->links() }}
    </div>
</flux:card>
@endsection
