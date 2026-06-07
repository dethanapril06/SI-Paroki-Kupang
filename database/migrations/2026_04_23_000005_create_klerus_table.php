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
        Schema::create('klerus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jabatan', ['pastor', 'uskup']);
            $table->enum('status_aktif', ['Aktif', 'Meninggal', 'Emeritus'])->default('Aktif');
            $table->timestamps();
        });

        Schema::table('keuskupan', function (Blueprint $table) {
            $table->foreign('klerus_id')
                ->references('id')
                ->on('klerus')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('paroki', function (Blueprint $table) {
            $table->foreign('klerus_id')
                ->references('id')
                ->on('klerus')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('kuasi', function (Blueprint $table) {
            $table->foreign('klerus_id')
                ->references('id')
                ->on('klerus')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klerus');
        Schema::table('keuskupan', function (Blueprint $table) {
            $table->dropForeign(['klerus_id']);
        });

        Schema::table('paroki', function (Blueprint $table) {
            $table->dropForeign(['klerus_id']);
        });

        Schema::table('kuasi', function (Blueprint $table) {
            $table->dropForeign(['klerus_id']);
        });
    }
};
