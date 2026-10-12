# Company-Profile-dan-Sistem-PPDB-SMK-Muhammadiyah-Paguyangan

Sistem web Company Profile dan PPDB SMK Muhammadiyah Paguyangan berbasis Laravel & MySQL. Memuat profil sekolah publik, portal registrasi daring, verifikasi berkas digital, loket seragam, serta panel admin terpadu untuk staf dan calon siswa.

---

## 📌 Fitur Utama

1. **Company Profile (Halaman Publik)**
   - Profil sekolah, visi & misi, fasilitas, dan ekstrakurikuler.
   - Program keahlian / kejuruan.
   - Publikasi berita sekolah, pengumuman, prestasi siswa, dan galeri kegiatan.
   - Informasi panduan PPDB dan kontak sekolah.

2. **Portal Calon Siswa (PPDB Daring)**
   - Pendaftaran akun mandiri calon siswa baru.
   - Pengisian biodata dan unggah berkas persyaratan secara bertahap.
   - Pemantauan status verifikasi berkas secara real-time.
   - Unduh dan cetak kartu bukti pendaftaran.

3. **Panel Admin Piket**
   - Pelayanan pendaftaran calon siswa secara offline / di tempat.
   - Verifikasi berkas fisik maupun digital.
   - Pengelolaan loket pengambilan seragam sekolah.
   - Manajemen publikasi berita dan prestasi.

4. **Panel Super Admin**
   - Manajemen tahun ajaran aktif, kuota pendaftaran, dan gelombang PPDB.
   - Manajemen akun staf dan kontrol hak akses peran (*roles*).
   - Ekspor rekapitulasi data pendaftar ke format Excel dan PDF.
   - Konfigurasi sistem umum dan audit log aktivitas.

---

## 🛠️ Tech Stack

- **Backend Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL
- **Frontend:** Blade Templating, Vanilla CSS, JavaScript
- **Tools Pendukung:** DomPDF, Maatwebsite Excel, Laragon

---

## 🚀 Panduan Instalasi Lokal

### 1. Klon Repositori
```bash
git clone https://github.com/itoo28/Company-Profile-dan-Sistem-PPDB-SMK-Muhammadiyah-Paguyangan.git
cd Company-Profile-dan-Sistem-PPDB-SMK-Muhammadiyah-Paguyangan
```

### 2. Pasang Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin berkas sampel `.env.example`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi basis data pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smk_mutupa
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Migrasi dan Seeder Basis Data
```bash
php artisan migrate --seed
```

### 6. Buat Storage Link
```bash
php artisan storage:link
```

### 7. Jalankan Server Pengembangan
```bash
php artisan serve
```
Akses aplikasi melalui peramban web pada alamat `http://127.0.0.1:8000`.

---

## 👥 Akun Pengujian Default (Seeder)

| Peran | Username | Password Default |
|---|---|---|
| **Super Admin** | `superadmin` | `admin123` |
| **Admin Piket** | `adminpiket` | `piket123` |

> *Catatan: Ubah kredensial default sebelum melakukan publikasi atau deployment ke lingkungan produksi.*
>>>>>>> 998275f (feat: inisialisasi project Company Profile dan PPDB SMK Muhammadiyah Paguyangan)
