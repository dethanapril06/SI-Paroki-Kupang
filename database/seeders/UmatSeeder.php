<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Umat;
use Illuminate\Database\Seeder;

class UmatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Keluarga 1 - 4 umat
        $umat1_1 = $this->createUmatWithAccount([
            'keluarga_id' => 1,
            'nama' => 'Bapak Yohanes Suryanto',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '1970-05-15',
            'jenis_kelamin' => 'Laki-laki',
            'hubungan_keluarga' => 'Suami',
            'status_pernikahan' => 'Kawin',
            'no_telepon' => '081234567890',
            'golongan_darah' => 'O',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Guru',
        ], 'yohanes.suryanto@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 1,
            'nama' => 'Ibu Maria Suryanto',
            'tempat_lahir' => 'Soe',
            'tanggal_lahir' => '1975-08-22',
            'jenis_kelamin' => 'Perempuan',
            'hubungan_keluarga' => 'Istri',
            'status_pernikahan' => 'Kawin',
            'no_telepon' => '081234567891',
            'golongan_darah' => 'A',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
        ], 'maria.suryanto@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 1,
            'nama' => 'Petrus Suryanto',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '2000-03-10',
            'jenis_kelamin' => 'Laki-laki',
            'hubungan_keluarga' => 'Anak',
            'status_pernikahan' => 'Belum Kawin',
            'golongan_darah' => 'O',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Mahasiswa',
        ], 'petrus.suryanto@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 1,
            'nama' => 'Theresia Suryanto',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '2005-07-20',
            'jenis_kelamin' => 'Perempuan',
            'hubungan_keluarga' => 'Anak',
            'status_pernikahan' => 'Belum Kawin',
            'golongan_darah' => 'A',
            'pendidikan' => 'SMA',
        ], 'theresia.suryanto@si-paroki.test');

        // Keluarga 2 - 4 umat
        $umat2_1 = $this->createUmatWithAccount([
            'keluarga_id' => 2,
            'nama' => 'Bapak Ignatius Hartono',
            'tempat_lahir' => 'Ende',
            'tanggal_lahir' => '1968-12-05',
            'jenis_kelamin' => 'Laki-laki',
            'hubungan_keluarga' => 'Suami',
            'status_pernikahan' => 'Kawin',
            'no_telepon' => '082345678901',
            'golongan_darah' => 'B',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Pengusaha',
        ], 'ignatius.hartono@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 2,
            'nama' => 'Ibu Lucia Hartono',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '1972-06-18',
            'jenis_kelamin' => 'Perempuan',
            'hubungan_keluarga' => 'Istri',
            'status_pernikahan' => 'Kawin',
            'no_telepon' => '082345678902',
            'golongan_darah' => 'AB',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Dokter',
        ], 'lucia.hartono@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 2,
            'nama' => 'Marcus Hartono',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '1998-09-12',
            'jenis_kelamin' => 'Laki-laki',
            'hubungan_keluarga' => 'Anak',
            'status_pernikahan' => 'Belum Kawin',
            'golongan_darah' => 'B',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Pekerja',
        ], 'marcus.hartono@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 2,
            'nama' => 'Magdalena Hartono',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '2003-01-25',
            'jenis_kelamin' => 'Perempuan',
            'hubungan_keluarga' => 'Anak',
            'status_pernikahan' => 'Belum Kawin',
            'golongan_darah' => 'AB',
            'pendidikan' => 'SMA',
        ], 'magdalena.hartono@si-paroki.test');

        // Keluarga 3 - 4 umat
        $umat3_1 = $this->createUmatWithAccount([
            'keluarga_id' => 3,
            'nama' => 'Ibu Fransiska Simatupang',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '1965-11-03',
            'jenis_kelamin' => 'Perempuan',
            'hubungan_keluarga' => 'Istri',
            'status_pernikahan' => 'Cerai Mati',
            'no_telepon' => '083456789012',
            'golongan_darah' => 'O',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Pedagang',
        ], 'fransiska.simatupang@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 3,
            'nama' => 'Daniel Simatupang',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '2001-02-14',
            'jenis_kelamin' => 'Laki-laki',
            'hubungan_keluarga' => 'Anak',
            'status_pernikahan' => 'Belum Kawin',
            'golongan_darah' => 'O',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Teknisi',
        ], 'daniel.simatupang@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 3,
            'nama' => 'Natalia Simatupang',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '2004-08-30',
            'jenis_kelamin' => 'Perempuan',
            'hubungan_keluarga' => 'Anak',
            'status_pernikahan' => 'Belum Kawin',
            'golongan_darah' => 'O',
            'pendidikan' => 'SMP',
        ], 'natalia.simatupang@si-paroki.test');

        $this->createUmatWithAccount([
            'keluarga_id' => 3,
            'nama' => 'Stefanus Simatupang',
            'tempat_lahir' => 'Kupang',
            'tanggal_lahir' => '2006-10-17',
            'jenis_kelamin' => 'Laki-laki',
            'hubungan_keluarga' => 'Anak',
            'status_pernikahan' => 'Belum Kawin',
            'golongan_darah' => 'O',
            'pendidikan' => 'SD',
        ], 'stefanus.simatupang@si-paroki.test');

        // Update kepala_keluarga_id untuk setiap keluarga
        Keluarga::find(1)->update(['kepala_keluarga_id' => $umat1_1->id]);
        Keluarga::find(2)->update(['kepala_keluarga_id' => $umat2_1->id]);
        Keluarga::find(3)->update(['kepala_keluarga_id' => $umat3_1->id]);
    }

    private function createUmatWithAccount(array $umatData, string $email): Umat
    {
        $umat = Umat::create($umatData);

        $user = $umat->user()->create([
            'name' => $umatData['nama'],
            'email' => $email,
            'password' => 'password',
        ]);

        $roleId = \Illuminate\Support\Facades\DB::table('roles')->where('name', 'umat')->value('id');
        if ($roleId) {
            \Illuminate\Support\Facades\DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $user->id,
                'role_id'    => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $umat;
    }
}
