<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Umat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AnggotaKeluargaController extends Controller
{
    /**
     * Pastikan user adalah kepala keluarga dari keluarga yang dimilikinya.
     */
    private function getKeluargaSaya(): Keluarga
    {
        $umatId = Auth::user()->umat_id;

        $keluarga = Keluarga::where('kepala_keluarga_id', $umatId)->first();

        if (!$keluarga) {
            abort(403, 'Hanya kepala keluarga yang dapat menambahkan anggota keluarga.');
        }

        return $keluarga;
    }

    /**
     * Form tambah anggota keluarga.
     */
    public function create()
    {
        $keluarga = $this->getKeluargaSaya();

        return view('portal.keluarga-saya.anggota.create', compact('keluarga'));
    }

    /**
     * Simpan anggota baru ke dalam keluarga milik user.
     */
    public function store(Request $request)
    {
        $keluarga = $this->getKeluargaSaya();

        $validated = $request->validate([
            'nama'                   => ['required', 'string', 'max:255'],
            'tempat_lahir'           => ['required', 'string', 'max:255'],
            'tanggal_lahir'          => ['required', 'date'],
            'jenis_kelamin'          => ['required', 'in:Laki-laki,Perempuan'],
            'hubungan_keluarga'      => ['required', 'in:Suami,Istri,Anak,Saudara,Ayah,Ibu,Lainnya'],
            'nama_ayah'              => ['nullable', 'string', 'max:255'],
            'nama_ibu'               => ['nullable', 'string', 'max:255'],
            'status_pernikahan'      => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'no_telepon'             => ['nullable', 'string', 'max:20'],
            'golongan_darah'         => ['nullable', 'in:A,B,AB,O'],
            'pendidikan'             => ['nullable', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'pekerjaan'              => ['nullable', 'string', 'max:255'],
            'penyandang_disabilitas' => ['boolean'],
            'email'                  => ['required', 'email', 'unique:users,email'],
        ]);

        $validated['keluarga_id']            = $keluarga->id;
        $validated['penyandang_disabilitas'] = $request->boolean('penyandang_disabilitas');

        // Buat data umat
        $umat = Umat::create(
            collect($validated)->except(['email'])->toArray()
        );

        // Buat akun login hanya jika email diisi
        $pesanAkun = '';
        if (!empty($validated['email'])) {
            $user = User::create([
                'name'     => $umat->nama,
                'email'    => $validated['email'],
                'password' => Hash::make('password'),
                'umat_id'  => $umat->id,
            ]);

            $roleId = DB::table('roles')->where('name', 'umat')->value('id');
            if ($roleId) {
                DB::table('user_roles')->insertOrIgnore([
                    'user_id'    => $user->id,
                    'role_id'    => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $pesanAkun = " Akun login: {$validated['email']} | Password: password";
        }

        return redirect()
            ->route('portal.dashboard')
            ->with('success', "Anggota keluarga <strong>{$umat->nama}</strong> berhasil ditambahkan.{$pesanAkun}");
    }
}
