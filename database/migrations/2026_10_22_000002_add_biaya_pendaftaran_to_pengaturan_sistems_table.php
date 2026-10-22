<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            $table->unsignedBigInteger('biaya_pendaftaran')->default(100000)->after('kuota_promo_benefit');
            $table->string('nama_bank')->default('Bank Syariah Indonesia (BSI)')->after('biaya_pendaftaran');
            $table->string('nomor_rekening')->default('7123456789')->after('nama_bank');
            $table->string('atas_nama_rekening')->default('SMK Muhammadiyah Paguyangan')->after('nomor_rekening');
            $table->text('instruksi_pembayaran')->nullable()->after('atas_nama_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            $table->dropColumn([
                'biaya_pendaftaran',
                'nama_bank',
                'nomor_rekening',
                'atas_nama_rekening',
                'instruksi_pembayaran',
            ]);
        });
    }
};
