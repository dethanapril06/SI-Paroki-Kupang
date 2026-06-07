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
        Schema::create('sakramen', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('umat_id')
                  ->constrained('umat')
                  ->cascadeOnDelete();
 
            $table->enum('jenis_sakramen', [
                'BAPTIS',
                'KOMUNI_PERTAMA',
                'KRISMA',
                'PERNIKAHAN',
                'MINYAK_SUCI',
            ]);
 
            $table->string('nomor_surat')->nullable()->unique();
 
            $table->date('tanggal_penerimaan');
 
            $table->foreignId('paroki_id')
                  ->nullable()
                  ->constrained('paroki')
                  ->nullOnDelete();
 
            $table->foreignId('klerus_id')
                  ->nullable()
                  ->constrained('klerus')
                  ->nullOnDelete();
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sakramen');
    }
};
