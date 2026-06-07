<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ParokiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keuskupan = \App\Models\Keuskupan::first(); 
        
        $parokiData = [
            [
                'nama' => 'Kathedral Kristus Raja', 
                'alamat' => 'Kupang', 
                'keuskupan_id' => $keuskupan->id, 
            ],
            [
                'nama' => 'test', 
                'alamat' => 'test', 
                'keuskupan_id' => $keuskupan->id, 
            ],
        ];

        foreach ($parokiData as $data) {
            \App\Models\Paroki::create($data);
        }
    }
}
