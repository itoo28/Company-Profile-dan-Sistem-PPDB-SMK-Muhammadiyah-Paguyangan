@extends('layouts.admin')

@section('title', 'Loket Pelayanan Seragam — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Loket Pelayanan & Pencatatan Seragam')
@section('header-subtitle', 'Pemberian seragam hanya untuk siswa yang seluruh berkas wajibnya Disetujui (BR-05) dan hanya dapat diambil sekali (BR-06)')

@section('content')

{{-- Search & Filter --}}
<form method="GET" action="{{ route('admin.seragam.index') }}" class="admin-filter-bar flex items-center justify-between flex-wrap gap-3">
    <div class="flex-1 min-w-[240px]">
        <input type="text" 
               name="q" 
               value="{{ request('q') }}" 
               class="form-control text-xs h-9" 
               placeholder="Cari Nama Siswa atau Nomor Pendaftaran...">
    </div>

    <div>
        <select name="status" class="form-select text-xs h-9">
            <option value="">Semua Status Pengambilan</option>
            <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Ambil</option>
            <option value="sudah" {{ request('status') === 'sudah' ? 'selected' : '' }}>Sudah Ambil</option>
        </select>
    </div>

    <div class="flex items-center gap-1.5">
        <flux:button type="submit" variant="primary" size="sm">
            Cari
        </flux:button>
        @if(request()->hasAny(['q', 'status']))
            <flux:button href="{{ route('admin.seragam.index') }}" variant="subtle" size="sm">
                Reset
            </flux:button>
        @endif
    </div>
</form>

<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 m-0">Antrean & Status Pengambilan Seragam ({{ $siswaList->total() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama Lengkap</th>
                    <th>Jurusan</th>
                    <th>Jalur & Gelombang</th>
                    <th>Clearance Berkas (BR-05)</th>
                    <th>Status Pengambilan (BR-06)</th>
                    <th style="text-align: right; width: 220px;">Aksi Loket TU</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswaList as $idx => $s)
                    @php
                        $isClearance = $s->isSeragamClearanceApproved();
                        $isAmbil = $s->pengambilanSeragam !== null;
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
                                <flux:badge color="rose" size="sm">Belum Clearance</flux:badge>
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
                                <form action="{{ route('admin.seragam.store') }}" method="POST" onsubmit="return confirm('Konfirmasi: Catat pengambilan seragam untuk {{ addslashes($s->nama_lengkap) }}? Data hanya boleh dicatat 1 kali (BR-06).')" class="inline-flex m-0 p-0 items-center">
                                    @csrf
                                    <input type="hidden" name="calon_siswa_id" value="{{ $s->id }}">
                                    <flux:button type="submit" variant="primary" size="xs" icon="check">
                                        Catat Pengambilan
                                    </flux:button>
                                </form>
                            @elseif($isAmbil)
                                <span class="text-xs text-emerald-600 font-semibold inline-flex items-center gap-1">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Selesai Dilayani
                                </span>
                            @else
                                <span class="text-xs text-slate-400 italic" title="Berkas wajib belum semua disetujui">
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
    <div class="px-5 py-3.5 border-t border-slate-100 bg-white">
        {{ $siswaList->links() }}
    </div>
</flux:card>
@endsection
