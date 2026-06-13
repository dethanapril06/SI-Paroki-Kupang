<?php

namespace Database\Seeders;

use App\Models\Klerus;
use Illuminate\Database\Seeder;

class KlerusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createKlerusWithAccount([
            'nama' => 'Pastor Yohanes',
            'jabatan' => 'pastor',
            'status_aktif' => 'Aktif',
        ], 'pastor.yohanes@paroki.com');

        $this->createKlerusWithAccount([
            'nama' => 'Pastor Markus',
            'jabatan' => 'pastor',
            'status_aktif' => 'Aktif',
        ], 'pastor.markus@paroki.com');
        Klerus::create([
            'nama' => 'Uskup Paulus',
            'jabatan' => 'uskup',
            'status_aktif' => 'Aktif',
        ]);

        // Hubungkan pastor ke kategorial untuk data uji
        $pastorYohanes = Klerus::where('nama', 'Pastor Yohanes')->first();
        if ($pastorYohanes) {
            $misdinar = \App\Models\Kategorial::where('nama', 'Misdinar')->first();
            if ($misdinar) {
                $misdinar->update(['klerus_id' => $pastorYohanes->id]);
            }
        }

        $pastorMarkus = Klerus::where('nama', 'Pastor Markus')->first();
        if ($pastorMarkus) {
            $omk = \App\Models\Kategorial::where('nama', 'OMK')->first();
            if ($omk) {
                $omk->update(['klerus_id' => $pastorMarkus->id]);
            }
        }
    }

    private function createKlerusWithAccount(array $klerusData, string $email): Klerus
    {
        $klerus = Klerus::create($klerusData);

        $user = $klerus->user()->create([
            'name' => $klerusData['nama'],
            'email' => $email,
            'password' => 'password',
        ]);

        $roleId = \Illuminate\Support\Facades\DB::table('roles')->where('name', 'pastor')->value('id');
        if ($roleId) {
            \Illuminate\Support\Facades\DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $user->id,
                'role_id'    => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $klerus;
    }
}
