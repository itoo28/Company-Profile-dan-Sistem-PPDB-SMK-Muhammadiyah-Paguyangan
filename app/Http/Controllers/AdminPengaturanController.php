<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\PengaturanSistem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanSistem::instance();

        return view('pages.admin.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('web')->user();
        $pengaturan = PengaturanSistem::instance();

        $validated = $request->validate([
            'batas_waktu_berkas' => ['nullable', 'date'],
            'max_file_size_mb' => ['required', 'integer', 'min:1', 'max:20'],
            'kuota_promo_benefit' => ['required', 'integer', 'min:0', 'max:1000'],
            'is_promo_aktif' => ['nullable', 'boolean'],
            'nama_sekolah' => ['required', 'string', 'max:150'],
            'slogan' => ['nullable', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:500'],
            'telepon' => ['required', 'string', 'max:50'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100'],
            'jam_operasional' => ['required', 'string', 'max:200'],
            'maps_embed_url' => ['nullable', 'string'],
        ]);

        $validated['is_promo_aktif'] = $request->boolean('is_promo_aktif');

        $pengaturan->update($validated);

        LogAktivitas::catat($user, 'ubah_pengaturan', 'Pengaturan Sistem', 'Super Admin memperbarui konfigurasi operasional PPDB dan identitas sekolah.');

        return back()->with('sukses', 'Pengaturan PPDB dan informasi identitas sekolah berhasil disimpan.');
    }
}
