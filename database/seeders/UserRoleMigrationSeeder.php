<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleMigrationSeeder extends Seeder
{
    /**
     * Migrasi data role lama dari kolom users.role → tabel user_roles.
     *
     * PENTING: Jalankan seeder ini SEBELUM migration remove_role_from_users_table.
     * Kolom users.role harus masih ada saat seeder ini dijalankan.
     */
    public function run(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
            $this->command->info("ℹ️ Kolom 'role' pada tabel 'users' sudah tidak ada. Migrasi dilewati.");
            return;
        }

        // Ambil semua user yang masih punya kolom role (sebelum dihapus)
        $users = DB::table('users')
            ->select('id', 'role')
            ->whereNotNull('role')
            ->get();

        foreach ($users as $user) {
            // Cari role_id dari tabel roles berdasarkan name
            $role = DB::table('roles')
                ->where('name', $user->role)
                ->first();

            if ($role) {
                DB::table('user_roles')->insertOrIgnore([
                    'user_id'    => $user->id,
                    'role_id'    => $role->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $migrated = DB::table('user_roles')->count();
        $this->command->info("✅ Berhasil migrasi {$migrated} entri role ke tabel user_roles.");
    }
}
