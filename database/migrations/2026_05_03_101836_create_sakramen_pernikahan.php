<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pernikahan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sakramen_id')
                  ->unique()
                  ->constrained('sakramen')
                  ->cascadeOnDelete();

            $table->foreignId('pasangan_id')
                  ->nullable()
                  ->constrained('umat')
                  ->nullOnDelete();
            $table->string('pasangan_nama')->nullable();
            $table->string('pasangan_agama')->nullable();

            $table->enum('jenis_pernikahan', [
                'KATOLIK_KATOLIK',
                'KATOLIK_PROTESTAN',
                'KATOLIK_ISLAM',
                'KATOLIK_HINDU',
                'KATOLIK_BUDDHA',
                'KATOLIK_KONGHUCU',
                'KATOLIK_KEPERCAYAAN',
            ]);

            $table->boolean('izin_beda_gereja')->default(false);

            $table->boolean('dispensasi')->default(false);

            $table->date('tanggal_nikah_katolik')->nullable();

            $table->date('tanggal_catatan_sipil')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pernikahan');
    }
};