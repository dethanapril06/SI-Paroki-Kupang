<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keanggotaan_dpp', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_umat');
            $table->foreign('id_umat')
                  ->references('id')
                  ->on('umat')
                  ->onDelete('cascade');

            $table->enum('jabatan', [
                'Ketua',
                'Wakil Ketua',
                'Sekretaris',
                'Bendahara',
                'Koordinator Bidang',
                'Anggota',
                'Lainnya',
            ]);

            $table->string('bidang_tugas', 50)->nullable();

            $table->enum('status_aktif', ['Aktif', 'Nonaktif'])->default('Aktif');

            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keanggotaan_dpp');
    }
};