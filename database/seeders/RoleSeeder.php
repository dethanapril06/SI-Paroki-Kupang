<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Isi master data tabel roles.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'umat',              'label' => 'Umat'],
            ['name' => 'ketua_kub',         'label' => 'Ketua KUB'],
            ['name' => 'ketua_kategorial',  'label' => 'Ketua Kategorial'],
            ['name' => 'pastor',            'label' => 'Pastor'],
            ['name' => 'dewan_pastoral',    'label' => 'Dewan Pastoral'],
            ['name' => 'sekretariat',       'label' => 'Sekretariat'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'name'       => $role['name'],
                'label'      => $role['label'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
