@extends('layouts.admin')

@section('title', 'Dashboard Administrasi — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ringkasan Operasional PPDB')
@section('header-subtitle', 'Tahun Ajaran ' . $tahunAjaran->nama . ' • Data Terkini Sistem PPDB SMK Muhammadiyah Paguyangan')

@section('header-action')
    <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
        <flux:button href="{{ route('admin.pendaftar.baru') }}" variant="primary" size="sm" icon="user-plus" class="flex-1 sm:flex-none">
            Pendaftaran Offline Baru
        </flux:button>
        <flux:button href="{{ route('admin.seragam.index') }}" variant="outline" size="sm" icon="shopping-bag" class="flex-1 sm:flex-none">
            Buka Loket Seragam
        </flux:button>
    </div>
@endsection

@section('content')

{{-- 4 Stat Cards Menggunakan Flux Card (FR-30, No Invented Slop Numbers) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <flux:card class="p-5 flex flex-col justify-between">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Calon Siswa</div>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalPendaftar }}</div>
        </div>
        <div class="text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
            <span class="font-semibold text-slate-700">{{ $pendaftarOnline }}</span> Daring &bull; <span class="font-semibold text-slate-700">{{ $pendaftarOffline }}</span> Langsung (Offline)
        </div>
    </flux:card>

    <flux:card class="p-5 flex flex-col justify-between">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Menunggu Verifikasi</div>
            <div class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $berkasMenungguCount }}</div>
        </div>
        <div class="text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
            <span class="font-semibold text-rose-600">{{ $berkasDitolakCount }}</span> berkas perlu perbaikan
        </div>
    </flux:card>

    <flux:card class="p-5 flex flex-col justify-between">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pengambilan Seragam</div>
            <div class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $totalSeragamDiambil }}</div>
        </div>
        <div class="text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
            Siswa yang telah dilayani di loket TU
        </div>
    </flux:card>

    <flux:card class="p-5 flex flex-col justify-between">
        <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Promo Bebas SPP 3 Bulan</div>
            <div class="text-3xl font-extrabold text-blue-600 tracking-tight">
                {{ $promoKlaimAktifCount }} <span class="text-base font-normal text-slate-400">/ {{ $kuotaPromo }}</span>
            </div>
        </div>
        <div class="text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
            <span class="font-semibold text-slate-700">{{ $promoKandidatCount }}</span> kandidat belum tuntas berkas
        </div>
    </flux:card>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Tabel Rekap Daya Tampung per Jurusan --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-900 m-0">
                Keterisian Kuota per Jurusan Vokasi
            </h2>
            <flux:button href="{{ route('admin.jurusan.index') }}" variant="ghost" size="xs" icon-trailing="arrow-right">
                Kelola Jurusan
            </flux:button>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th>Jurusan</th>
                        <th style="text-align: center;">Daya Tampung</th>
                        <th style="text-align: center;">Pendaftar</th>
                        <th style="text-align: center;">Sisa Kuota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurusans as $j)
                        @php
                            $sisa = max(0, $j->kuota - $j->calon_siswa_count);
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-slate-900">{{ $j->nama_jurusan }}</strong>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $j->bidang_keahlian }}</div>
                            </td>
                            <td style="text-align: center;" class="font-medium text-slate-700">{{ $j->kuota }}</td>
                            <td style="text-align: center;" class="font-bold text-blue-600">{{ $j->calon_siswa_count }}</td>
                            <td style="text-align: center;" class="font-semibold {{ $sisa === 0 ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $sisa }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </flux:card>

    {{-- Tabel Rekap Gelombang PPDB --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-900 m-0">
                Pendaftar per Gelombang
            </h2>
            @if(auth('web')->user()->isSuperAdmin())
                <flux:button href="{{ route('admin.gelombang.index') }}" variant="ghost" size="xs" icon-trailing="arrow-right">
                    Kelola Gelombang
                </flux:button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th>Nama Gelombang</th>
                        <th>Rentang Tanggal</th>
                        <th style="text-align: center;">Pendaftar</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gelombangs as $g)
                        <tr>
                            <td>
                                <strong class="text-slate-900">{{ $g->nama_gelombang }}</strong>
                                @if($g->is_bidikmisi)
                                    <flux:badge color="amber" size="sm" class="ms-1">Bidikmisi</flux:badge>
                                @endif
                            </td>
                            <td class="text-xs text-slate-500 whitespace-nowrap">
                                {{ $g->tanggal_mulai->format('d/m/Y') }} - {{ $g->tanggal_selesai->format('d/m/Y') }}
                            </td>
                            <td style="text-align: center;" class="font-bold text-slate-900">{{ $g->calon_siswa_count }}</td>
                            <td style="text-align: center;">
                                @if($g->isSedangBerjalan())
                                    <flux:badge color="emerald" size="sm" inset="top bottom">Aktif Saat Ini</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" inset="top bottom">
                                        {{ $g->is_aktif ? 'Terjadwal' : 'Nonaktif' }}
                                    </flux:badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-slate-500 py-6">Belum ada gelombang pendaftaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>
</div>
@endsection
