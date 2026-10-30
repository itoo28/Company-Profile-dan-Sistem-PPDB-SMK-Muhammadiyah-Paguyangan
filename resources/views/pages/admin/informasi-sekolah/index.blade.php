@extends('layouts.admin')

@section('title', 'Manajemen Informasi Profil Sekolah')
@section('header-title', 'Manajemen Profil Visi, Misi & Sambutan Pimpinan')
@section('header-subtitle', 'Kelola haluan visi misi, tujuan pendidikan, dan amanah sambutan kepala sekolah yang tampil pada halaman profil publik.')

@section('subnav')
    @include('partials.admin.profil-tabs')
@endsection

@section('content')
<div class="max-w-4xl">
    <flux:card class="p-6">
        <form action="{{ route('admin.informasi-sekolah.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- 1. Bagian Visi Sekolah --}}
            <div class="mb-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-2 mb-3">
                    <div class="size-7 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">1</div>
                    <h2 class="text-sm font-bold text-slate-900 m-0">Visi SMK Muhammadiyah Paguyangan</h2>
                </div>
                <div class="text-xs text-slate-500 mb-2">Teks pernyataan visi utama yang tampil di beranda dan halaman profil sekolah.</div>
                <textarea name="visi" id="visi" class="form-control" rows="3" required>{{ old('visi', $visi->konten ?? 'Terwujudnya SMK Muhammadiyah Paguyangan sebagai pusat keunggulan pendidikan vokasi yang berakhlak mulia, unggul dalam kompetensi kejuruan, mandiri, dan berdaya saing di dunia industri nasional.') }}</textarea>
            </div>

            {{-- 2. Bagian Misi Sekolah --}}
            <div class="mb-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-2 mb-3">
                    <div class="size-7 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">2</div>
                    <h2 class="text-sm font-bold text-slate-900 m-0">Misi Sekolah</h2>
                </div>
                <div class="text-xs text-slate-500 mb-2">Poin-poin misi pelaksanaan pendidikan (bisa format paragraf atau tag daftar HTML &lt;ol&gt;&lt;li&gt;).</div>
                <textarea name="misi" id="misi" class="form-control" rows="5" required>{{ old('misi', $misi->konten ?? '') }}</textarea>
            </div>

            {{-- 3. Bagian Tujuan Pendidikan --}}
            <div class="mb-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-2 mb-3">
                    <div class="size-7 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">3</div>
                    <h2 class="text-sm font-bold text-slate-900 m-0">Tujuan Pendidikan</h2>
                </div>
                <div class="text-xs text-slate-500 mb-2">Target dan capaian kompetensi lulusan peserta didik.</div>
                <textarea name="tujuan" id="tujuan" class="form-control" rows="4" required>{{ old('tujuan', $tujuan->konten ?? '') }}</textarea>
            </div>

            {{-- 4. Bagian Sambutan Kepala Sekolah --}}
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="size-7 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">4</div>
                    <h2 class="text-sm font-bold text-slate-900 m-0">Kepemimpinan & Sambutan Kepala Sekolah</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start mb-4">
                    <div class="md:col-span-1">
                        @php
                            $fotoKepala = !empty($sambutan->gambar) 
                                ? (str_starts_with($sambutan->gambar, 'images/') || str_starts_with($sambutan->gambar, 'storage/') ? asset($sambutan->gambar) : asset('storage/' . $sambutan->gambar)) 
                                : asset('images/kepala-sekolah.jpg');
                        @endphp
                        <label class="form-label mb-2">Foto Resmi Kepala Sekolah</label>
                        <div class="rounded-lg overflow-hidden border border-slate-200 bg-slate-50 p-2 mb-2 text-center">
                            <img src="{{ $fotoKepala }}" alt="Kepala Sekolah" class="w-full h-48 object-cover object-top rounded">
                        </div>
                        <input type="file" name="foto_kepala_sekolah" id="foto_kepala_sekolah" class="form-control text-xs" accept="image/jpeg,image/png,image/jpg,image/webp">
                        <div class="text-[11px] text-slate-400 mt-1">Maks. 2 MB (JPG/PNG). Kosongkan jika tidak diganti.</div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="mb-3">
                            <label for="nama_kepala_sekolah" class="form-label">Nama Lengkap & Gelar Kepala Sekolah <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kepala_sekolah" id="nama_kepala_sekolah" class="form-control" value="{{ old('nama_kepala_sekolah', $sambutan->extra_data['nama_kepala_sekolah'] ?? 'Mohammad Ardianshah, S.Kom') }}" required>
                        </div>

                        <div>
                            <label for="sambutan" class="form-label">Teks Amanah / Sambutan Kepala Sekolah <span class="text-rose-500">*</span></label>
                            <textarea name="sambutan" id="sambutan" class="form-control" rows="8" placeholder="Tuliskan kata sambutan pimpinan kepada orang tua dan calon siswa..." required>{{ old('sambutan', $sambutan->konten ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <flux:button type="submit" variant="primary" size="base">
                    Simpan Seluruh Informasi Profil
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection

