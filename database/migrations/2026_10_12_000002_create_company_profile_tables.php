<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('isi');
            $table->string('gambar_sampul')->nullable();
            $table->date('tanggal_terbit');
            $table->enum('status', ['draf', 'terbit'])->default('terbit');
            $table->string('penulis')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });

        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_prestasi');
            $table->string('slug')->unique();
            $table->string('nama_peraih'); // nama siswa, tim, atau pembina
            $table->string('tingkat'); // Sekolah, Kecamatan, Kabupaten, Karesidenan, Provinsi, Nasional, Internasional
            $table->string('peringkat'); // Juara 1, Juara 2, Juara 3, Medali Emas, dll
            $table->date('tanggal_perolehan');
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->enum('status', ['draf', 'terbit'])->default('terbit');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('isi');
            $table->date('tanggal');
            $table->enum('status', ['draf', 'terbit'])->default('terbit');
            $table->string('file_lampiran')->nullable();
            $table->timestamps();
        });

        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori'); // Bengkel & Laboratorium, Ruang Belajar, Fasilitas Penunjang, Ibadah & Olahraga
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('ekstrakurikulers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori'); // Kepanduan, Beladiri, Kejuruan/IT, Keagamaan, Olahraga, Kesenian
            $table->text('deskripsi');
            $table->string('pembina')->nullable();
            $table->string('jadwal')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('galeris', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori')->default('Kegiatan'); // Kegiatan, Fasilitas, Prestasi, Praktikum
            $table->string('foto');
            $table->text('keterangan')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });

        Schema::create('informasi_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'visi', 'misi', 'sejarah', 'sambutan_kepala_sekolah', 'tujuan', dll
            $table->string('judul');
            $table->longText('konten');
            $table->string('gambar')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamps();
        });

        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pelaku');
            $table->string('peran'); // super_admin | admin_piket
            $table->string('jenis_aksi'); // login, verifikasi, unlock, simpan, ubah, hapus, seragam
            $table->string('objek')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('informasi_sekolahs');
        Schema::dropIfExists('galeris');
        Schema::dropIfExists('ekstrakurikulers');
        Schema::dropIfExists('fasilitas');
        Schema::dropIfExists('pengumumans');
        Schema::dropIfExists('prestasis');
        Schema::dropIfExists('beritas');
    }
};
