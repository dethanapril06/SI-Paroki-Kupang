<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayah1 = \App\Models\Wilayah::where('nama', 'Wilayah 1')->first();
        $wilayah12 = \App\Models\Wilayah::where('nama', 'Wilayah 12')->first();
        $wilayah16 = \App\Models\Wilayah::where('nama', 'Wilayah 16')->first();

        $kubData = [
            [
                'nama' => 'KUB 1.1',
                'wilayah_id' => $wilayah1->id,
            ],
            [
                'nama' => 'KUB 12.1', 
                'wilayah_id' => $wilayah12->id,
            ],
            [
                'nama' => 'KUB 16.1', 
                'wilayah_id' => $wilayah16->id,
            ],
        ];

        foreach ($kubData as $data) {
            \App\Models\Kub::create($data);
        }
    }
}
