@extends('layouts.admin')

@section('title', 'Pesan Masuk Kontak — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Pesan Masuk Pengunjung')
@section('header-subtitle', 'Daftar pesan dan pertanyaan yang dikirim masyarakat melalui formulir kontak website')

@section('content')
<!-- Filter Bar -->
<div class="admin-filter-bar">
    <form action="{{ route('admin.pesan-masuk.index') }}" method="GET" class="flex items-center justify-between w-full flex-wrap gap-3">
        <div class="flex items-center gap-3 flex-wrap">
            <label class="text-xs font-bold text-slate-700 m-0">Status Pesan:</label>
            <select name="status" class="form-select text-xs h-9 min-w-[180px]" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="belum_dibaca" {{ request('status') === 'belum_dibaca' ? 'selected' : '' }}>Belum Dibaca</option>
                <option value="sudah_dibaca" {{ request('status') === 'sudah_dibaca' ? 'selected' : '' }}>Sudah Dibaca</option>
                <option value="dibalas" {{ request('status') === 'dibalas' ? 'selected' : '' }}>Sudah Dibalas</option>
            </select>
        </div>
        <div>
            @if(request()->hasAny(['status']))
                <flux:button href="{{ route('admin.pesan-masuk.index') }}" variant="subtle" size="sm">
                    Reset Filter
                </flux:button>
            @endif
        </div>
    </form>
</div>

<!-- Data Table Card -->
<flux:card class="p-0 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Pesan Masuk ({{ $pesanList->total() }})</h3>
    </div>
    
    @if($pesanList->count() > 0)
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th style="width: 130px;">Waktu Kirim</th>
                        <th style="width: 170px;">Pengirim</th>
                        <th style="width: 160px;">Kontak</th>
                        <th>Subjek & Pesan</th>
                        <th style="width: 130px; text-align: center;">Status</th>
                        <th style="width: 220px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanList as $pesan)
                        <tr class="{{ $pesan->status === 'belum_dibaca' ? 'bg-amber-50/30' : '' }}">
                            <td class="text-xs text-slate-500 whitespace-nowrap">
                                <strong class="text-slate-900">{{ $pesan->created_at->format('d/m/Y') }}</strong><br>
                                <span>{{ $pesan->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <strong class="text-slate-900">{{ $pesan->nama }}</strong>
                            </td>
                            <td class="text-xs">
                                <div><a href="mailto:{{ $pesan->email }}" class="text-blue-600 hover:underline font-medium">{{ $pesan->email }}</a></div>
                                @if($pesan->telepon)
                                    <div class="mt-0.5">
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pesan->telepon)) }}" target="_blank" rel="noopener" class="text-emerald-600 hover:underline font-semibold">
                                            WA: {{ $pesan->telepon }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-900 mb-1 text-xs">{{ $pesan->subjek }}</div>
                                <div class="text-slate-600 text-xs leading-relaxed whitespace-pre-wrap">{{ $pesan->pesan }}</div>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                @if($pesan->status === 'belum_dibaca')
                                    <flux:badge color="amber" size="sm">Belum Dibaca</flux:badge>
                                @elseif($pesan->status === 'sudah_dibaca')
                                    <flux:badge color="blue" size="sm">Sudah Dibaca</flux:badge>
                                @else
                                    <flux:badge color="emerald" size="sm">Dibalas</flux:badge>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form action="{{ route('admin.pesan-masuk.update-status', $pesan->id) }}" method="POST" class="inline-flex m-0 p-0 items-center">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select text-xs h-7 py-0.5 px-2 rounded border-slate-300" onchange="this.form.submit()">
                                            <option value="belum_dibaca" {{ $pesan->status === 'belum_dibaca' ? 'selected' : '' }}>Tandai Baru</option>
                                            <option value="sudah_dibaca" {{ $pesan->status === 'sudah_dibaca' ? 'selected' : '' }}>Tandai Dibaca</option>
                                            <option value="dibalas" {{ $pesan->status === 'dibalas' ? 'selected' : '' }}>Tandai Dibalas</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('admin.pesan-masuk.destroy', $pesan->id) }}" method="POST" onsubmit="return confirm('Hapus pesan dari {{ addslashes($pesan->nama) }}?');" class="inline-flex m-0 p-0 items-center">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="danger" size="xs" icon="trash">
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
        <div class="px-5 py-3.5 border-t border-slate-100 bg-white">
            {{ $pesanList->links() }}
        </div>
    @else
        <div class="text-center text-slate-500 py-12 px-4">
            <svg class="w-10 h-10 text-slate-400 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            <p class="m-0 text-sm">Belum ada pesan masuk dari pengunjung website.</p>
        </div>
    @endif
</flux:card>
@endsection
