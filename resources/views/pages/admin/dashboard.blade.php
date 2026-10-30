@extends('layouts.admin')

@section('title', 'Dashboard Administrasi — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Ringkasan Operasional PPDB')
@section('header-subtitle', 'Tahun Ajaran ' . $tahunAjaran->nama . ' • Data Terkini Sistem PPDB SMK Muhammadiyah Paguyangan')

@section('header-action')
    <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
        <form method="GET" action="{{ route('admin.dashboard') }}" style="margin: 0;">
            <select name="tahun_ajaran_id" class="form-select text-xs h-9 font-semibold bg-white border-slate-300 text-slate-800 rounded-lg shadow-sm" onchange="this.form.submit()">
                @foreach($semuaTahunAjaran as $ta)
                    <option value="{{ $ta->id }}" {{ $tahunAjaran->id == $ta->id ? 'selected' : '' }}>
                        Periode: {{ $ta->nama }} {{ $ta->is_aktif ? '✓ (Aktif)' : '(Arsip)' }}
                    </option>
                @endforeach
            </select>
        </form>

        <flux:button href="{{ route('admin.pendaftar.baru') }}" variant="primary" size="sm" icon="user-plus" class="flex-1 sm:flex-none">
            Pendaftaran Offline Baru
        </flux:button>
        <flux:button href="{{ route('admin.seragam.index') }}" variant="outline" size="sm" icon="shopping-bag" class="flex-1 sm:flex-none">
            Buka Loket Seragam
        </flux:button>
    </div>
@endsection

@section('content')

{{-- Banner Arsip jika menampilkan periode lampau --}}
@if(!$tahunAjaran->is_aktif)
    <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between gap-3 text-xs text-amber-900">
        <div class="flex items-center gap-2">
            <flux:icon name="archive-box" class="size-4 text-amber-700 shrink-0" />
            <span>
                Anda sedang melihat <strong>Statistik & Arsip PPDB Tahun Ajaran {{ $tahunAjaran->nama }}</strong>. Periode operasional berjalan saat ini adalah: <strong>{{ $tahunAjaranAktif->nama ?? '-' }}</strong>.
            </span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="font-bold text-amber-800 underline hover:text-amber-950 shrink-0">
            Kembali ke Periode Berjalan &rarr;
        </a>
    </div>
@endif

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
            <span class="font-semibold text-slate-700">{{ $promoKandidatCount }}</span> kandidat belum ambil seragam
        </div>
    </flux:card>
</div>

