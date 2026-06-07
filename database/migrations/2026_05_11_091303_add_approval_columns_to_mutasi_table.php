<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom approval workflow ke tabel mutasi.
     *
     * status             : status request mutasi (pending = belum diproses)
     * pemohon_umat_id    : umat yang mengajukan request (null = dibuat langsung oleh sekretariat)
     * diproses_oleh_user_id : user sekretariat yang approve/reject
     * catatan_admin      : catatan dari sekretariat (opsional saat approve, wajib saat reject)
     * diproses_pada      : timestamp saat diproses
     */
    public function up(): void
    {
        Schema::table('mutasi', function (Blueprint $table) {
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])
                  ->default('pending')
                  ->after('keterangan');

            $table->foreignId('pemohon_umat_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('umat')
                  ->nullOnDelete();

            $table->foreignId('diproses_oleh_user_id')
                  ->nullable()
                  ->after('pemohon_umat_id')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('catatan_admin')
                  ->nullable()
                  ->after('diproses_oleh_user_id');

            $table->timestamp('diproses_pada')
                  ->nullable()
                  ->after('catatan_admin');
        });
    }

    public function down(): void
    {
        Schema::table('mutasi', function (Blueprint $table) {
            $table->dropForeign(['pemohon_umat_id']);
            $table->dropForeign(['diproses_oleh_user_id']);
            $table->dropColumn([
                'status',
                'pemohon_umat_id',
                'diproses_oleh_user_id',
                'catatan_admin',
                'diproses_pada',
            ]);
        });
    }
};
