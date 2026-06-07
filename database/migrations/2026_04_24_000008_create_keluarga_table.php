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
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kub_id')->nullable()->constrained('kub')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedBigInteger('kepala_keluarga_id')->nullable()->index();
            $table->text('alamat');
            $table->enum('status_tempat_tinggal', ['Rumah Pribadi', 'Kontrak/Kost', 'Dinas']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga');
    }
};