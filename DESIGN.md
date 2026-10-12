# DESIGN.md — Panduan Desain & Arah Visual
## Sistem Web Company Profile dan PPDB SMK Muhammadiyah Paguyangan

Dokumen ini adalah **pedoman visual resmi** untuk implementasi antarmuka (UI/UX) pada seluruh modul sistem. Seluruh kode Blade dan CSS wajib mengacu pada panduan ini.

Dokumen ini bekerja sama dengan filter **anti-slop**:
- **anti-slop** bertindak sebagai **filter** (mencegah template generik AI, animasi tanpa tujuan, gradient ungu-cyan klise, copy marketing kosong, dan ketidakterbacaan).
- **DESIGN.md** bertindak sebagai **kompas arah** (menetapkan warna, tipografi, ritme layout, dan kepribadian institusi SMK Muhammadiyah Paguyangan).

---

## 1. Karakter & Kepribadian Brand (Brand Personality)

* **Institusi:** SMK Muhammadiyah Paguyangan (Sekolah Menengah Kejuruan berbasis Islam Berkemajuan di Paguyangan, Brebes).
* **Nuansa Utama:** Profesional, Islami Modern, Edukatif, Tanggap, Rapi, dan Berorientasi Industri/Masa Depan Kerja.
* **Nada Bahasa (Voice & Tone):** Lugas, ramah, jujur, informatif, dan transparan. Tidak menggunakan istilah berlebihan (*hype/buzzwords*) seperti "revolusioner", "masa depan tanpa batas", atau "teknologi AI tercanggih".

### Anti-Slop Dials (Indikator Liveliness)
* **ENERGY: 2** — Terstruktur, kokoh, berbobot institusional, dan hangat. Bukan landing page startup neon, bukan pula portal jadul yang membosankan.
* **RHYTHM: 2** — Variasi struktural yang teratur antar section (kombinasi layout 2 kolom, kartu foto 16:9, listicle pengumuman, dan tabel data yang padat).
* **MOTION: 1–2** — Transisi mikro yang fungsional (150ms–200ms) untuk hover tombol, fokus input form, dan pembukaan modal. Dilarang menggunakan animasi mengambang (*floating elements*) atau scrolling parallax berat yang mengganggu pendaftaran.

---

## 2. Sistem Warna (Harmonisasi Warna Logo SMK)

Palet warna diadaptasi langsung dari warna identitas pada logo resmi sekolah (`logo.jpg`), yaitu perpaduan **Biru Royal** dan **Kuning Emas Kejuruan**. Agar nyaman dipandang di layar monitor maupun ponsel (*eye-friendly*), tidak menimbulkan vibrasi warna (*chromatic vibration*), dan memenuhi standar kontras aksesibilitas WCAG AA/AAA, nilai saturasi dan keterbacaan telah disempurnakan secara profesional:

```
[Logo Asli]                         [Sistem Warna Terkalibrasi (UI)]
• Biru Murni  RGB(0, 0, 254)   ──>  • Royal Sapphire Blue (#0b57d0) & Deep Navy (#092347)
• Kuning Murni RGB(255, 245, 0) ──>  • Warm Golden Amber  (#f59e0b) & Dark Amber (#b45309)
• Putih Netral RGB(255, 255, 255)──> • Clean Slate Canvas (#f8fafc) & Pure White (#ffffff)
```

### 2.1 Warna Utama (Primary: Royal Sapphire & Deep Navy)
* **Primary Base:** `#0b57d0` (`hsl(217, 90%, 43%)`)
  * *Peruntukan:* Tombol aksi utama (CTA pendaftaran), tautan aktif, border fokus formulir, header kartu penting.
  * *Rasio Kontras:* 4.67:1 terhadap latar putih (Lolos WCAG AA).
* **Primary Hover / Active:** `#0947a8` (`hsl(217, 90%, 35%)`)
* **Primary Deep (Deep Royal Navy):** `#092347` (`hsl(215, 77%, 16%)`)
  * *Peruntukan:* Background sidebar panel admin, topbar institusional, teks judul besar, memberikan bobot wibawa institusi.
  * *Rasio Kontras:* 14.8:1 terhadap putih (Lolos WCAG AAA tertinggi).
