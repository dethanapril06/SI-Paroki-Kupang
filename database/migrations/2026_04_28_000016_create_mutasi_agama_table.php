<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ISA child: Mutasi Agama
     * Mencatat peristiwa perpindahan agama seorang umat keluar dari Katolik.
     *
     * agama_asal : agama sebelumnya (default 'katolik' karena ini sistem paroki)
     * agama_tujuan : agama yang dituju setelah keluar
     */
    public function up(): void
    {
        Schema::create('mutasi_agama', function (Blueprint $table) {
            // PK sekaligus FK ke parent mutasi (pola ISA)
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

            $table->enum('agama_asal', [
                'katolik',
            ])->default('katolik');

            $table->enum('agama_tujuan', [
                'protestan',
                'hindu',
                'budha',
                'khonghucu',
                'islam',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_agama');
    }
};