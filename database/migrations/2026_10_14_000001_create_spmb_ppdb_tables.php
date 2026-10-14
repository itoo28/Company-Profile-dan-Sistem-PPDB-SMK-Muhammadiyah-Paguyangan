<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi tabel pengaturan_sistems: Tambah kuota promo benefit dan status promo aktif
        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            $table->unsignedInteger('kuota_promo_benefit')->default(100)->after('nomor_urut_terakhir');
            $table->boolean('is_promo_aktif')->default(true)->after('kuota_promo_benefit');
        });

        // 2. Modifikasi tabel jenis_berkas: Tambah aturan_wajib dan is_aktif
        Schema::table('jenis_berkas', function (Blueprint $table) {
            $table->enum('aturan_wajib', ['semua', 'khusus_prestasi', 'opsional'])->default('semua')->after('is_wajib');
            $table->boolean('is_aktif')->default(true)->after('urutan');
        });

        // 3. Tabel jurusans (Konsentrasi Keahlian Vokasi - FR-46)
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // contoh: 'TKR', 'TSM', 'TOTR', 'RPL', 'BROADCASTING'
            $table->string('nama_jurusan');
            $table->string('bidang_keahlian');
            $table->text('deskripsi');
            $table->text('prospek_kerja')->nullable();
            $table->unsignedInteger('kuota')->default(100);
            $table->string('foto')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });

        // 4. Tabel gelombangs (Gelombang Pendaftaran PPDB - FR-44, FR-45)
        Schema::create('gelombangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('nama_gelombang'); // contoh: 'Gelombang 1 (Bidikmisi)', 'Gelombang 2', 'Gelombang 3'
            $table->string('kode_gelombang', 10)->nullable(); // contoh: 'G1', 'G2', 'G3'
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan')->nullable();
            $table->boolean('is_bidikmisi')->default(false);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 5. Tabel calon_siswas (Data Pendaftar PPDB - FR-04, FR-06, FR-43, FR-44)
        Schema::create('calon_siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran', 50)->unique();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->restrictOnDelete();
            $table->foreignId('gelombang_id')->constrained('gelombangs')->restrictOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusans')->restrictOnDelete();
            $table->enum('jalur_pendaftaran', ['umum', 'prestasi'])->default('umum');
            $table->enum('jalur_registrasi', ['online', 'offline'])->default('online');
            $table->unsignedInteger('nomor_urut')->default(1);
            $table->enum('status_benefit', ['tidak_dapat', 'kandidat_100_pertama', 'klaim_aktif'])->default('tidak_dapat');
            
            // Data Diri Siswa
            $table->string('nama_lengkap');
            $table->string('nik', 16);
            $table->string('nisn', 10);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('asal_sekolah');
            
            // Data Orang Tua / Wali
            $table->string('nama_orang_tua');
            $table->string('no_hp_orang_tua', 30);
            
            // Autentikasi Siswa
            $table->string('password'); // Hash dari tanggal lahir DDMMYYYY
            $table->boolean('is_unlocked')->default(false); // Bypass lockdown oleh Super Admin
            $table->rememberToken();
            $table->timestamps();

            // Constraint unik: NIK dan NISN unik per tahun ajaran
            $table->unique(['tahun_ajaran_id', 'nik'], 'unique_nik_per_tahun');
            $table->unique(['tahun_ajaran_id', 'nisn'], 'unique_nisn_per_tahun');
        });

        // 6. Tabel berkas_siswas (Berkas Upload Bertahap Siswa - FR-07, FR-13, FR-24, FR-25)
        Schema::create('berkas_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswas')->cascadeOnDelete();
            $table->foreignId('jenis_berkas_id')->constrained('jenis_berkas')->restrictOnDelete();
            $table->string('file_path'); // Disimpan di storage privat
            $table->string('nama_file_asli');
            $table->unsignedInteger('file_size_kb')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('waktu_verifikasi')->nullable();
            $table->timestamps();

            // Satu berkas aktif per jenis berkas per siswa
            $table->unique(['calon_siswa_id', 'jenis_berkas_id'], 'unique_siswa_berkas');
        });

        // 7. Tabel pengambilan_seragams (Loket Seragam - FR-14, FR-27)
        Schema::create('pengambilan_seragams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->unique()->constrained('calon_siswas')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('waktu_ambil');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 8. Tabel pesan_masuks (Inbox Kontak Pengunjung - FR-20, FR-48)
        Schema::create('pesan_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kontak', 100);
            $table->string('subjek', 150);
            $table->text('pesan');
            $table->enum('status', ['belum_dibaca', 'sudah_dibaca', 'dibalas'])->default('belum_dibaca');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_masuks');
        Schema::dropIfExists('pengambilan_seragams');
        Schema::dropIfExists('berkas_siswas');
        Schema::dropIfExists('calon_siswas');
        Schema::dropIfExists('gelombangs');
        Schema::dropIfExists('jurusans');

        if (Schema::hasColumn('jenis_berkas', 'aturan_wajib')) {
            Schema::table('jenis_berkas', function (Blueprint $table) {
                $table->dropColumn(['aturan_wajib', 'is_aktif']);
            });
        }

        if (Schema::hasColumn('pengaturan_sistems', 'kuota_promo_benefit')) {
            Schema::table('pengaturan_sistems', function (Blueprint $table) {
                $table->dropColumn(['kuota_promo_benefit', 'is_promo_aktif']);
            });
        }
    }
};
