---
name: regression-guard
description: >-
  Mencegah kerusakan fitur yang sudah stabil (anti-regresi) saat melakukan bug fix,
  refactoring, atau penambahan fitur baru. Terapkan skill ini saat memperbaiki bug,
  mengubah kode logika bersama (shared code), atau melakukan modifikasi agar fitur lain
  tidak terdampak.
---

# Regression Guard: Protokol Pencegahan Kerusakan Fitur Eksisting

Skill ini dirancang untuk memastikan bahwa setiap perbaikan bug (*bug fix*), perubahan kode, maupun penambahan fitur baru dilakukan secara **presisi, aman, dan terisolasi**, tanpa merusak atau mengubah perilaku fitur lain yang sudah berjalan dengan stabil (*zero regression*).

---

## 1. Prinsip Utama (Golden Rules)

1. **"Surgical Edit" (Perubahan Presisi Bedah):**
   Ubah hanya baris kode yang menjadi akar masalah bug. Jangan merombak struktur lain di luar cakupan tugas.
2. **"If It Works, Preserve It":**
   Kode yang sudah berjalan stabil dan teruji tidak boleh diubah hanya karena alasan preferensi gaya penulisan pribadi (*gratuitous refactoring*).
3. **"Backward Compatibility First":**
   Jangan pernah menghapus atau mengubah nama fungsi, rute, view, atau kolom database yang sudah dipakai oleh modul lain tanpa menyediakan *fallback* / nilai default yang kompatibel.
4. **"Verify Before & After":**
   Verifikasi kondisi fitur sebelum dan sesudah perubahan dijalankan. Semua *feature test* yang sudah ada wajib tetap lulus 100%.

---

## 2. Langkah Kerja (5-Step Safe Fix Protocol)

### Langkah 1: Blast Radius & Dependency Mapping (Pemetaan Dampak)
Sebelum menyentuh satu baris kode pun, cari tahu siapa saja yang memanggil atau bergantung pada file/fungsi tersebut:
- **Grep Caller:** Cari di codebase (`grep_search`) fungsi, method, rute, model, atau variabel yang akan disentuh.
- **Identifikasi Konsumen:** Apakah fungsi ini dipakai oleh Controller publik, Controller admin, Seeder, atau Blade View?
- **Petakan Efek Samping:** Jika mengubah return type atau format data array/JSON, modul mana saja yang akan menerima perubahan tersebut?

### Langkah 2: Isolate Root Cause (Tangani Akar Masalah, Bukan Gejala)
- Pahami mengapa bug terjadi tanpa membuat asumsi berlebihan.
- Buat solusi yang **terlokalisasi** pada titik masalah, bukan mengubah arsitektur global yang mempengaruhi modul lain.

### Langkah 3: Terapkan Perubahan dengan Kompatibilitas Mundur
Saat mengubah kode, terapkan teknik berikut:
- **Parameter Baru Harus Opsional:** Jika menambahkan parameter ke fungsi/method yang sudah ada, selalu berikan nilai default (`function contoh($paramLama, $paramBaru = null)`).
- **Null-Safety & Fallbacks:** Pada Blade view dan Controller, selalu gunakan null coalescing (`??`) atau default value agar tidak menghasilkan error *"Trying to get property of non-object"* jika data lama tidak memiliki atribut baru.
- **Database & Migration Safety:** Jika menambah kolom baru di database, selalu gunakan `->nullable()` atau `->default(...)`. Jangan mengubah tipe data kolom yang sudah ada yang berisiko merusak query modul lain.
- **Route & URL Safety:** Jangan mengubah nama rute (`route('...')`) atau parameter URL yang sudah dipakai oleh navigasi, form, atau tombol aksi di view lain.

### Langkah 4: Regression Testing (Uji Bebas Regresi)
Setelah perubahan dilakukan:
1. **Jalankan Test Otomatis:**
   ```bash
   php artisan test
   ```
   Pastikan seluruh test suite lulus (warna hijau) tanpa ada kegagalan baru.
2. **Uji Fitur Terkait (Smoke Test):**
   - Buka/akses halaman yang baru diperbaiki untuk memastikan bug sudah tuntas.
   - Buka/akses halaman lain yang berbagi komponen atau model yang sama untuk memastikan fungsi mereka tidak terganggu.

### Langkah 5: Git Diff Inspection (Inspeksi Perubahan Bersih)
Sebelum commit atau menyampaikan hasil ke user:
1. Jalankan `git diff` untuk melihat setiap baris yang bertanda `+` dan `-`.
2. Pastikan:
   - Tidak ada kode fitur lain yang tidak sengaja terhapus.
   - Tidak ada perubahan file sampingan atau file temporary yang ikut terubah.
   - Perubahan hanya berfokus 100% pada perbaikan bug yang diminta.

---

## 3. Checklist Cepat Sebelum Menyatakan Bugfix Selesai

- [ ] Apakah saya sudah memeriksa semua tempat yang memanggil fungsi/file ini?
- [ ] Apakah ada fitur lain yang berbagi model, controller, atau helper yang sama?
- [ ] Apakah saya menambahkan nilai default / fallback agar data lama tetap aman?
- [ ] Apakah seluruh `php artisan test` tetap lulus 100%?
- [ ] Apakah `git diff` sudah bersih dan hanya berisi baris yang relevan?