@php
    $totalKuota = $jurusans->sum('kuota');
    $totalPendaftarJurusan = $jurusans->sum('calon_siswa_count');
    $sisaKuotaJurusan = max(0, $totalKuota - $totalPendaftarJurusan);
    $persenKeterisian = $totalKuota > 0 ? round(($totalPendaftarJurusan / $totalKuota) * 100, 1) : 0;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Card Grafik Keterisian Kuota per Jurusan Vokasi --}}
    <flux:card class="p-0 overflow-hidden flex flex-col justify-between">
        <div class="p-4 border-b border-slate-100 flex flex-wrap gap-2 justify-between items-center bg-slate-50/50">
            <div>
                <h2 class="text-sm font-bold text-slate-900 m-0">
                    Keterisian Kuota per Jurusan Vokasi
                </h2>
                <p class="text-xs text-slate-500 m-0 mt-0.5">Grafik perbandingan pendaftar dengan daya tampung kelas</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex items-center p-0.5 bg-slate-200/70 rounded-lg text-xs font-semibold">
                    <button type="button" class="px-2 py-1 rounded-md text-slate-800 bg-white shadow-xs font-bold transition flex items-center gap-1 cursor-pointer" id="btn-chart-jurusan" onclick="toggleView('jurusan', 'chart')">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                        <span>Grafik</span>
                    </button>
                    <button type="button" class="px-2 py-1 rounded-md text-slate-500 hover:text-slate-800 transition flex items-center gap-1 cursor-pointer" id="btn-table-jurusan" onclick="toggleView('jurusan', 'table')">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
                        <span>Tabel</span>
                    </button>
                </div>
                <flux:button href="{{ route('admin.jurusan.index') }}" variant="ghost" size="xs" icon-trailing="arrow-right">
                    Kelola
                </flux:button>
            </div>
        </div>

        {{-- Kontainer Tampilan Grafik Batang Jurusan --}}
        <div id="jurusan-chart-container" class="p-4 flex-1 flex flex-col justify-between">
            <div class="relative w-full h-[260px]">
                <canvas id="chartJurusan"></canvas>
            </div>
            <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;" class="pt-3 mt-3 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-1.5 text-slate-600">
                    <span class="inline-block size-2 rounded-full bg-slate-400"></span>
                    <span>Total Kuota: <strong class="text-slate-900 font-bold">{{ $totalKuota }}</strong></span>
                </div>
                <div class="flex items-center gap-1.5 text-slate-600">
                    <span class="inline-block size-2 rounded-full bg-blue-600"></span>
                    <span>Pendaftar: <strong class="text-blue-700 font-bold">{{ $totalPendaftarJurusan }}</strong> ({{ $persenKeterisian }}%)</span>
                </div>
                <div class="flex items-center gap-1.5 text-slate-600">
                    <span class="inline-block size-2 rounded-full bg-amber-500"></span>
                    <span>Sisa Kuota: <strong class="text-slate-900 font-bold">{{ $sisaKuotaJurusan }}</strong> kursi</span>
                </div>
            </div>
        </div>

        {{-- Kontainer Tampilan Tabel Jurusan (Hidden by default) --}}
        <div id="jurusan-table-container" class="overflow-x-auto hidden">
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

    {{-- Card Grafik Pendaftar per Gelombang --}}
    <flux:card class="p-0 overflow-hidden flex flex-col justify-between">
        <div class="p-4 border-b border-slate-100 flex flex-wrap gap-2 justify-between items-center bg-slate-50/50">
            <div>
                <h2 class="text-sm font-bold text-slate-900 m-0">
                    Pendaftar per Gelombang
                </h2>
                <p class="text-xs text-slate-500 m-0 mt-0.5">Distribusi pendaftar berdasarkan periode seleksi PPDB</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex items-center p-0.5 bg-slate-200/70 rounded-lg text-xs font-semibold">
                    <button type="button" class="px-2 py-1 rounded-md text-slate-800 bg-white shadow-xs font-bold transition flex items-center gap-1 cursor-pointer" id="btn-chart-gelombang" onclick="toggleView('gelombang', 'chart')">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                        <span>Grafik</span>
                    </button>
                    <button type="button" class="px-2 py-1 rounded-md text-slate-500 hover:text-slate-800 transition flex items-center gap-1 cursor-pointer" id="btn-table-gelombang" onclick="toggleView('gelombang', 'table')">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
                        <span>Tabel</span>
                    </button>
                </div>
                @if(auth('web')->user()->isSuperAdmin())
                    <flux:button href="{{ route('admin.gelombang.index') }}" variant="ghost" size="xs" icon-trailing="arrow-right">
                        Kelola
                    </flux:button>
                @endif
            </div>
        </div>

        {{-- Kontainer Tampilan Grafik Batang Gelombang --}}
        <div id="gelombang-chart-container" class="p-4 flex-1 flex flex-col justify-between">
            <div class="relative w-full h-[260px]">
                <canvas id="chartGelombang"></canvas>
            </div>
            <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;" class="pt-3 mt-3 border-t border-slate-100 text-xs">
                @foreach($gelombangs as $g)
                    @php
                        $pctG = $totalPendaftar > 0 ? round(($g->calon_siswa_count / $totalPendaftar) * 100, 1) : 0;
                        $dotColor = $g->is_bidikmisi ? 'bg-amber-500' : ($loop->index === 1 ? 'bg-blue-600' : 'bg-emerald-500');
                    @endphp
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <span class="inline-block size-2.5 rounded-full {{ $dotColor }}"></span>
                        <span>{{ $g->kode_gelombang ?? 'G' . ($loop->index + 1) }}: <strong class="text-slate-900 font-bold">{{ $g->calon_siswa_count }}</strong> ({{ $pctG }}%)</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kontainer Tampilan Tabel Gelombang (Hidden by default) --}}
        <div id="gelombang-table-container" class="overflow-x-auto hidden">
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

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
    function toggleView(type, view) {
        const chartEl = document.getElementById(type + '-chart-container');
        const tableEl = document.getElementById(type + '-table-container');
        const btnChart = document.getElementById('btn-chart-' + type);
        const btnTable = document.getElementById('btn-table-' + type);

        if (!chartEl || !tableEl || !btnChart || !btnTable) return;

        if (view === 'chart') {
            chartEl.classList.remove('hidden');
            tableEl.classList.add('hidden');
            btnChart.classList.add('bg-white', 'text-slate-800', 'shadow-xs', 'font-bold');
            btnChart.classList.remove('text-slate-500');
            btnTable.classList.remove('bg-white', 'text-slate-800', 'shadow-xs', 'font-bold');
            btnTable.classList.add('text-slate-500');
        } else {
            chartEl.classList.add('hidden');
            tableEl.classList.remove('hidden');
            btnTable.classList.add('bg-white', 'text-slate-800', 'shadow-xs', 'font-bold');
            btnTable.classList.remove('text-slate-500');
            btnChart.classList.remove('bg-white', 'text-slate-800', 'shadow-xs', 'font-bold');
            btnChart.classList.add('text-slate-500');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js gagal dimuat');
            return;
        }

        // 1. Inisialisasi Grafik Keterisian Kuota Jurusan (Horizontal Bar Chart)
        const ctxJurusan = document.getElementById('chartJurusan');
        if (ctxJurusan) {
            const jurusanLabels = @json($jurusans->map(fn($j) => $j->kode . ' (' . \Illuminate\Support\Str::limit($j->nama_jurusan, 20) . ')'));
            const jurusanFullNames = @json($jurusans->pluck('nama_jurusan'));
            const jurusanPendaftar = @json($jurusans->pluck('calon_siswa_count'));
            const jurusanKuota = @json($jurusans->pluck('kuota'));

            new Chart(ctxJurusan.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: jurusanLabels,
                    datasets: [
                        {
                            label: 'Pendaftar',
                            data: jurusanPendaftar,
                            backgroundColor: '#0b57d0',
                            hoverBackgroundColor: '#0947a8',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.8,
                        },
                        {
                            label: 'Daya Tampung (Kuota)',
                            data: jurusanKuota,
                            backgroundColor: '#e2e8f0',
                            borderColor: '#cbd5e1',
                            borderWidth: 1,
                            hoverBackgroundColor: '#cbd5e1',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.8,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 7,
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#475569'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#092347',
                            titleFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12,
                                weight: '700'
                            },
                            bodyFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11
                            },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                title: function(items) {
                                    const idx = items[0].dataIndex;
                                    return jurusanFullNames[idx] || items[0].label;
                                },
                                afterBody: function(items) {
                                    const idx = items[0].dataIndex;
                                    const p = jurusanPendaftar[idx] || 0;
                                    const k = jurusanKuota[idx] || 0;
                                    const s = Math.max(0, k - p);
                                    const pct = k > 0 ? Math.round((p / k) * 100) : 0;
                                    return `Sisa Kuota: ${s} kursi\nKeterisian: ${pct}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 10
                                },
                                color: '#64748b',
                                precision: 0
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#1e293b'
                            }
                        }
                    }
                }
            });
        }

        // 2. Inisialisasi Grafik Pendaftar per Gelombang (Horizontal Bar Chart)
        const ctxGelombang = document.getElementById('chartGelombang');
        if (ctxGelombang) {
            const gelombangLabels = @json($gelombangs->pluck('nama_gelombang'));
            const gelombangPendaftar = @json($gelombangs->pluck('calon_siswa_count'));
            const gelombangDates = @json($gelombangs->map(fn($g) => $g->tanggal_mulai->format('d/m/Y') . ' - ' . $g->tanggal_selesai->format('d/m/Y')));
            const gelombangStatus = @json($gelombangs->map(fn($g) => $g->isSedangBerjalan() ? 'Aktif Saat Ini' : ($g->is_aktif ? 'Terjadwal' : 'Nonaktif')));
            const totalSiswa = {{ $totalPendaftar }};

            const paletWarna = ['#f59e0b', '#0b57d0', '#10b981', '#6366f1', '#ec4899'];
            const paletHover = ['#d97706', '#0947a8', '#059669', '#4f46e5', '#db2777'];

            new Chart(ctxGelombang.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: gelombangLabels,
                    datasets: [{
                        label: 'Jumlah Pendaftar',
                        data: gelombangPendaftar,
                        backgroundColor: gelombangLabels.map((_, i) => paletWarna[i % paletWarna.length]),
                        hoverBackgroundColor: gelombangLabels.map((_, i) => paletHover[i % paletHover.length]),
                        borderRadius: 6,
                        barPercentage: 0.65,
                        categoryPercentage: 0.8,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#092347',
                            titleFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12,
                                weight: '700'
                            },
                            bodyFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11
                            },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(item) {
                                    return `Pendaftar: ${item.formattedValue} calon siswa`;
                                },
                                afterBody: function(items) {
                                    const idx = items[0].dataIndex;
                                    const count = items[0].raw || 0;
                                    const pct = totalSiswa > 0 ? Math.round((count / totalSiswa) * 100) : 0;
                                    return `Rentang: ${gelombangDates[idx] || '-'}\nStatus: ${gelombangStatus[idx] || '-'}\nProporsi: ${pct}% dari total`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 10
                                },
                                color: '#64748b',
                                precision: 0
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#1e293b'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