* **Primary Surface / Tint:** `#eff6ff` (Blue 50 — sejuk dan ramah mata untuk kartu sorotan atau baris tabel terpilih).

### 2.2 Warna Aksen (Accent: Warm Golden Amber)
Menggantikan warna kuning neon mentah agar tidak silau dan tetap terbaca di latar terang:
* **Accent Base (Golden Amber):** `#f59e0b` (`hsl(38, 92%, 50%)`)
  * *Peruntukan:* Elemen sorotan kejuruan/matahari pencerahan, bintang prestasi, badge akreditasi, border aksen selektif.
* **Accent Text / Dark (untuk teks di latar terang):** `#b45309` (`hsl(32, 95%, 37%)`)
  * *Rasio Kontras:* 5.2:1 terhadap latar putih (Lolos WCAG AA).
* **Accent Surface / Soft Background:** `#fefce8` (Amber 50 / Yellow 50 — latar badge status/peringatan ringan).
* **Accent Hover:** `#d97706` (`hsl(38, 92%, 44%)`)

### 2.3 Warna Netral & Permukaan (Neutrals & Surfaces)
* **Canvas / Body Background:** `#f8fafc` (Slate 50 — abu-abu kebiruan sejuk, mengurangi kelelahan mata dibanding putih polos 100%).
* **Surface / Card Background:** `#ffffff` (Putih murni untuk kartu, kontainer formulir, dan dialog).
* **Surface Subdued / Input Fill:** `#f1f5f9` (Slate 100 — latar input saat idle dan striping tabel).
* **Border Default:** `#e2e8f0` (Slate 200 — garis pembatas halus dan tegas).
* **Border Strong:** `#cbd5e1` (Slate 300 — batas field form yang jelas terlihat).

### 2.4 Tipografi & Teks (Text Colors)
* **Heading & Teks Tegas:** `#0f172a` (Slate 900 — kontras tinggi 14:1 terhadap latar putih).
* **Body Text:** `#334155` (Slate 700 — sangat nyaman untuk membaca paragraf berita/syarat).
* **Muted / Caption Text:** `#64748b` (Slate 500 — teks bantuan, instruksi form, timestamp).

### 2.5 Warna Semantik Status (PPDB & Berkas)
Dilarang menggunakan warna neon. Gunakan warna semantik yang tenang namun tegas:
* **Disetujui (Approved):** Teks `#15803d` (`green-700`) pada badge `#dcfce7` (`green-100`) dengan border `#bbf7d0`.
* **Menunggu Verifikasi (Pending):** Teks `#b45309` (`amber-700`) pada badge `#fef3c7` (`amber-100`) dengan border `#fde68a`.
* **Ditolak (Rejected):** Teks `#b91c1c` (`red-700`) pada badge `#fee2e2` (`red-100`) dengan border `#fecaca`.
* **Belum Diunggah:** Teks `#475569` (`slate-600`) pada badge `#f1f5f9` (`slate-100`) dengan border `#e2e8f0`.
* **Jalur Offline:** Teks `#4338ca` (`indigo-700`) pada badge `#e0e7ff` (`indigo-100`).
* **Clearance Seragam (Berhak Ambil):** Banner hijau toska elegan `#065f46` berlatar `#ecfdf5` dengan aksen border `#a7f3d0`.

---

## 3. Tipografi

Sistem menggunakan jenis huruf tunggal yang kohesif dan modern dari Google Fonts: **Plus Jakarta Sans**. Font ini memiliki proporsi tegas khas institusional kontemporer Indonesia dan keterbacaan tinggi di layar smartphone murah sekalipun.

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

### Skala Tipografi (Type Scale)
| Peran | Ukuran (Desktop) | Ukuran (Mobile) | Weight | Line Height |
|---|---|---|---|---|
| **Display Hero** | `2.5rem` (40px) | `1.875rem` (30px) | 800 (Bold) | 1.15 |
| **H1 (Page Title)** | `2rem` (32px) | `1.5rem` (24px) | 700 (Bold) | 1.25 |
| **H2 (Section Title)** | `1.5rem` (24px) | `1.25rem` (20px) | 700 (Bold) | 1.3 |
| **H3 (Card Title)** | `1.125rem` (18px) | `1.125rem` (18px) | 600 (SemiBold) | 1.4 |
| **Body (Default)** | `1rem` (16px) | `0.9375rem` (15px) | 400 (Regular) | 1.6 |
| **Body Medium** | `0.875rem` (14px) | `0.875rem` (14px) | 500 (Medium) | 1.5 |
| **Caption / Small** | `0.75rem` (12px) | `0.75rem` (12px) | 600 (SemiBold) | 1.4 |

