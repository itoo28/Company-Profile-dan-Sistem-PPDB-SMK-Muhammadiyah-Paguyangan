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
Route::post('/kontak', [CompanyProfileController::class, 'kirimKontak'])->name('kontak.kirim');

// Rute Pendaftaran PPDB & Portal (Tahap T2 yang akan datang, diberikan tampilan pengantar informatif)
Route::get('/ppdb/daftar', function () {
    return view('pages.ppdb-daftar-info');
})->name('ppdb.daftar');

Route::get('/siswa/login', function () {
    return view('pages.siswa-login-info');
})->name('siswa.login');

Route::get('/admin/login', function () {
    return view('pages.admin-login-info');
})->name('admin.login');
