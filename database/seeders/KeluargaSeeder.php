<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use Illuminate\Database\Seeder;

class KeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Keluarga::create([
            'kub_id' => 1,
            'alamat' => 'Jl. Merdeka No. 45, Kupang',
            'status_tempat_tinggal' => 'Rumah Pribadi',
        ]);

        Keluarga::create([
            'kub_id' => 2,
            'alamat' => 'Jl. Sudirman No. 12, Kompleks Perumahan Indah',
            'status_tempat_tinggal' => 'Rumah Pribadi',
        ]);

        Keluarga::create([
            'kub_id' => 3,
            'alamat' => 'Jl. Ahmad Yani No. 78, Kos Putri Abadi',
            'status_tempat_tinggal' => 'Kontrak/Kost',
        ]);
    }
}
