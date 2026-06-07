<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paroki = \App\Models\Paroki::first();
        $stasiAntonius = \App\Models\Stasi::where('nama', 'Stasi St. Antonius')->first();
        $stasiMaria = \App\Models\Stasi::where('nama', 'Stasi St. Maria')->first();

        // Contoh data wilayah
        $wilayahData = [
            [
                'nama' => 'Wilayah 1',
                'paroki_id' => $paroki->id,
            ],
            [
                'nama' => 'Wilayah 12', 
                'stasi_id' => $stasiAntonius->id,
            ],
            [
                'nama' => 'Wilayah 16', 
                'stasi_id' => $stasiMaria->id,
            ],
        ];

        foreach ($wilayahData as $data) {
            \App\Models\Wilayah::create($data);
        }
    }
}
