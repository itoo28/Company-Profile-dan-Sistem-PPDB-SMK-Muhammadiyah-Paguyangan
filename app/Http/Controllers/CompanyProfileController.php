<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Ekstrakurikuler;
use App\Models\Fasilitas;
use App\Models\Galeri;
use App\Models\InformasiSekolah;
use App\Models\JenisBerkas;
use App\Models\PengaturanSistem;
use App\Models\Pengumuman;
use App\Models\Prestasi;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    /**
     * Halaman Beranda Utama
     */
    public function index()
    {
        $pengaturan = PengaturanSistem::instance();
        $tahunAjaran = TahunAjaran::aktif() ?? new TahunAjaran(['nama' => '2026/2027']);

        $sambutan = InformasiSekolah::getByKey('sambutan_kepala_sekolah');
        $programKeahlian = InformasiSekolah::getByKey('program_keahlian');

        $beritaTerbaru = Berita::terbit()
            ->latest('tanggal_terbit')
            ->take(3)
            ->get();

        $prestasiTerbaru = Prestasi::terbit()
            ->latest('tanggal_perolehan')
            ->take(3)
            ->get();

        $pengumumanTerbaru = Pengumuman::terbit()
            ->latest('tanggal')
            ->take(3)
            ->get();

        $fasilitasUtama = Fasilitas::orderBy('urutan')
            ->take(4)
            ->get();

        return view('pages.beranda', compact(
            'pengaturan',
            'tahunAjaran',
            'sambutan',
            'programKeahlian',
            'beritaTerbaru',
            'prestasiTerbaru',
            'pengumumanTerbaru',
            'fasilitasUtama'
        ));
    }

    /**
     * Halaman Visi, Misi, dan Tujuan
     */
    public function visiMisi()
    {
        $visi = InformasiSekolah::getByKey('visi');
        $misi = InformasiSekolah::getByKey('misi');
        $tujuan = InformasiSekolah::getByKey('tujuan');
        $sambutan = InformasiSekolah::getByKey('sambutan_kepala_sekolah');

        return view('pages.visi-misi', compact('visi', 'misi', 'tujuan', 'sambutan'));
    }

    /**
     * Halaman Fasilitas Sekolah
     */
    public function fasilitas(Request $request)
    {
        $kategori = $request->query('kategori');
        $query = Fasilitas::orderBy('urutan');

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $fasilitas = $query->get();
        $kategoriList = Fasilitas::select('kategori')->distinct()->pluck('kategori');

        return view('pages.fasilitas', compact('fasilitas', 'kategoriList', 'kategori'));
    }

    /**
     * Halaman Ekstrakurikuler
     */
    public function ekstrakurikuler()
    {
        $ekskul = Ekstrakurikuler::all();
        return view('pages.ekstrakurikuler', compact('ekskul'));
    }

    /**
     * Halaman Daftar Berita Terbit
     */
    public function berita(Request $request)
    {
        $cari = $request->query('q');
        $query = Berita::terbit()->latest('tanggal_terbit');

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                  ->orWhere('ringkasan', 'like', "%{$cari}%")
                  ->orWhere('isi', 'like', "%{$cari}%");
            });
        }

        $beritaList = $query->paginate(6)->withQueryString();

        return view('pages.berita.index', compact('beritaList', 'cari'));
    }

    /**
     * Halaman Detail Berita
     */
    public function detailBerita(string $slug)
    {
        // Hanya yang berstatus terbit yang boleh dibuka (BR-11, 404 jika draf)
        $berita = Berita::terbit()->where('slug', $slug)->firstOrFail();

        // Increment counter penayangan
        $berita->increment('views_count');

        $beritaLainnya = Berita::terbit()
            ->where('id', '!=', $berita->id)
            ->latest('tanggal_terbit')
            ->take(3)
            ->get();

        return view('pages.berita.show', compact('berita', 'beritaLainnya'));
    }

    /**
     * Halaman Pengumuman
     */
    public function pengumuman()
    {
        $pengumumanList = Pengumuman::terbit()
            ->latest('tanggal')
            ->paginate(10);

        return view('pages.pengumuman', compact('pengumumanList'));
    }

    /**
     * Halaman Prestasi
     */
    public function prestasi(Request $request)
    {
        $tingkat = $request->query('tingkat');
        $query = Prestasi::terbit()->latest('tanggal_perolehan');

        if ($tingkat) {
            $query->where('tingkat', $tingkat);
        }

        $prestasiList = $query->paginate(6)->withQueryString();
        $tingkatList = Prestasi::terbit()->select('tingkat')->distinct()->pluck('tingkat');

        return view('pages.prestasi.index', compact('prestasiList', 'tingkatList', 'tingkat'));
    }

    /**
     * Halaman Detail Prestasi
     */
    public function detailPrestasi(string $slug)
    {
        $prestasi = Prestasi::terbit()->where('slug', $slug)->firstOrFail();

        $prestasiLainnya = Prestasi::terbit()
            ->where('id', '!=', $prestasi->id)
            ->latest('tanggal_perolehan')
            ->take(3)
            ->get();

        return view('pages.prestasi.show', compact('prestasi', 'prestasiLainnya'));
    }

    /**
     * Halaman Galeri Foto
     */
    public function galeri()
    {
        $galeriList = Galeri::latest()->paginate(9);
        return view('pages.galeri', compact('galeriList'));
    }

    /**
     * Halaman Info Alur dan Persyaratan PPDB (FR-19)
     */
    public function ppdb()
    {
        $pengaturan = PengaturanSistem::instance();
        $tahunAjaran = TahunAjaran::aktif() ?? new TahunAjaran(['nama' => '2026/2027']);
        $jenisBerkas = JenisBerkas::orderBy('urutan')->get();

        return view('pages.ppdb-info', compact('pengaturan', 'tahunAjaran', 'jenisBerkas'));
    }

    /**
     * Halaman Kontak & Lokasi (FR-20)
     */
    public function kontak()
    {
        $pengaturan = PengaturanSistem::instance();
        return view('pages.kontak', compact('pengaturan'));
    }

    /**
     * Penanganan Pesan Pengunjung dari Formulir Kontak (FR-20 & FR-48)
     */
    public function kirimKontak(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kontak' => 'required|string|max:100',
            'subjek' => 'required|string|max:150',
            'pesan' => 'required|string|max:1000',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'kontak.required' => 'Nomor WhatsApp atau alamat email wajib diisi.',
            'subjek.required' => 'Subjek pertanyaan wajib diisi.',
            'pesan.required' => 'Isi pesan atau pertanyaan wajib diisi.',
        ]);

        \App\Models\PesanMasuk::create([
            'nama' => $validated['nama'],
            'kontak' => $validated['kontak'],
            'subjek' => $validated['subjek'],
            'pesan' => $validated['pesan'],
            'status' => 'belum_dibaca',
        ]);

        return redirect()->route('kontak')->with('sukses', 'Terima kasih, pesan Anda telah terkirim. Petugas kami akan segera menindaklanjuti.');
    }
}
