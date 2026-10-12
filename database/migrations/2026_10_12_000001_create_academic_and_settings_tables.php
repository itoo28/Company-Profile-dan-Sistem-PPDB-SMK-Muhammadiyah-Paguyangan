<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // contoh: '2026/2027'
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('pengaturan_sistems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->dateTime('batas_waktu_berkas')->nullable();
            $table->unsignedInteger('max_file_size_mb')->default(2);
            $table->unsignedInteger('nomor_urut_terakhir')->default(0);
            $table->string('nama_sekolah')->default('SMK Muhammadiyah Paguyangan');
            $table->string('slogan')->nullable()->default('Islami, Mandiri, Berprestasi, dan Berorientasi Industri');
            $table->string('alamat')->default('Jl. Raya Paguyangan No. 12, Paguyangan, Brebes, Jawa Tengah 52276');
            $table->string('telepon')->default('(0289) 430123');
            $table->string('whatsapp')->default('0812-3456-7890');
            $table->string('email')->default('info@smkmuhpaguyangan.sch.id');
            $table->string('jam_operasional')->default('Senin - Jumat: 07.00 - 15.30 WIB | Sabtu: 07.00 - 13.00 WIB');
            $table->text('maps_embed_url')->nullable();
            $table->timestamps();
        });

        Schema::create('jenis_berkas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('is_wajib')->default(true);
            $table->string('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_berkas');
        Schema::dropIfExists('pengaturan_sistems');
        Schema::dropIfExists('tahun_ajarans');
    }
};
