<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KategorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategorialData = [
            ['nama' => 'Misdinar'],
            ['nama' => 'OMK'],
            ['nama' => 'WKRI'],
        ];

        foreach ($kategorialData as $data) {
            \App\Models\Kategorial::create($data);
        }
    }
}
