<?php

use App\Http\Controllers\CompanyProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SMK Muhammadiyah Paguyangan
| Modul Company Profile & Info PPDB (Tahap T1)
|--------------------------------------------------------------------------
*/

// Halaman Publik (Company Profile)
Route::get('/', [CompanyProfileController::class, 'index'])->name('beranda');
Route::get('/visi-misi', [CompanyProfileController::class, 'visiMisi'])->name('visi-misi');
Route::get('/fasilitas', [CompanyProfileController::class, 'fasilitas'])->name('fasilitas');
Route::get('/ekstrakurikuler', [CompanyProfileController::class, 'ekstrakurikuler'])->name('ekstrakurikuler');

Route::get('/berita', [CompanyProfileController::class, 'berita'])->name('berita');
Route::get('/berita/{slug}', [CompanyProfileController::class, 'detailBerita'])->name('berita.detail');

Route::get('/pengumuman', [CompanyProfileController::class, 'pengumuman'])->name('pengumuman');

Route::get('/prestasi', [CompanyProfileController::class, 'prestasi'])->name('prestasi');
Route::get('/prestasi/{slug}', [CompanyProfileController::class, 'detailPrestasi'])->name('prestasi.detail');

Route::get('/galeri', [CompanyProfileController::class, 'galeri'])->name('galeri');

Route::get('/ppdb', [CompanyProfileController::class, 'ppdb'])->name('ppdb.info');
Route::get('/kontak', [CompanyProfileController::class, 'kontak'])->name('kontak');
Route::post('/kontak', [CompanyProfileController::class, 'kirimKontak'])
    ->middleware('throttle:5,1')
    ->name('kontak.kirim');

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminBeritaController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminEksporController;
use App\Http\Controllers\AdminGelombangController;
use App\Http\Controllers\AdminJenisBerkasController;
use App\Http\Controllers\AdminJurusanController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AdminPembayaranController;
use App\Http\Controllers\AdminPendaftarController;
use App\Http\Controllers\AdminPengaturanController;
use App\Http\Controllers\AdminPesanMasukController;
use App\Http\Controllers\AdminPrestasiController;
use App\Http\Controllers\AdminSeragamController;
use App\Http\Controllers\AdminStafController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\SiswaAuthController;
use App\Http\Controllers\SiswaPortalController;

// Rute Pendaftaran PPDB Publik (Fase 1 - FR-04, FR-05, FR-22)
Route::get('/ppdb/daftar', [PendaftaranController::class, 'daftar'])->name('ppdb.daftar');
Route::post('/ppdb/daftar', [PendaftaranController::class, 'simpan'])->name('ppdb.daftar.simpan');
Route::get('/ppdb/berhasil', [PendaftaranController::class, 'berhasil'])->name('ppdb.berhasil');
Route::get('/ppdb/bukti/{id}', [PendaftaranController::class, 'cetakBukti'])->name('ppdb.bukti');

// Portal Calon Siswa (Fase 2 - FR-06, FR-07, FR-08, FR-23)
Route::get('/siswa/login', [SiswaAuthController::class, 'login'])->name('siswa.login');
Route::post('/siswa/login', [SiswaAuthController::class, 'authenticate'])->name('siswa.login.proses');
Route::post('/siswa/logout', [SiswaAuthController::class, 'logout'])->name('siswa.logout');

Route::middleware('auth:siswa')->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/berkas/upload', [SiswaPortalController::class, 'uploadBerkas'])->name('berkas.upload');
    Route::get('/berkas/{id}', [SiswaPortalController::class, 'lihatBerkas'])->name('berkas.lihat');
    Route::post('/pembayaran/konfirmasi', [SiswaPortalController::class, 'konfirmasiPembayaran'])->name('pembayaran.konfirmasi');
    Route::get('/pembayaran/bukti', [SiswaPortalController::class, 'lihatBuktiPembayaran'])->name('pembayaran.bukti');
    Route::get('/pembayaran/kuitansi', [SiswaPortalController::class, 'cetakKuitansi'])->name('pembayaran.kuitansi');
    Route::get('/bukti', function () {
        $siswa = auth('siswa')->user();
        return app(PendaftaranController::class)->cetakBukti($siswa->id);
    })->name('bukti');
});

