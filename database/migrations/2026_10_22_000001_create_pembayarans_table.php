<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswas')->cascadeOnDelete();
            $table->string('kode_transaksi')->unique();
            $table->unsignedBigInteger('nominal')->default(100000);
            $table->enum('metode_pembayaran', ['transfer', 'tunai'])->default('transfer');
            $table->enum('status', ['menunggu_pembayaran', 'menunggu_verifikasi', 'lunas', 'ditolak'])->default('menunggu_pembayaran');
            $table->string('nama_pengirim')->nullable();
            $table->string('bank_pengirim')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diverifikasi_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
