# LAPORAN PENERAPAN METODOLOGI AGILE (SCRUM)
## Rancang Bangun Sistem Informasi Web Company Profile dan Penerimaan Peserta Didik Baru (PPDB) SMK Muhammadiyah Paguyangan

---

**Identitas Penyusun:**  
- **Nama:** Suwito  
- **NIM:** 240111013  

**Informasi Akademik:**  
- **Mata Kuliah:** Proyek Perangkat Lunak (Semester 5)  
- **Program Studi:** Teknik Informatika / Rekayasa Perangkat Lunak  
- **Topik:** Metodologi Pengembangan Perangkat Lunak Agile: Scrum Framework  
- **Studi Kasus:** SMK Muhammadiyah Paguyangan, Kab. Brebes  
- **Tahun Akademik:** 2025/2026  

---

## DAFTAR ISI
1. [BAB I: PENDAHULUAN](#bab-i-pendahuluan)
   - 1.1 Latar Belakang Masalah
   - 1.2 Tujuan Penerapan Metodologi Agile
   - 1.3 Ruang Lingkup Sistem
2. [BAB II: LANDASAN TEORI & METODOLOGI AGILE](#bab-ii-landasan-teori--metodologi-agile)
   - 2.1 Konsep Dasar Agile & Scrum
   - 2.2 Alasan Pemilihan Agile Scrum dibanding Waterfall
   - 2.3 Struktur Tim & Pembagian Peran (*Scrum Roles*)
3. [BAB III: PRODUCT BACKLOG & ARTEFAK SCRUM](#bab-iii-product-backlog--artefak-scrum)
   - 3.1 Identifikasi Kebutuhan & User Stories
   - 3.2 Product Backlog Items (PBI) & Estimasi Story Points
   - 3.3 Definition of Ready (DoR) & Definition of Done (DoD)
4. [BAB IV: PERENCANAAN DAN EKSEKUSI SPRINT (SPRINT ROADMAP)](#bab-iv-perencanaan-dan-eksekusi-sprint-sprint-roadmap)
   - 4.1 Sprint 0: Inisialisasi Fondasi & Konfigurasi Lingkungan
   - 4.2 Sprint 1: Modul Company Profile & Halaman Publik
   - 4.3 Sprint 2: Portal Registrasi Calon Siswa & Bukti PDF
   - 4.4 Sprint 3: Manajemen Berkas Bertahap, Server-Side Timer, & Lockdown
   - 4.5 Sprint 4: Operasional Admin Piket, Verifikasi, & Loket Seragam
   - 4.6 Sprint 5: Super Admin Console, Master Data, Ekspor, & Audit Trail
   - 4.7 Sprint 6: System Hardening, Regression Testing, & UAT
5. [BAB V: SEREMONIAL SCRUM & KONTROL KUALITAS](#bab-v-seremonial-scrum--kontrol-kualitas)
   - 5.1 Pelaksanaan Ritual Scrum
   - 5.2 Strategi Pengujian Otomatis & Regression Guard
   - 5.3 Manajemen Risiko dan Mitigasi
6. [BAB VI: PENUTUP](#bab-vi-penutup)
   - 6.1 Kesimpulan
   - 6.2 Saran dan Pengembangan Lanjutan

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang Masalah
SMK Muhammadiyah Paguyangan merupakan salah satu institusi pendidikan kejuruan yang memiliki 5 konsentrasi keahlian: Teknik Kendaraan Ringan, Teknik Sepeda Motor, Teknik Ototronik, Rekayasa Perangkat Lunak, dan Broadcasting (Produksi Film). Pada era digital saat ini, kebutuhan transparansi informasi profil sekolah serta efisiensi proses Penerimaan Peserta Didik Baru (PPDB) menjadi krusial. 

Proses PPDB konvensional seringkali menghadapi kendala seperti:
- Antrean fisik pendaftar yang menumpuk di loket sekolah.
- Berkas fisik persyaratan yang rawan hilang, rusak, atau tercecer.
- Validasi berkas persyaratan yang lambat karena pencatatan manual.
- Sulitnya mengendalikan hak pengambilan seragam bagi siswa yang belum melengkapi seluruh berkas wajib.
- Keterbatasan publikasi keunggulan jurusan, prestasi siswa, dan berita sekolah kepada calon siswa dan wali murid.

Untuk menjawab permasalahan tersebut, dirancanglah sebuah aplikasi web terpadu satu basis data yang menggabungkan fungsi **Company Profile** dinamis dan **Sistem PPDB Digital** berbasis alur verifikasi bertahap.

### 1.2 Tujuan Penerapan Metodologi Agile
Pengembangan sistem ini mengadopsi metodologi **Agile Scrum** dengan pertimbangan:
1. **Iteratif dan Inkremental:** Modul dapat dirilis dan diuji fungsi demi fungsi secara cepat tanpa harus menunggu seluruh sistem selesai 100%.
2. **Kolaborasi dengan Mitra Sekolah:** Pihak sekolah (Kepala Sekolah, Panitia PPDB) dapat memberikan umpan balik (*feedback*) pada setiap akhir sprint sehingga sistem yang dibangun benar-benar sesuai dengan SOP di lapangan.
3. **Fleksibilitas terhadap Perubahan Aturan:** Perubahan aturan pendaftaran (misalnya kuota promo bebas SPP 3 bulan, tanggal gelombang, atau penyesuaian berkas wajib) dapat diakomodasi dalam *sprint planning* berikutnya tanpa merusak arsitektur yang sudah berjalan.

### 1.3 Ruang Lingkup Sistem
Sistem ini mencakup 4 modul terintegrasi dalam 1 database MySQL:
1. **Halaman Publik (Company Profile):** Beranda, profil sekolah, informasi 5 jurusan, berita, prestasi, alur PPDB, dan kontak publik.
2. **Portal Calon Siswa:** Registrasi online (Jalur Umum & Jalur Prestasi), generate nomor pendaftaran unik, cetak bukti PDF, upload berkas persyaratan dengan batasan waktu, dan pemantauan status verifikasi.
3. **Area Admin Piket:** Pendaftaran offline (calon siswa yang datang langsung), verifikasi berkas (*Approve/Reject* dengan catatan), loket pencatatan pengambilan seragam (*one-time record*), dan manajemen berita/prestasi.
4. **Area Super Admin:** Kontrol penuh konfigurasi tahun ajaran, gelombang pendaftaran, master jenis berkas, pembukaan kunci (*unlock*) siswa yang terkena batas waktu, ekspor data pendaftar (Excel/PDF), audit log aktivitas, dan pengelolaan staf.

---

## BAB II: LANDASAN TEORI & METODOLOGI AGILE

### 2.1 Konsep Dasar Agile & Scrum
**Agile** adalah filosofi pengembangan sistem berbasis manifesto 2001 yang mengedepankan adaptabilitas, kolaborasi aktif, serta perangkat lunak yang berfungsi secara bertahap. **Scrum** adalah framework implementasi Agile yang membagi proyek ke dalam siklus kerja berulang berdurasi tetap yang disebut **Sprint** (biasanya 1 - 4 minggu).

Setiap Sprint menghasilkan *Increment*, yaitu penambahan fungsi baru yang memenuhi *Definition of Done* (DoD) dan berpotensi untuk langsung dirilis (*potentially shippable product*).

### 2.2 Alasan Pemilihan Agile Scrum dibanding Waterfall
Perbandingan karakteristik kedua model pada konteks proyek ini:

| Parameter | Model Waterfall | Model Agile Scrum (Dipilih) |
|---|---|---|
| **Pola Kerja** | Linier dan sekuensial; tahapan berikutnya tidak bisa dimulai sebelum tahap sebelumnya selesai. | Iteratif dan inkremental; fitur dikembangkan dalam siklus pendek yang berkesinambungan. |
| **Respon terhadap Perubahan** | Kaku; perubahan spesifikasi di tengah proyek membutuhkan biaya dan waktu re-analisis tinggi. | Adaptif; prioritas *Product Backlog* dapat disesuaikan pada setiap sesi *Sprint Planning*. |
| **Keterlibatan Klien/Mitra** | Terbatas pada fase awal (analisis kebutuhan) dan akhir (UAT). | Berkelanjutan; klien dan dosen melihat hasil nyata sistem pada setiap *Sprint Review*. |
| **Visibilitas Kemajuan** | Hanya berupa dokumen tebal sampai fase koding/pengujian selesai. | Produk nyata (*working software*) dapat dicoba langsung di akhir tiap Sprint. |
| **Manajemen Risiko** | Risiko kegagalan sistem terakumulasi di akhir proyek. | Risiko terdeteksi lebih awal pada level fitur di setiap akhir sprint. |

### 2.3 Struktur Tim & Pembagian Peran (*Scrum Roles*)
Dalam konteks Tugas Besar Proyek Perangkat Lunak Semester 5, struktur tim kerja dipetakan ke dalam 3 peran utama Scrum:

1. **Product Owner (PO):**
   - *Representasi:* Dosen Pengampu & Mitra Sekolah (SMK Muhammadiyah Paguyangan).
   - *Tanggung Jawab:* Menetapkan visi produk, menyusun dan memprioritaskan *Product Backlog*, serta memberikan persetujuan penerimaan fitur (*acceptance*).
2. **Scrum Master (SM):**
   - *Representasi:* Ketua Tim Pengembang Mahasiswa.
   - *Tanggung Jawab:* Memastikan prinsip Scrum dipatuhi, memoderasi pertemuan tim, memfasilitasi integrasi, dan menyelesaikan hambatan tim (*impediment removal*).
3. **Development Team:**
   - *Backend Developer:* Merancang database migration, seeder, model Eloquent, controller, policy/middleware, dan validasi server-side.
   - *Frontend Developer:* Membangun UI responsif dengan Blade, integrasi komponen, interaktivitas antarmuka, dan konsistensi terhadap panduan desain (`DESIGN.md`).
   - *QA & Test Engineer:* Merancang dan menjalankan pengujian otomatis (*feature & unit test* dengan Pest/PHPUnit), uji regresi, serta dokumentasi pengujian.

---

## BAB III: PRODUCT BACKLOG & ARTEFAK SCRUM

### 3.1 Identifikasi Kebutuhan & User Stories
Kebutuhan fungsional dipetakan ke dalam format *User Story* standar:  
*“Sebagai [Peran], saya ingin [Aksi/Fitur] agar [Manfaat/Tujuan].”*

Beberapa contoh User Story utama:
- **US-01 (Publik):** Sebagai calon siswa/wali murid, saya ingin melihat profil 5 jurusan dan informasi PPDB agar saya memahami prospek keahlian sebelum mendaftar.
- **US-02 (Calon Siswa):** Sebagai calon siswa, saya ingin mendaftar secara daring dan mendapatkan Nomor Pendaftaran unik otomatis serta akun login agar saya dapat memproses pendaftaran dari rumah.
- **US-03 (Calon Siswa):** Sebagai pendaftar awal, saya ingin mengetahui status promo benefit bebas SPP 3 bulan saya agar mendapatkan kepastian hak beasiswa.
- **US-04 (Calon Siswa):** Sebagai calon siswa, saya ingin mengunggah berkas persyaratan dan melihat timer hitung mundur batas waktu agar saya melengkapi berkas tepat waktu.
- **US-05 (Admin Piket):** Sebagai petugas loket, saya ingin mendaftarkan calon siswa yang datang langsung (offline) dan mencetak bukti pendaftaran agar proses pelayanan di sekolah cepat.
- **US-06 (Admin Piket):** Sebagai petugas verifikator, saya ingin memeriksa berkas pendaftar dan memberikan status *Disetujui* atau *Ditolak* beserta alasan agar siswa tahu dokumen yang perlu diperbaiki.
- **US-07 (Admin Piket):** Sebagai petugas seragam, saya ingin memverifikasi kelayakan pengambilan seragam (hanya bagi siswa yang seluruh berkas wajibnya disetujui) dan mencatatnya satu kali agar seragam tidak terambil ganda.
- **US-08 (Super Admin):** Sebagai pimpinan sistem, saya ingin dapat membuka kunci (*unlock*) siswa yang melewati tenggat waktu, mengatur gelombang pendaftaran, dan mengekspor rekap pendaftar ke Excel/PDF untuk laporan dinas/yayasan.

### 3.2 Product Backlog Items (PBI) & Estimasi Story Points
Estimasi menggunakan skala Fibonacci (*Planning Poker*: 1, 2, 3, 5, 8, 13) yang merepresentasikan kompleksitas, volume kerja, dan tingkat risiko teknis:

| ID | Item Backlog (PBI) | FR Terkait | Prioritas | Story Points |
|---|---|---|---|:---:|
| **PBI-01** | Setup arsitektur Laravel, Dual-Guard Auth, Role Middleware, dan Database Seeder | FR-33, FR-34 | P0 (Must) | 8 |
| **PBI-02** | Modul Berita & Prestasi (CRUD + Draf/Terbit) untuk Admin Piket & Super Admin | FR-01, FR-02 | P1 (High) | 5 |
| **PBI-03** | Halaman Publik Company Profile, Profil 5 Jurusan, dan Formulir Kontak Publik | FR-03, FR-46, FR-48 | P1 (High) | 5 |
| **PBI-04** | Form Registrasi Online Mandiri, Generator Nomor Unik, & Gelombang Aktif | FR-04, FR-21, FR-43 | P0 (Must) | 8 |
| **PBI-05** | Alokasi Kuota Benefit Bebas SPP 3 Bulan (100 Pendaftar Pertama) | FR-44, BR-14 | P1 (High) | 5 |
| **PBI-06** | Autentikasi Calon Siswa (Guard Siswa, Rate Limiting, Brute Force Protection) | FR-05, FR-23 | P0 (Must) | 5 |
| **PBI-07** | Cetak Bukti Registrasi PDF (Dompdf) | FR-22 | P1 (High) | 3 |
| **PBI-08** | Portal Upload Berkas Bertahap per Jalur (Umum vs Prestasi) & Validasi MIME/Size | FR-07, FR-08, FR-47 | P0 (Must) | 8 |
| **PBI-09** | Timer Hitung Mundur Server-Side & Mekanisme Auto-Lockdown Upload | FR-10, FR-24, BR-07 | P0 (Must) | 5 |
| **PBI-10** | Pendaftaran Offline oleh Admin Piket & Cetak Bukti Fisik | FR-26, BR-01 | P1 (High) | 5 |
| **PBI-11** | Panel Verifikasi Berkas (Approve/Reject dengan Catatan Koreksi Wajib) | FR-09, FR-11, BR-04 | P0 (Must) | 8 |
| **PBI-12** | Filter Multi-Parameter Pendaftar & Pencarian Cepat Data Siswa | FR-12, FR-13 | P1 (High) | 5 |
| **PBI-13** | Loket Clearance Seragam (Validasi Berkas Lengkap & Catat Ambil Satu Kali) | FR-14, FR-27, BR-05 | P0 (Must) | 5 |
| **PBI-14** | Master Data Gelombang Pendaftaran & Master Jenis Berkas Dinamis | FR-45, FR-47 | P1 (High) | 5 |
| **PBI-15** | Fitur Unlock Berkas Siswa oleh Super Admin & Logging Audit Trail | FR-17, FR-18, FR-25 | P0 (Must) | 5 |
| **PBI-16** | Ekspor Rekapitulasi Data Siswa per Gelombang (Excel Maatwebsite & PDF) | FR-16 | P1 (High) | 5 |
| **PBI-17** | Manajemen Tahun Ajaran Baru, Pengarsipan Data, & Pengerasan Keamanan (DoD/UAT) | FR-32, NFR-05 s.d 12 | P1 (High) | 8 |
| **Total** | | | | **95 Story Points** |

### 3.3 Definition of Ready (DoR) dan Definition of Done (DoD)

#### Definition of Ready (DoR)
Item Backlog siap masuk ke dalam Sprint apabila:
1. *User Story* memiliki deskripsi yang jelas dan bebas dari ambiguitas.
2. *Acceptance Criteria* (Kriteria Keberterimaan) telah disepakati bersama Product Owner.
3. Ketergantungan (*dependency*) database atau modul sebelumnya telah tersedia.
4. Estimasi *Story Points* telah disepakati oleh seluruh anggota tim pengembang.

#### Definition of Done (DoD)
Fitur dinyatakan **Selesai (Done)** apabila memenuhi 4 pilar standar:
1. **Fungsionalitas:** Seluruh *acceptance criteria* pada PRD/SRS terpenuhi tanpa galat.
2. **Kualitas Kode & Testing:** Lulus pengujian otomatis (`php artisan test`) mencakup jalur sukses (*happy path*), jalur gagal (*validation fail*), dan uji otorisasi peran (*forbidden/unauthorized access*).
3. **Keamanan & Standar Arsitektur:** Password terenkripsi bcrypt, berkas pendaftar disimpan di disk privat (terproteksi dari direct URL access), input terfilter dan tersanitasi XSS, serta waktu bisnis merujuk ke server.
4. **Repositori & Dokumentasi:** Kode telah dicommit dengan konvensi pesan git terstruktur dan terdokumentasi dengan baik.

---

## BAB IV: PERENCANAAN DAN EKSEKUSI SPRINT (SPRINT ROADMAP)

Pengembangan sistem dibagi menjadi **7 Sprint** (Sprint 0 sampai Sprint 6) dengan siklus 1 - 2 minggu per Sprint:

### 4.1 Sprint 0: Inisialisasi Fondasi & Konfigurasi Lingkungan
* **Tujuan Sprint:** Menyiapkan kerangka kerja arsitektur teknis yang kokoh sebelum pengembangan fitur bisnis.
* **Durasi:** 1 Minggu
* **Item yang Dikerjakan:** PBI-01 (FR-33, FR-34)
* **Aktivitas:**
  - Konfigurasi Laravel pada stack Laragon (PHP 8.2+, MySQL, Composer, NPM).
  - Konfigurasi sistem autentikasi terpisah (Dual Guard: `siswa` untuk portal murid dan `web` untuk staf/admin).
  - Implementasi Middleware otorisasi berbasis peran: `Super Admin`, `Admin Piket`, dan `Siswa`.
  - Pembuatan database migration dasar dan *Seeder* awal (Akun Super Admin default, 1 Tahun Ajaran aktif, 5 Jurusan SMK, 3 Gelombang, dan 6 Master Jenis Berkas).
  - Pembuatan layout antarmuka dasar berstandar `DESIGN.md`.
* **Deliverable:** Aplikasi dapat dijalankan di lingkungan lokal, otentikasi login berhasil untuk guard staf dan siswa, serta seed data terisi lengkap di database.

### 4.2 Sprint 1: Modul Company Profile & Halaman Publik
* **Tujuan Sprint:** Merilis website profil sekolah yang representatif dan modul pengelolaan konten dinamis.
* **Durasi:** 1 Minggu
* **Item yang Dikerjakan:** PBI-02, PBI-03 (FR-01, FR-02, FR-03, FR-46, FR-48)
* **Aktivitas:**
  - Pembuatan halaman Landing Page publik (Visi Misi, Sambutan, Fasilitas, Video/Foto Gedung, Galeri).
  - Halaman showcase 5 Konsentrasi Keahlian (TKR, TSM, TOTR, RPL, Broadcasting) beserta prospek kerja dan daya tampung.
  - CRUD Berita dan Prestasi untuk peran Admin Piket & Super Admin dengan status *Draf* vs *Terbit*.
  - Sanitasi konten editor teks kaya (mencegah payload XSS pada deskripsi berita/prestasi).
  - Halaman Kontak dan formulir kirim pesan masuk publik dengan validasi rate limiter.
* **Deliverable:** Website profil sekolah dapat diakses publik, berita dan prestasi dapat dikelola staf, dan pesan pengunjung tersimpan di database.

### 4.3 Sprint 2: Portal Registrasi Calon Siswa & Bukti PDF
* **Tujuan Sprint:** Menyediakan fasilitas pendaftaran daring bagi calon siswa serta akun akses pribadi.
* **Durasi:** 1 - 2 Minggu
* **Item yang Dikerjakan:** PBI-04, PBI-05, PBI-06, PBI-07 (FR-04, FR-05, FR-06, FR-21, FR-22, FR-23, FR-43, FR-44)
* **Aktivitas:**
  - Formulir registrasi mandiri publik (pilihan jurusan, jalur umum/prestasi, data pribadi, asal sekolah, kontak ortu).
  - Logika generator Nomor Pendaftaran unik otomatis berformat `PPDB/{tahun}/{0001}` (bebas race condition).
  - Algoritma alokasi promo bebas SPP 3 bulan untuk 100 pendaftar pertama berbasis nomor urut registrasi.
  - Autentikasi guard siswa (username: nomor pendaftaran, password default: tanggal lahir `DDMMYYYY`).
  - Mekanisme proteksi brute-force login (lock 5 menit setelah 5 kali kegagalan).
  - Pembuatan generator kartu bukti registrasi format PDF siap cetak menggunakan Dompdf.
  - Dashboard ringkasan status pendaftaran untuk calon siswa.
* **Deliverable:** Calon siswa dapat mendaftar mandiri, login ke portal siswa, mengunduh bukti pendaftaran PDF, dan melihat status awal mereka.

### 4.4 Sprint 3: Manajemen Berkas Bertahap, Server-Side Timer, & Lockdown
* **Tujuan Sprint:** Mengotomatiskan proses upload dokumen persyaratan dengan penegakan batas waktu server yang disiplin.
* **Durasi:** 1 - 2 Minggu
* **Item yang Dikerjakan:** PBI-08, PBI-09 (FR-07, FR-08, FR-10, FR-24, FR-25, BR-07, BR-08)
* **Aktivitas:**
  - Portal pengunggahan dokumen pendaftaran berbasis jenis berkas dinamis sesuai jalur pendaftaran:
    - *Jalur Umum:* Ijazah/SKL (wajib), KK (wajib), Akta (opsional/identitas), SKTM/KIP (opsional bantuan).
    - *Jalur Prestasi:* Syarat umum + Sertifikat Prestasi (wajib).
  - Validasi ketat sisi server: file MIME (PDF, JPG, PNG) dan ukuran maksimal 2 MB.
  - Penyimpanan file di storage disk privat (bukan folder public asset) untuk menjamin kerahasiaan data siswa.
  - Komponen countdown timer batas waktu kelengkapan berkas berbasis **waktu server** (menolak manipulasi jam di browser klien).
  - Mekanisme otomatisasi *Lockdown* form upload saat batas waktu habis.
* **Deliverable:** Siswa dapat mengunggah berkas secara terstruktur, melihat hitung mundur waktu yang valid, dan form terkunci otomatis saat tenggat terlewati.

### 4.5 Sprint 4: Operasional Admin Piket, Verifikasi, & Loket Seragam
* **Tujuan Sprint:** Mendukung panitia sekolah dalam melayani pendaftar langsung dan memproses verifikasi berkas hingga pembagian seragam.
* **Durasi:** 1 - 2 Minggu
* **Item yang Dikerjakan:** PBI-10, PBI-11, PBI-12, PBI-13 (FR-09, FR-11, FR-12, FR-13, FR-14, FR-26, FR-27, BR-04, BR-05)
* **Aktivitas:**
  - Modul Pendaftaran Offline di loket sekolah oleh Admin Piket untuk calon siswa yang datang langsung.
  - Antarmuka verifikasi berkas: petugas dapat melihat preview dokumen, lalu memilih *Disetujui* (*Approve*) atau *Ditolak* (*Reject*).
  - Penegakan aturan: penolakan berkas **wajib** menyertakan catatan koreksi yang jelas agar siswa dapat mengunggah ulang.
  - Filter data pendaftar multi-kriteria (berdasarkan gelombang, konsentrasi jurusan, jalur pendaftaran, status kelengkapan berkas).
  - Modul Loket Pengambilan Seragam: verifikasi status kelayakan otomatis (*clearance* terpenuhi jika semua berkas wajib disetujui) dan pencatatan pengambilan seragam hanya satu kali per siswa.
* **Deliverable:** Admin Piket dapat melayani pendaftaran manual, memverifikasi dokumen secara transparan, dan mengendalikan hak pembagian seragam sekolah.

### 4.6 Sprint 5: Super Admin Console, Master Data, Ekspor, & Audit Trail
* **Tujuan Sprint:** Memberikan kendali konfigurasi menyeluruh, pelaporan eksekutif, dan fungsi pemulihan data bagi pimpinan.
* **Durasi:** 1 - 2 Minggu
* **Item yang Dikerjakan:** PBI-14, PBI-15, PBI-16 (FR-15, FR-16, FR-17, FR-18, FR-29 s.d FR-31, FR-45, FR-47)
* **Aktivitas:**
  - Pengelolaan Master Gelombang Pendaftaran (G1 Bidikmisi, G2, G3) dan Master Jenis Berkas Persyaratan secara fleksibel.
  - Konfigurasi profil sekolah, nomor kontak resmi WhatsApp CS PPDB, dan kuota promo benefit.
  - Fitur *Unlock* berkas siswa individual: Super Admin dapat membuka kembali hak upload siswa yang terkunci akibat batas waktu.
  - Fitur Ekspor Data Calon Siswa per Gelombang ke format Spreadsheet (Excel/CSV via Maatwebsite) dan dokumen cetak PDF.
  - Pencatatan Log Audit Aktivitas staf (*Audit Trail*: pencatatan IP, aksi verifikasi berkas, perubahan data, dan unlock).
  - Dashboard statistik analitik: grafik sebaran pilihan jurusan, rasio kelengkapan berkas, dan grafik kuota pendaftar per gelombang.
* **Deliverable:** Panel kontrol Super Admin berfungsi penuh, data pendaftar siap diekspor untuk laporan resmi yayasan/dinas, dan keamanan audit terjamin.

### 4.7 Sprint 6: System Hardening, Regression Testing, & UAT
* **Tujuan Sprint:** Memastikan kestabilan sistem secara menyeluruh, ketiadaan celah keamanan, dan kelulusan pengujian pengguna akhir (*User Acceptance Test*).
* **Durasi:** 1 Minggu
* **Item yang Dikerjakan:** PBI-17 (FR-32, NFR-01 s.d NFR-15)
* **Aktivitas:**
  - Manajemen Tahun Ajaran Baru dan mekanisme pengarsipan data pendaftaran lama.
  - Eksekusi rangkaian uji otomatis menyeluruh (`php artisan test`) untuk memverifikasi tidak adanya regresi (*Regression Guard*).
  - Uji celah otorisasi: verifikasi bahwa Calon Siswa dan Admin Piket tidak dapat mengakses rute khusus Super Admin.
  - Uji perlindungan akses file privat: memastikan link berkas fisik tidak dapat diakses publik tanpa login dan hak akses sah.
  - Pelaksanaan UAT (*User Acceptance Testing*) bersama representasi mitra SMK Muhammadiyah Paguyangan atau simulasi tugas besar.
  - Penyusunan dokumentasi instalasi, panduan operasional (*User Manual*), dan panduan backup database harian (`mysqldump`).
* **Deliverable:** Sistem terverifikasi stabil 100%, seluruh test suite lulus, berkas laporan akhir dan manual penggunaan siap diserahkan.

---

## BAB V: SEREMONIAL SCRUM & KONTROL KUALITAS

### 5.1 Pelaksanaan Ritual Scrum
Dalam pelaksanaan proyek semester ini, tim menerapkan 4 ritual standar Scrum:

1. **Sprint Planning (Awal Sprint):**
   - Menentukan *Sprint Goal* berdasarkan prioritas Product Backlog.
   - Menguraikan PBI terpilih menjadi daftar tugas teknis (*Sprint Backlog*).
   - Menetapkan penanggung jawab tugas backend, frontend, dan tester.
2. **Daily Standup / Regular Sync (2 - 3 kali seminggu):**
   - Rapat sinkronisasi 10 - 15 menit membahas 3 pertanyaan inti:
     * *Apa yang berhasil diselesaikan sejak pertemuan terakhir?*
     * *Apa yang akan dikerjakan hingga pertemuan berikutnya?*
     * *Hambatan atau kendala teknis apa yang sedang dihadapi?*
3. **Sprint Review & Demo (Akhir Sprint):**
   - Tim mendemonstrasikan fungsionalitas produk yang sudah memenuhi kriteria *Done* kepada dosen pembimbing atau perwakilan sekolah.
   - Mengumpulkan masukan langsung untuk dimasukkan ke dalam backlog sprint berikutnya.
4. **Sprint Retrospective (Setelah Sprint Review):**
   - Refleksi internal tim pengembang mengenai proses kolaborasi: apa yang berjalan efektif, apa yang kurang, dan aksi perbaikan konkret untuk sprint selanjutnya.

### 5.2 Strategi Pengujian Otomatis & Regression Guard
Kualitas kode dijaga ketat dengan menerapkan protokol **Regression Guard**:
- **Automated Feature Test:** Setiap penambahan rute dan alur bisnis wajib disertai skenario pengujian otomatis menggunakan framework test Laravel (Pest / PHPUnit).
- **Cakupan Pengujian:**
  1. *Happy Path:* Skenario pendaftaran sukses, berkas valid terunggah, verifikasi berkas disetujui, seragam berhasil diambil.
  2. *Validation Fail:* Skenario upload berkas non-PDF/gambar, berkas melebihi 2MB, pendaftaran melewati batas waktu.
  3. *Authorization & Security:* Skenario pengujian akses terlarang (calon siswa mencoba membuka rute `/admin`, staf mencoba mengakses konfigurasi master Super Admin).
- **Pre-Commit / Pre-Push Verification:** Seluruh pengujian dalam `php artisan test` wajib berstatus hijau (100% *pass*) sebelum perubahan digabungkan ke cabang utama repositori.

### 5.3 Manajemen Risiko dan Mitigasi

| Risiko Proyek | Tingkat | Dampak | Rencana Mitigasi (*Agile Action*) |
|---|:---:|:---:|---|
| Perubahan persyaratan/aturan PPDB dari pihak sekolah di tengah jalan. | Sedang | Menengah | Karakteristik Agile menyambut perubahan: ubah prioritas PBI pada *Sprint Planning* berikutnya tanpa merombak modul fondasi. |
| Calon siswa memanipulasi jam di laptop/HP agar terhindar dari *lockdown*. | Tinggi | Kritis | Seluruh validasi waktu diimplementasikan di sisi server (*server-time enforcement*); waktu klien hanya dipakai untuk tampilan visual semata. |
| Pendaftar mengunggah dokumen palsu atau rusak. | Tinggi | Menengah | Menerapkan alur verifikasi berkas dua arah oleh Admin Piket dengan status *Ditolak* dan kewajiban input catatan perbaikan. |
| Pengambilan seragam tercatat ganda akibat human error petugas. | Sedang | Kritis | Relasi *unique* database pada entitas pengambilan seragam per siswa serta validasi sistem yang menolak entri kedua secara otomatis. |
| Terjadinya konflik kode (*merge conflict*) antar anggota tim. | Sedang | Rendah | Menggunakan Git Branching berbasis fitur dan komunikasi harian saat standup; commit presisi dan berukuran kecil. |

---

## BAB VI: PENUTUP

### 6.1 Kesimpulan
Penerapan metodologi **Agile Scrum** dalam proyek pengembangan *Sistem Web Company Profile dan PPDB SMK Muhammadiyah Paguyangan* memberikan berbagai keuntungan strategis bagi tim mahasiswa dan institusi mitra:
1. **Pengerjaan Terstruktur & Terukur:** Proyek terbagi secara sistematis ke dalam 7 Sprint (Sprint 0 sampai Sprint 6) dengan total 95 Story Points memungkinkan pengelolaan kompleksitas sistem secara bertahap dan terukur.
2. **Kualitas Sistem Terjamin:** Dengan penerapan *Definition of Done (DoD)* dan pengujian otomatis anti-regresi, modul yang dihasilkan terbukti stabil, aman, dan siap digunakan.
3. **Penyelarasan Nyata dengan Kebutuhan Pengguna:** Umpan balik yang diterima secara berkala memungkinkan sistem beradaptasi dengan alur kerja nyata di SMK Muhammadiyah Paguyangan (seperti penanganan pendaftar offline, alokasi promo bebas SPP 100 siswa pertama, dan integrasi loket seragam).

### 6.2 Saran dan Pengembangan Lanjutan
Untuk pengembangan pada fase berikutnya setelah tugas besar ini selesai:
1. Penambahan integrasi Payment Gateway resmi untuk pembayaran biaya seragam/administrasi sekolah secara otomatis.
2. Integrasi layanan notifikasi WhatsApp Business API otomatis saat status verifikasi berkas siswa diperbarui oleh verifikator.
3. Pengembangan fitur ekspor terintegrasi dengan format sinkronisasi data calon siswa ke Data Pokok Pendidikan (Dapodik).

---

Paguyangan, Brebes, 2026

Penyusun Laporan,

**Suwito**  
NIM: 240111013  
*Mahasiswa Program Studi Teknik Informatika / Rekayasa Perangkat Lunak*