// Portal Administrasi Sekolah (Staf & Super Admin - FR-33, FR-34)
Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'authenticate'])->name('admin.login.proses');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Area Administrasi Terproteksi (Admin Piket & Super Admin)
Route::middleware(['auth:web', 'role:super_admin,admin_piket'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Calon Siswa & Verifikasi (FR-11, FR-12, FR-13, FR-26)
    Route::get('/pendaftar', [AdminPendaftarController::class, 'index'])->name('pendaftar.index');
    Route::get('/pendaftar/baru', [AdminPendaftarController::class, 'createOffline'])->name('pendaftar.baru');
    Route::post('/pendaftar/baru', [AdminPendaftarController::class, 'storeOffline'])->name('pendaftar.baru.simpan');
    Route::get('/pendaftar/{id}', [AdminPendaftarController::class, 'show'])->name('pendaftar.show');
    Route::post('/pendaftar/berkas/{berkasId}/verifikasi', [AdminPendaftarController::class, 'verifikasiBerkas'])->name('pendaftar.berkas.verifikasi');
    Route::get('/berkas/{id}', [AdminPendaftarController::class, 'lihatBerkas'])->name('berkas.lihat');

    // Manajemen Pembayaran PPDB Semi-Manual (Verifikasi Transfer & Loket Tunai)
    Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/{id}/verifikasi', [AdminPembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::post('/pembayaran/bayar-tunai', [AdminPembayaranController::class, 'bayarTunai'])->name('pembayaran.tunai');
    Route::get('/pembayaran/{id}/kuitansi', [AdminPembayaranController::class, 'cetakKuitansi'])->name('pembayaran.kuitansi');
    Route::get('/pembayaran/{id}/bukti', [AdminPembayaranController::class, 'lihatBukti'])->name('pembayaran.bukti');

    // Loket Seragam (FR-14, FR-27)
    Route::get('/seragam', [AdminSeragamController::class, 'index'])->name('seragam.index');
    Route::post('/seragam', [AdminSeragamController::class, 'store'])->name('seragam.store');

    // Berita & Prestasi (Admin Piket dan Super Admin - FR-37 s.d. FR-42)
    Route::resource('berita', AdminBeritaController::class);
    Route::resource('prestasi', AdminPrestasiController::class);

    // Area Khusus Super Admin (FR-15 s.d. FR-18, FR-29 s.d. FR-32, FR-45 s.d. FR-48)
    Route::middleware('role:super_admin')->group(function () {
        Route::post('/pendaftar/{id}/unlock', [AdminPendaftarController::class, 'unlock'])->name('pendaftar.unlock');
        Route::post('/pendaftar/{id}/koreksi', [AdminPendaftarController::class, 'koreksi'])->name('pendaftar.koreksi');
        Route::delete('/pendaftar/{id}', [AdminPendaftarController::class, 'destroy'])->name('pendaftar.destroy');

        Route::delete('/seragam/{id}', [AdminSeragamController::class, 'destroy'])->name('seragam.destroy');

        Route::resource('gelombang', AdminGelombangController::class)->except(['create', 'show', 'edit']);
        Route::resource('jurusan', AdminJurusanController::class)->except(['create', 'show', 'edit']);
        Route::resource('jenis-berkas', AdminJenisBerkasController::class)->except(['create', 'show', 'edit']);

        // Pesan Masuk Pengunjung Kontak (FR-48)
        Route::get('/pesan-masuk', [AdminPesanMasukController::class, 'index'])->name('pesan-masuk.index');
        Route::patch('/pesan-masuk/{id}/status', [AdminPesanMasukController::class, 'updateStatus'])->name('pesan-masuk.status');
        Route::patch('/pesan-masuk/{id}/update-status', [AdminPesanMasukController::class, 'updateStatus'])->name('pesan-masuk.update-status');
        Route::delete('/pesan-masuk/{id}', [AdminPesanMasukController::class, 'destroy'])->name('pesan-masuk.destroy');

        // Pengaturan Sistem & PPDB (FR-18)
        Route::get('/pengaturan', [AdminPengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan', [AdminPengaturanController::class, 'update'])->name('pengaturan.update');

        // Ekspor Data PPDB (FR-16)
        Route::get('/ekspor', [AdminEksporController::class, 'index'])->name('ekspor.index');
        Route::get('/ekspor/csv', [AdminEksporController::class, 'eksporCsv'])->name('ekspor.csv');
        Route::get('/ekspor/excel', [AdminEksporController::class, 'eksporExcel'])->name('ekspor.excel');
        Route::get('/ekspor/cetak', [AdminEksporController::class, 'eksporCetak'])->name('ekspor.cetak');

        // Manajemen Staf (FR-18, FR-35)
        Route::get('/staf', [AdminStafController::class, 'index'])->name('staf.index');
        Route::post('/staf', [AdminStafController::class, 'store'])->name('staf.store');
        Route::put('/staf/{id}', [AdminStafController::class, 'update'])->name('staf.update');
        Route::delete('/staf/{id}', [AdminStafController::class, 'destroy'])->name('staf.destroy');
        Route::patch('/staf/{id}/toggle', [AdminStafController::class, 'toggleAktif'])->name('staf.toggle');
        Route::patch('/staf/{id}/reset-password', [AdminStafController::class, 'resetPassword'])->name('staf.reset');

        // Log Aktivitas (FR-31)
        Route::get('/log', [AdminLogController::class, 'index'])->name('log.index');
    });
});
