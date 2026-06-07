<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel parent ISA untuk semua jenis mutasi.
     * Mencatat tanggal dan keterangan umum.
     * Kolom 'jenis' digunakan sebagai discriminator (tipe mutasi apa).
     */
    public function up(): void
    {
        Schema::create('mutasi', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['umat', 'keluarga', 'agama']);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi');
    }
};