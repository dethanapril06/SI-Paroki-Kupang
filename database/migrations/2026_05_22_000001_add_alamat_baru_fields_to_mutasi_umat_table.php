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
        Schema::table('mutasi_umat', function (Blueprint $table) {
            $table->text('alamat_baru')->nullable()->after('keuskupan_tujuan_id');
            $table->string('status_tempat_tinggal_baru')->nullable()->after('alamat_baru');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_umat', function (Blueprint $table) {
            $table->dropColumn(['alamat_baru', 'status_tempat_tinggal_baru']);
        });
    }
};
