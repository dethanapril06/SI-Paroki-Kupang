<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel pivot antara umat dan kategorial.
     * Mencatat keanggotaan umat di suatu kelompok kategorial
     * beserta jabatan, tanggal bergabung, dan status keanggotaannya.
     */
    public function up(): void
    {
        Schema::create('anggota_kategorial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umat_id')
                ->constrained('umat')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('kategorial_id')
                ->constrained('kategorial')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('jabatan', ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Anggota'])->default('Anggota');
            $table->string('bidang_tugas')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->date('tanggal_bergabung');
            $table->timestamps();

            // Satu umat hanya bisa terdaftar sekali di satu kategorial
            $table->unique(['umat_id', 'kategorial_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_kategorial');
    }
};
