<?php

namespace Database\Seeders;

use Database\Seeders\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,       // ← harus pertama, UserSeeder butuh data roles
            UserSeeder::class,
            KeuskupanSeeder::class,
            ParokiSeeder::class,
            StasiSeeder::class,
            KategorialSeeder::class,
            WilayahSeeder::class,
            KubSeeder::class,
            KlerusSeeder::class,
            KeluargaSeeder::class,
            UmatSeeder::class,

        ]);
    }
}
