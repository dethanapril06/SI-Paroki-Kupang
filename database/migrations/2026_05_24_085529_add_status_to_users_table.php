<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom status untuk alur approval registrasi mandiri umat.
     * - pending  : baru daftar, belum disetujui sekretariat
     * - active   : sudah disetujui / dibuat langsung oleh sekretariat
     * - rejected : ditolak oleh sekretariat
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'rejected'])
                  ->default('active')
                  ->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
