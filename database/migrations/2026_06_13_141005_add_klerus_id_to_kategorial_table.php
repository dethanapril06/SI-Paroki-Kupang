<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategorial', function (Blueprint $table) {
            $table->foreignId('klerus_id')
                ->nullable()
                ->after('ketua_umat_id')
                ->constrained('klerus')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategorial', function (Blueprint $table) {
            $table->dropForeign(['klerus_id']);
            $table->dropColumn('klerus_id');
        });
    }
};