---

## 4. Struktur Komponen & Layout (No Slop Standards)

### 4.1 Bentuk & Radius Sudut (Border Radius)
* Dilarang membuat semua tombol, input, dan kartu berbentuk lonjong penuh (pill).
* Skala radius terukur:
  * **Input & Tombol:** `6px` (`rounded-md`) — tegas dan presisi.
  * **Card & Panel:** `10px` — sudut modern tanpa berlebihan.
  * **Modal & Dialog:** `14px`.
  * **Pill (`9999px`):** Khusus untuk status badge kecil saja.

### 4.2 Bayangan (Shadow & Elevation)
* Sebagian besar komponen menempel datar (*flat*) dengan batas garis halus (`1px solid #e2e8f0`).
* Elevasi hanya dipakai untuk elemen yang benar-benar melayang di atas dokumen:
  * **Card Hover:** `0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05)`
  * **Dropdown Menu / Toast:** `0 10px 15px -3px rgba(0, 0, 0, 0.08)`
  * **Modal Pop-up:** `0 20px 25px -5px rgba(0, 0, 0, 0.12)`

### 4.3 Navigasi Publik & Header
* **Top Bar Informasi:** Jam operasional, alamat Paguyangan, kontak telepon & email.
* **Main Header:** Logo SMK Muhammadiyah Paguyangan + Nama Sekolah, menu navigasi jelas (Beranda, Profil, Jurusan/Fasilitas, Prestasi, Berita, Info PPDB, Kontak), serta tombol CTA "Daftar PPDB" berwarna Biru Royal (`#0b57d0`) dengan hover tegas yang mencolok dan ramah mata.
* **Sticky Header:** Latar belakang solid putih dengan border bawah halus, atau semi-transparan dengan blur minimal khusus saat digulir.

---

## 5. Tata Letak Modul Khusus

### 5.1 Halaman Publik (Company Profile)
1. **Hero Section:**
   * Judul lugas mengenai pendaftaran peserta didik baru SMK Muhammadiyah Paguyangan.
   * Disertai badge tahun ajaran aktif dengan aksen Golden Amber (`#f59e0b`).
   * Dua tombol aksi: Tombol utama "Daftar Siswa Baru" (Biru Royal `#0b57d0`) dan tombol sekunder "Alur & Persyaratan" (outline slate halus `#e2e8f0` dengan hover `#f1f5f9`).
   * Foto nyata fasilitas atau siswa SMK berseragam praktikum/kejuruan (bukan ilustrasi 3D AI mengambang).
2. **Keunggulan & Program Keahlian:**
   * Daftar program kejuruan disajikan dalam bentuk grid informatif dengan foto nyata lab/bengkel dan deskripsi kompetensi kerja.
3. **Berita & Pengumuman:**
   * Berita disajikan dalam kartu berformat gambar 16:9, label tanggal, dan judul tebal.
   * Pengumuman disajikan dalam format listicle vertikal bertanggal agar menghemat ruang dan mudah dipindai mata.
4. **Alur PPDB (`/ppdb`):**
   * Infografis alur langkah 1 s.d. 4 (Pendaftaran Online $\rightarrow$ Unggah Berkas $\rightarrow$ Verifikasi Panitia $\rightarrow$ Ambil Seragam).
   * Daftar berkas persyaratan yang terhubung dinamis dengan data master berkas.

### 5.2 Portal Calon Siswa (`/siswa/*`)
1. **Formulir Pendaftaran (`/ppdb/daftar`):**
   * Dikelompokkan rapi ke dalam beberapa grup logis:
     * *Data Calon Siswa* (Nama, NIK, NISN, TTL, JK, Alamat)
     * *Asal Sekolah*
     * *Data Orang Tua / Wali* (Nama Ayah/Ibu/Wali, Nomor WhatsApp)
   * Setiap input memiliki label yang jelas, pesan validasi instan, dan panduan format (contoh: NIK 16 digit).
