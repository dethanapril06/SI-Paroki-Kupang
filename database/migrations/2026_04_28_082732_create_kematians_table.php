<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel kematian umat.
     * Mencatat detail kematian dan pemakaman seorang umat.
     *
     * Saat data kematian dibuat, status almarhum di tabel umat
     * otomatis diubah menjadi true melalui Observer.
     */
    public function up(): void
    {
        Schema::create('kematian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('umat_id')
                  ->unique() // satu umat hanya bisa punya satu data kematian
                  ->constrained('umat')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->date('tanggal_meninggal');
            $table->string('tempat_meninggal');

            $table->date('tanggal_pemakaman')->nullable();
            $table->string('tempat_pemakaman')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kematian');
    }
};