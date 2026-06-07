<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ISA child: Mutasi Umat
     * Mencatat perpindahan/perubahan status seorang umat.
     *
     * sub_jenis:
     *   - pindah_keluarga_ada  : umat berpindah ke keluarga yang sudah ada
     *   - pindah_keluarga_baru : umat berpindah ke keluarga baru (umat jadi kepala atau anggota)
     *   - paroki               : pindah antar paroki dalam keuskupan
     *   - keuskupan            : pindah ke luar keuskupan
     */
    public function up(): void
    {
        Schema::create('mutasi_umat', function (Blueprint $table) {
            $table->unsignedBigInteger('mutasi_id')->primary();
            $table->foreign('mutasi_id')
                  ->references('id')
                  ->on('mutasi')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('umat_id')
                  ->constrained('umat')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->enum('sub_jenis', [
                'pindah_keluarga_ada',
                'pindah_keluarga_baru',
                'paroki',
                'keuskupan',
            ])->comment('Sub-jenis mutasi umat');

            $table->string('nomor_surat', 100)->nullable();

            // Keluarga asal (selalu diisi)
            $table->foreignId('keluarga_asal_id')
                  ->nullable()
                  ->constrained('keluarga')
                  ->restrictOnDelete();

            // Keluarga tujuan (diisi jika pindah_keluarga_ada atau pindah_keluarga_baru)
            $table->foreignId('keluarga_tujuan_id')
                  ->nullable()
                  ->constrained('keluarga')
                  ->restrictOnDelete();

            // Histori asal — untuk referensi saat pindah paroki/keuskupan
            $table->foreignId('kub_asal_id')
                  ->nullable()
                  ->constrained('kub')
                  ->restrictOnDelete();

            $table->foreignId('wilayah_asal_id')
                  ->nullable()
                  ->constrained('wilayah')
                  ->restrictOnDelete();

            $table->foreignId('paroki_asal_id')
                  ->nullable()
                  ->constrained('paroki')
                  ->restrictOnDelete();

            $table->foreignId('paroki_tujuan_id')
                  ->nullable()
                  ->constrained('paroki')
                  ->restrictOnDelete();

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
        Schema::dropIfExists('mutasi_umat');
    }
};