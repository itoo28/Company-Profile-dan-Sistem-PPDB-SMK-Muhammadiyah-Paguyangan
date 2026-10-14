# Panduan Proyek — SMK Muhammadiyah Paguyangan

Proyek ini adalah Sistem Web Company Profile dan PPDB untuk **SMK Muhammadiyah Paguyangan**.

- Spesifikasi kebutuhan fungsional & alur: [`PRD_Sistem_Web_Company_Profile_dan_PPDB.md`](./PRD_Sistem_Web_Company_Profile_dan_PPDB.md)
- Spesifikasi desain & gaya visual: [`DESIGN.md`](./DESIGN.md)

## Prinsip Keamanan Kode & Anti-Regresi (Selalu Aktif)
- **Skill Anti-Regresi:** Setiap kali melakukan perbaikan bug (*bug fix*), perubahan kode, atau refactoring, wajib mengikuti protokol [`.agents/skills/regression-guard/SKILL.md`](./.agents/skills/regression-guard/SKILL.md).
- **Prinsip Utama:** Lakukan perubahan secara presisi (*surgical edit*), jangan menyentuh kode yang sudah stabil di luar lingkup tugas, jaga kompatibilitas mundur (*backward compatibility*), dan selalu verifikasi kelulusan test (`php artisan test`) untuk memastikan tidak ada fitur lain yang rusak.

## Standar Git Commit & Push (Selalu Aktif)
- **Skill Git Commit & Push:** Setiap kali melakukan commit dan push ke GitHub, wajib mengikuti protokol [`.agents/skills/git-commit-push/SKILL.md`](./.agents/skills/git-commit-push/SKILL.md).
- **Prinsip Utama:** Gunakan format Conventional Commits dengan pesan bahasa Indonesia yang jelas, spesifik, dan informatif (hindari pesan ambigu seperti "update" atau "fix bug"). Pastikan seluruh pengujian (`php artisan test`) lulus dan inspeksi `git status` bersih sebelum melakukan push ke GitHub.

## Penggunaan Skill Anti-Slop (Opsional / On-Demand)
Skill **anti-slop** hanya digunakan jika user **secara eksplisit memintanya** (misalnya menyebut "antislop", "pakai antislop", atau "audit antislop"). Jika user tidak memintanya dalam perintah, kerjakan perubahan/tugas secara langsung tanpa menanyakan atau mengaktifkan antislop.