2. **Halaman Sukses (`/ppdb/berhasil`):**
   * Kartu ringkasan Nomor Pendaftaran yang dicetak tebal.
   * Kotak instruksi login (Username = Nomor Pendaftaran, Password = Tanggal Lahir `DDMMYYYY`).
   * Tombol cetak/unduh bukti pendaftaran PDF.
3. **Dashboard Siswa (`/siswa/dashboard`):**
   * **Header:** Ucapan selamat datang, Nomor Pendaftaran, dan status ringkas.
   * **Countdown Timer:** Box hitung mundur batas waktu upload berkas (hari, jam, menit) berbasis waktu server. Jika mendekati tenggat, warna box berubah menjadi amber hangat, dan merah tegas jika telah lewat waktu (*Lockdown*).
   * **Tabel Berkas:** Menampilkan daftar jenis berkas wajib dan opsional, status berkas terkini, tombol upload/ganti, serta kotak catatan penolakan jika berkas ditolak.
   * **Banner Clearance Seragam:** Ditampilkan di bagian atas saat seluruh berkas wajib berstatus *Disetujui*, memberitahukan siswa berhak datang ke loket TU untuk mengambil seragam.

### 5.3 Area Administrasi & Loket (`/admin/*`)
1. **Struktur Panel Admin:**
   * Sidebar navigasi di sisi kiri (latar belakang Deep Royal Navy `#092347` dengan aksen aktif Royal Sapphire `#0b57d0` dan teks putih kontras tinggi).
   * Header admin menampilkan nama staf yang sedang bertugas dan peran (`Admin Piket` atau `Super Admin`).
   * Filter menu ketat: Admin Piket hanya melihat menu operasional dan Berita/Prestasi; menu Konfigurasi, Akun Staf, Ekspor, dan Konten Lain disembunyikan.
2. **Tabel Data Pendaftar (`/admin/pendaftar`):**
   * Format padat (*dense table*) dengan pencarian cepat nama/nomor pendaftaran.
   * Filter instan: Status berkas (*Ada yang Ditolak*, *Menunggu Verifikasi*, *Belum Lengkap*, *Disetujui Semua*) dan Jalur (*Online* / *Offline*).
3. **Verifikasi Berkas Siswa:**
   * Layar periksa berkas: Di sisi kiri terdapat pratinjau dokumen (PDF atau gambar JPG/PNG) yang di-load aman dari disk privat, di sisi kanan terdapat tombol Approve (Hijau) dan Reject (Merah).
   * Tombol Reject secara wajib menampilkan kolom isian alasan/catatan perbaikan.
4. **Loket Seragam (`/admin/seragam`):**
   * Menampilkan verifikasi data diri siswa dan status clearance berkas.
   * Tombol pencatatan seragam hanya aktif jika semua berkas wajib disetujui.
   * Dilengkapi dialog konfirmasi untuk mencegah salah klik.
5. **Manajemen Berita & Prestasi:**
   * Tampilan daftar CRUD yang intuitif dengan badge filter *Draf* vs *Terbit*.
   * Editor teks kaya yang disanitasi di sisi server.

---

## 6. Format Dokumen Bukti Pendaftaran PDF

* Ukuran kertas: A4.
* Header: Kop surat resmi SMK Muhammadiyah Paguyangan (Logo, Alamat, Kontak, Garis Kop Ganda).
* Judul: "BUKTI PENDAFTARAN PESERTA DIDIK BARU".
* Isi:
  * Tabel Data Diri Pendaftar (Nomor Pendaftaran, Nama, NIK, NISN, Asal Sekolah, Tanggal Daftar).
  * Kotak Instruksi Akses Portal Siswa (Alamat Web, Username, Password Awal).
  * Catatan persyaratan berkas yang wajib diunggah.
  * Tanda tangan stempel panitia PPDB.

---

## 7. Prinsip Aksesibilitas (WCAG AA)

* Semua elemen interaktif memiliki outline fokus yang terlihat jelas (`focus-visible: ring-2 ring-emerald-600 ring-offset-2`).
* Semua tombol dan tautan memiliki target sentuh minimal $44 \times 44\text{ px}$ pada tampilan mobile.
* Kontras warna teks terhadap latar belakang minimal 4.5:1 untuk teks normal dan 3:1 untuk teks besar.
