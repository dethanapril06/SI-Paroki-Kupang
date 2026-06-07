<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paroki = \App\Models\Paroki::first(); 

        $stasiData = [
            [
                'nama' => 'Stasi St. Antonius', 
                'paroki_id' => $paroki->id,
                'alamat' => 'Kelapa Lima',
                'koordinator' => 'Bapak Antonius',
            ],
            [
                'nama' => 'Stasi St. Maria', 
                'paroki_id' => $paroki->id,
                'alamat' => 'Perumnas',
                'koordinator' => 'Ibu Maria',
            ],
        ];

        foreach ($stasiData as $data) {
            \App\Models\Stasi::create($data);
        }
    }
}
