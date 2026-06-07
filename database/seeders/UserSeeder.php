<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name'     => 'Sekretariat',
            'email'    => 'sekretariat@paroki.com',
            'password' => bcrypt('password'),
        ]);

        // Assign role sekretariat via tabel user_roles
        $roleId = \Illuminate\Support\Facades\DB::table('roles')
            ->where('name', 'sekretariat')
            ->value('id');

        if ($roleId) {
            \Illuminate\Support\Facades\DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $user->id,
                'role_id'    => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
