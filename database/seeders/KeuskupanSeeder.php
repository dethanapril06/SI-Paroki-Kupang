<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KeuskupanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh data keuskupan
        $keuskupanData = [
            [
                'nama' => 'Keuskupan Agung Kupang', 
                'alamat' => 'Jl. El Tari No. 1, Kupang', 
            ],
            [
                'nama' => 'test', 
                'alamat' => 'test', 
            ],

        ];

        foreach ($keuskupanData as $data) {
            \App\Models\Keuskupan::create($data);
        }
    }
}
