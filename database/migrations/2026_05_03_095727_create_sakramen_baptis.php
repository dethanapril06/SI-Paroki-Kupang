<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('baptis', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('sakramen_id')
                  ->unique() // one-to-one dengan sakramen
                  ->constrained('sakramen')
                  ->cascadeOnDelete();
 
            // ------------------------------------------------------------------
            // Sumber baptis
            // KATOLIK  → klerus_id wajib, kolom protestan NULL
            // PROTESTAN → klerus_id NULL, nama_pemberi_protestan wajib
            // ------------------------------------------------------------------
            $table->enum('sumber_baptis', ['KATOLIK', 'PROTESTAN']);
 
            $table->foreignId('klerus_id')
                  ->nullable()
                  ->constrained('klerus')
                  ->nullOnDelete();
 
            $table->string('nama_pemberi_protestan')->nullable();
            $table->string('nama_gereja_protestan')->nullable();
 
            // ------------------------------------------------------------------
            // Tanggal
            // tgl_baptis           → tanggal baptis asli (Katolik maupun Protestan)
            // tgl_diterima_katolik → hanya diisi jika sumber_baptis = PROTESTAN,
            //                        mencatat kapan resmi diakui masuk Gereja Katolik
            // ------------------------------------------------------------------
            $table->date('tgl_baptis');
            $table->date('tgl_diterima_katolik')->nullable();
 
            $table->string('nama_baptis')->nullable();
 
            // ------------------------------------------------------------------
            // Wali baptis
            // Jika terdaftar sebagai umat → pakai foreignId
            // Jika tidak terdaftar / dari luar → nama manual (_nama)
            // ------------------------------------------------------------------
            $table->foreignId('bapak_baptis_id')
                  ->nullable()
                  ->constrained('umat')
                  ->nullOnDelete();
            $table->string('bapak_baptis_nama')->nullable();
 
            $table->foreignId('ibu_baptis_id')
                  ->nullable()
                  ->constrained('umat')
                  ->nullOnDelete();
            $table->string('ibu_baptis_nama')->nullable();
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baptis');
    }
};
