<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration ini menyelesaikan relasi yang tidak bisa dibuat sebelumnya
     * karena ketergantungan urutan tabel:
     *   - wilayah.ketua_umat_id  → umat  (mencatat siapa ketua, tanpa role khusus)
     *   - kub.ketua_umat_id      → umat
     *   - users.umat_id          → umat   (untuk: umat, ketua_kub, ketua_kategorial)
     *   - users.klerus_id        → klerus  (untuk: pastor)
     *
     * Catatan: sekretariat & dewan_pastoral = akun sistem murni (umat_id & klerus_id = null).
     * Catatan: ketua_wilayah TIDAK punya role khusus, cukup login sebagai umat biasa.
     */
    public function up(): void
    {
        // FK: wilayah.ketua_umat_id → umat
        Schema::table('wilayah', function (Blueprint $table) {
            $table->foreign('ketua_umat_id')
                ->references('id')
                ->on('umat')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        // FK: kub.ketua_umat_id → umat
        Schema::table('kub', function (Blueprint $table) {
            $table->foreign('ketua_umat_id')
                ->references('id')
                ->on('umat')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        // Tambah kolom umat_id dan klerus_id ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('umat_id')
                ->nullable()
                ->after('role')
                ->constrained('umat')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('klerus_id')
                ->nullable()
                ->after('umat_id')
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
        Schema::table('wilayah', function (Blueprint $table) {
            $table->dropForeign(['ketua_umat_id']);
        });

        Schema::table('kub', function (Blueprint $table) {
            $table->dropForeign(['ketua_umat_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['umat_id']);
            $table->dropForeign(['klerus_id']);
            $table->dropColumn(['umat_id', 'klerus_id']);
        });
    }
};
