<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minyak_suci', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sakramen_id')
                  ->unique()
                  ->constrained('sakramen')
                  ->cascadeOnDelete();

            $table->string('tempat_terima');

            // Pemberi minyak suci
            // Jika klerus terdaftar → pakai FK (klerus_id di parent sakramen sudah ada)
            // Jika bukan klerus (awam terlatih, darurat) → nama manual
            $table->string('nama_pemberi')->nullable();

            $table->text('keterangan_sebab')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minyak_suci');
    }
};