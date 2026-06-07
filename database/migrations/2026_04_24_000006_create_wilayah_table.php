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
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedBigInteger('ketua_umat_id')->nullable()->index();

            // Wilayah dapat berada langsung di bawah salah satu parent berikut.
            $table->foreignId('paroki_id')->nullable()->constrained('paroki')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('kuasi_id')->nullable()->constrained('kuasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('stasi_id')->nullable()->constrained('stasi')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
