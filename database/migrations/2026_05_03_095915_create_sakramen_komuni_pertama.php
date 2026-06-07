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
        Schema::create('komuni_pertama', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('sakramen_id')
                  ->unique()
                  ->constrained('sakramen')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sakramen_komuni_pertama');
    }
};
