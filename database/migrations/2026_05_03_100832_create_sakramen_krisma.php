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
        Schema::create('krisma', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('sakramen_id')
                  ->unique()
                  ->constrained('sakramen')
                  ->cascadeOnDelete();
 
            $table->foreignId('uskup_id')
                  ->nullable()
                  ->constrained('klerus')
                  ->nullOnDelete();
 
            $table->string('nama_krisma')->nullable();
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sakramen_krisma');
    }
};
