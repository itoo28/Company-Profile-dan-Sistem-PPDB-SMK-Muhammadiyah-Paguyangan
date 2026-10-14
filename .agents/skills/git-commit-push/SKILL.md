---
name: git-commit-push
description: >-
  Panduan dan protokol standar untuk melakukan commit dan push ke GitHub menggunakan pesan commit berbahasa Indonesia yang informatif, terstruktur (Conventional Commits), aman, dan terverifikasi.
---

# Git Commit & Push: Standar Pesan Commit Informatif Bahasa Indonesia

Skill ini mengatur tata cara staging, penyusunan pesan commit berbahasa Indonesia yang jelas dan bermakna (*meaningful & informative commit*), serta prosedur push yang aman ke repositori GitHub.

---

## 1. Format Pesan Commit (Conventional Commits Bahasa Indonesia)

Pesan commit wajib mengikuti pola standar berikut:

```
<tipe>(<cakupan opsional>): <ringkasan perubahan dalam bahasa Indonesia>

[Deskripsi detail jika perubahan mencakup banyak hal / bersifat penting]
- Butir perubahan spesifik 1
- Butir perubahan spesifik 2
```

### Prefix / Tipe Commit yang Digunakan:

| Tipe | Kapan Digunakan | Contoh Pesan |
|---|---|---|
| `feat:` | Menambahkan fitur, komponen, modul, atau halaman baru | `feat: tambahkan CRUD konsentrasi keahlian di super admin` |
| `fix:` | Memperbaiki bug, cacat logika, error validasi, atau tampilan rusak | `fix: perbaiki validasi upload berkas siswa agar menerima format PDF` |
| `docs:` | Menambah atau memperbarui dokumentasi (PRD, README, DESIGN, catatan arsitektur) | `docs: perbarui spesifikasi kuota pendaftar dan gelombang di PRD` |
| `style:` | Perubahan tampilan visual, styling CSS, Blade layout tanpa mengubah logika bisnis | `style: perbaiki kontras tombol CTA pendaftaran pada mobile header` |
| `refactor:` | Mengubah atau merapikan struktur kode tanpa mengubah fungsi/perilaku | `refactor: pisahkan logika verifikasi berkas ke dalam service class` |
| `test:` | Menambahkan, memperbaiki, atau memperbarui unit/feature test | `test: tambahkan feature test verifikasi berkas oleh admin piket` |
| `chore:` | Pemeliharaan dependensi, konfigurasi build, migration dasar, atau tooling | `chore: perbarui dependensi composer dan konfigurasi laravel` |

---

## 2. Kaidah Penulisan Pesan Commit yang Informatif

1. **Gunakan Kata Kerja Aktif Imperatif Bahasa Indonesia:**
   * ✅ Gunakan: *tambahkan*, *perbarui*, *perbaiki*, *sesuaikan*, *hapus*, *optimalkan*, *integrasikan*.
   * ❌ Hindari: *menambahkan*, *udah bener*, *update*, *fix bug*, *commit*, *done*.

2. **Jelaskan "Apa" dan "Di Mana":**
   * ❌ Buruk (Ambigu): `fix: perbaiki error`
   * ✅ Informatif: `fix: atasi error 403 saat admin piket mengakses daftar berita`
   * ❌ Buruk (Ambigu): `feat: update ppdb`
   * ✅ Informatif: `feat: sediakan pilihan konsentrasi keahlian pada formulir pendaftaran PPDB`

3. **Gunakan Huruf Kecil untuk Prefix dan Ringkasan Pertama:**
   * Contoh: `feat: sesuaikan jadwal 3 gelombang pada halaman informasi PPDB`

4. **Sertakan Body (Poin Rinci) untuk Perubahan Besar / Majemuk:**
   Jika sebuah commit menyentuh beberapa file atau fitur sekaligus, sertakan poin-poin singkat di baris berikutnya:
   ```git
   feat: implementasikan manajemen gelombang pendaftaran pada portal super admin

   - Tambahkan migrasi tabel gelombangs dan relasi ke tahun ajaran
   - Sediakan controller dan formulir CRUD gelombang
   - Sinkronisasikan tanggal gelombang aktif ke halaman publik /ppdb
   ```

---

## 3. Protokol 5 Langkah Aman Sebelum & Saat Push

Sebelum menjalankan perintah git, ikuti langkah berurutan ini:

### Langkah 1: Jalankan Pengujian Otomatis
Wajib memastikan tidak ada kode yang rusak (*anti-regresi*):
```bash
php artisan test
```
*Jika ada test yang gagal (merah), perbaiki terlebih dahulu sebelum commit!*

### Langkah 2: Periksa Status & Diff
```bash
git status
git diff
```
*Pastikan tidak ada:*
* File konfigurasi rahasia (`.env`)
* File temporary/cache (`.phpunit.result.cache`, `.tmp`, file dump log)
* File yang tidak berhubungan dengan tugas yang sedang dikerjakan

### Langkah 3: Staging Bersih (Hindari `git add .` Membabi Buta)
Stage file yang relevan secara eksplisit:
```bash
git add app/ resources/views/ routes/web.php
# atau sebutkan nama filenya secara langsung
```

### Langkah 4: Buat Commit dengan Format Standar
```bash
git commit -m "tipe: ringkasan perubahan informatif dalam bahasa indonesia"
```

### Langkah 5: Push ke Remote Repository
```bash
git push origin <nama-branch>
```
Setelah push, verifikasi bahwa *working tree* bersih:
```bash
git status
```

---

## 4. Contoh Nyata (Good vs Bad Commits)

| Status | Pesan Commit | Alasan |
|:---:|---|---|
| ❌ | `update` | Terlalu singkat, tidak memberi konteks apa pun kepada tim. |
| ❌ | `fix: benerin bug kemarin` | Tidak jelas bug apa dan modul mana yang diperbaiki. |
| ❌ | `WIP` | Tidak informatif untuk riwayat repositori produksi. |
| ✅ | `feat: tambahkan fitur ekspor data pendaftar ke format Excel dan PDF` | Jelas tipenya (`feat`), jelas tindakannya, dan jelas format outputnya. |
| ✅ | `fix: perbaiki perhitungan hitung mundur timer upload berkas di dashboard siswa` | Jelas bagian mana yang diperbaiki dan di halaman apa. |
| ✅ | `docs: tambahkan panduan alur clearance seragam ke dalam PRD` | Menjelaskan dokumen apa yang diubah dan isi materinya. |
