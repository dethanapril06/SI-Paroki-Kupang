<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ISA child: Mutasi Keluarga
     * Mencatat perpindahan sebuah keluarga antar wilayah / KUB.
     *
     * sub_jenis:
     *   - keuskupan : pindah ke luar keuskupan
     *   - paroki    : pindah antar paroki dalam keuskupan
     *   - wilayah   : pindah antar wilayah dalam paroki
     *   - kub       : pindah antar KUB dalam wilayah
     */
    public function up(): void
    {
        Schema::create('mutasi_keluarga', function (Blueprint $table) {
            // PK sekaligus FK ke parent mutasi (pola ISA)
            $table->unsignedBigInteger('mutasi_id')->primary();
            $table->foreign('mutasi_id')
                  ->references('id')
                  ->on('mutasi')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('keluarga_id')
                  ->constrained('keluarga')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->enum('sub_jenis', [
                'keuskupan',
                'paroki',
                'wilayah',
                'kub',
            ])->comment('Sub-jenis mutasi keluarga');

            $table->string('nomor_surat', 100)->nullable();

            // Pindah KUB
            $table->foreignId('kub_asal_id')
                  ->nullable()
                  ->constrained('kub')
                  ->restrictOnDelete();

            $table->foreignId('kub_tujuan_id')
                  ->nullable()
                  ->constrained('kub')
                  ->restrictOnDelete();

            // Pindah Wilayah
            $table->foreignId('wilayah_asal_id')
                  ->nullable()
                  ->constrained('wilayah')
                  ->restrictOnDelete();

            $table->foreignId('wilayah_tujuan_id')
                  ->nullable()
                  ->constrained('wilayah')
                  ->restrictOnDelete();

            // Pindah Paroki
            $table->foreignId('paroki_asal_id')
                  ->nullable()
                  ->constrained('paroki')
                  ->restrictOnDelete();

            $table->foreignId('paroki_tujuan_id')
                  ->nullable()
                  ->constrained('paroki')
                  ->restrictOnDelete();

            // Pindah Keuskupan
            $table->foreignId('keuskupan_asal_id')
                  ->nullable()
                  ->constrained('keuskupan')
                  ->restrictOnDelete();

            $table->foreignId('keuskupan_tujuan_id')
                  ->nullable()
                  ->constrained('keuskupan')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_keluarga');
    }
};