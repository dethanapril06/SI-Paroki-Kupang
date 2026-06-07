<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Hapus kolom 'role' dari tabel users.
     *
     * PENTING: Migration ini HARUS dijalankan SETELAH:
     *   1. Migration create_roles_table (tabel roles sudah ada)
     *   2. Migration create_user_roles_table (tabel pivot sudah ada)
     *   3. RoleSeeder (master data roles sudah terisi)
     *   4. UserRoleMigrationSeeder (data role lama sudah dipindah ke user_roles)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Kembalikan kolom role jika rollback diperlukan.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'umat',
                'ketua_kub',
                'ketua_kategorial',
                'pastor',
                'dewan_pastoral',
                'sekretariat',
            ])->default('umat')->after('password');
        });
    }
};
