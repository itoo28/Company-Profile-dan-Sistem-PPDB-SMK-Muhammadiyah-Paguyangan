<?php

namespace App\Http\Controllers;

use App\Models\InformasiSekolah;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminInformasiSekolahController extends Controller
{
    public function index()
    {
        $visi = InformasiSekolah::getByKey('visi');
        $misi = InformasiSekolah::getByKey('misi');
        $tujuan = InformasiSekolah::getByKey('tujuan');
        $sambutan = InformasiSekolah::getByKey('sambutan_kepala_sekolah');

        return view('pages.admin.informasi-sekolah.index', compact('visi', 'misi', 'tujuan', 'sambutan'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'visi' => ['required', 'string'],
            'misi' => ['required', 'string'],
            'tujuan' => ['required', 'string'],
            'nama_kepala_sekolah' => ['required', 'string', 'max:150'],
            'sambutan' => ['required', 'string'],
            'foto_kepala_sekolah' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Sanitasi konten teks kaya dari potensi Stored XSS (SEC-05)
        $validated['visi'] = $this->sanitizeHtml($validated['visi']);
        $validated['misi'] = $this->sanitizeHtml($validated['misi']);
        $validated['tujuan'] = $this->sanitizeHtml($validated['tujuan']);
        $validated['sambutan'] = $this->sanitizeHtml($validated['sambutan']);

        // 1. Simpan Visi
        InformasiSekolah::updateOrCreate(
            ['key' => 'visi'],
            ['judul' => 'Visi Sekolah', 'konten' => $validated['visi']]
        );

        // 2. Simpan Misi
        InformasiSekolah::updateOrCreate(
            ['key' => 'misi'],
            ['judul' => 'Misi Sekolah', 'konten' => $validated['misi']]
        );

        // 3. Simpan Tujuan
        InformasiSekolah::updateOrCreate(
            ['key' => 'tujuan'],
            ['judul' => 'Tujuan Pendidikan Sekolah', 'konten' => $validated['tujuan']]
        );

        // 4. Simpan Sambutan Kepala Sekolah
        $sambutanRecord = InformasiSekolah::getByKey('sambutan_kepala_sekolah');
        $fotoPath = $sambutanRecord?->gambar ?? 'images/kepala-sekolah.jpg';

        if ($request->hasFile('foto_kepala_sekolah')) {
            if ($sambutanRecord && !empty($sambutanRecord->gambar) && !str_starts_with($sambutanRecord->gambar, 'images/')) {
                $rawStoragePath = str_replace('storage/', '', $sambutanRecord->gambar);
                if (Storage::disk('public')->exists($rawStoragePath)) {
                    Storage::disk('public')->delete($rawStoragePath);
                }
            }
            $uploaded = $request->file('foto_kepala_sekolah')->store('kepala_sekolah', 'public');
            $fotoPath = 'storage/' . $uploaded;
        }

        InformasiSekolah::updateOrCreate(
            ['key' => 'sambutan_kepala_sekolah'],
            [
                'judul' => 'Sambutan Kepala Sekolah',
                'konten' => $validated['sambutan'],
                'gambar' => $fotoPath,
                'extra_data' => [
                    'nama_kepala_sekolah' => $validated['nama_kepala_sekolah'],
                ],
            ]
        );

        LogAktivitas::catat($user, 'ubah_informasi_sekolah', 'Profil Sekolah', 'Super Admin memperbarui informasi Visi, Misi, Tujuan, dan Sambutan Kepala Sekolah.');

        return back()->with('sukses', 'Profil sekolah, Visi Misi, dan Sambutan Kepala Sekolah berhasil disimpan.');
    }

    /**
     * Sanitasi konten HTML dari potensi serangan Stored XSS (SEC-05)
     */
    protected function sanitizeHtml(string $html): string
    {
        // 1. Izinkan hanya tag HTML dasar yang aman untuk teks informasi sekolah
        $clean = strip_tags($html, '<p><br><strong><em><b><i><u><ul><ol><li><a><blockquote><h2><h3><h4>');

        // 2. Bersihkan event handler inline (seperti onload, onerror, onclick, onmouseover, dsb.)
        $clean = preg_replace('/(\son[a-zA-Z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+))/i', '', $clean);

        // 3. Netralkan protokol berbahaya pada atribut href (seperti javascript:, vbscript:, data:)
        $clean = preg_replace('/href\s*=\s*(["\'])\s*(?:java\s*script|vb\s*script|data):.*?\1/i', 'href="#"', $clean);

        return $clean;
    }
}

