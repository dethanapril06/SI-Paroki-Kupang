<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Kub;
use App\Models\Role;
use App\Models\Umat;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UmatRegisterController extends Controller
{
    /**
     * Tampilkan halaman registrasi mandiri umat.
     */
    public function create(): View
    {
        $kubList = Kub::with('wilayah')
            ->orderBy('nama')
            ->get();

        // Daftar keluarga yang sudah ada untuk opsi "bergabung"
        $keluargaList = Keluarga::with([
                'kub.wilayah',
                'kepalaKeluarga' => fn($q) => $q->aktif(),
            ])
            ->whereHas('umat', fn($q) => $q->aktif())
            ->orderBy('id', 'desc')
            ->get();

        return view('auth.register', compact('kubList', 'keluargaList'));
    }

    /**
     * Proses data registrasi mandiri umat.
     * Mendukung dua mode:
     *  - 'baru'  : buat keluarga baru
     *  - 'ada'   : bergabung ke keluarga yang sudah ada
     */
    public function store(Request $request): RedirectResponse
    {
        $mode = $request->input('keluarga_mode', 'baru');

        // ── Aturan validasi berdasarkan mode ──────────────────────────────────
        $keluargaRules = $mode === 'ada'
            ? [
                'keluarga_id' => ['required', 'exists:keluarga,id'],
            ]
            : [
                'kub_id'                => ['required', 'exists:kub,id'],
                'alamat'                => ['required', 'string', 'max:1000'],
                'status_tempat_tinggal' => ['required', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
            ];

        $request->validate(array_merge($keluargaRules, [
            // Data pribadi
            'nama'                   => ['required', 'string', 'max:255'],
            'tempat_lahir'           => ['required', 'string', 'max:255'],
            'tanggal_lahir'          => ['required', 'date'],
            'jenis_kelamin'          => ['required', 'in:Laki-laki,Perempuan'],
            'hubungan_keluarga'      => ['required', 'in:Suami,Istri,Anak,Saudara,Ayah,Ibu,Lainnya'],
            'status_pernikahan'      => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'no_telepon'             => ['required', 'string', 'max:20'],
            'nama_ayah'              => ['nullable', 'string', 'max:255'],
            'nama_ibu'               => ['nullable', 'string', 'max:255'],
            'golongan_darah'         => ['nullable', 'in:A,B,AB,O'],
            'pendidikan'             => ['required', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'pekerjaan'              => ['required', 'string', 'max:255'],
            'penyandang_disabilitas' => ['nullable', 'boolean'],

            // Akun login
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]), [
            'keluarga_id.required'              => 'Keluarga wajib dipilih.',
            'keluarga_id.exists'                => 'Keluarga yang dipilih tidak valid.',
            'kub_id.required'                   => 'KUB wajib dipilih.',
            'kub_id.exists'                     => 'KUB yang dipilih tidak valid.',
            'alamat.required'                   => 'Alamat wajib diisi.',
            'status_tempat_tinggal.required'    => 'Status tempat tinggal wajib dipilih.',
            'nama.required'                     => 'Nama lengkap wajib diisi.',
            'tempat_lahir.required'             => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required'            => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required'            => 'Jenis kelamin wajib dipilih.',
            'hubungan_keluarga.required'        => 'Hubungan dalam keluarga wajib dipilih.',
            'status_pernikahan.required'        => 'Status pernikahan wajib dipilih.',
            'no_telepon.required'               => 'No. telepon wajib diisi.',
            'pendidikan.required'               => 'Pendidikan terakhir wajib dipilih.',
            'pekerjaan.required'                => 'Pekerjaan wajib diisi.',
            'email.required'                    => 'Email wajib diisi.',
            'email.unique'                      => 'Email ini sudah terdaftar.',
            'password.required'                 => 'Kata sandi wajib diisi.',
            'password.confirmed'                => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'                      => 'Kata sandi minimal 8 karakter.',
        ]);

        DB::transaction(function () use ($request, $mode) {
            // 1. Tentukan keluarga — buat baru atau pakai yang ada
            if ($mode === 'ada') {
                $keluarga = Keluarga::findOrFail($request->keluarga_id);
            } else {
                $keluarga = Keluarga::create([
                    'kub_id'               => $request->kub_id,
                    'alamat'               => $request->alamat,
                    'status_tempat_tinggal'=> $request->status_tempat_tinggal,
                    'kepala_keluarga_id'   => null,
                ]);
            }

            // 2. Buat data Umat
            $umat = Umat::create([
                'keluarga_id'            => $keluarga->id,
                'nama'                   => $request->nama,
                'tempat_lahir'           => $request->tempat_lahir,
                'tanggal_lahir'          => $request->tanggal_lahir,
                'jenis_kelamin'          => $request->jenis_kelamin,
                'hubungan_keluarga'      => $request->hubungan_keluarga,
                'status_pernikahan'      => $request->status_pernikahan,
                'no_telepon'             => $request->no_telepon,
                'nama_ayah'             => $request->nama_ayah,
                'nama_ibu'              => $request->nama_ibu,
                'golongan_darah'         => $request->golongan_darah,
                'pendidikan'             => $request->pendidikan,
                'pekerjaan'              => $request->pekerjaan,
                'penyandang_disabilitas' => $request->boolean('penyandang_disabilitas'),
                'status_almarhum'        => false,
                'status_keaktifan'       => 'non-aktif',
            ]);

            // 3. Jadikan kepala keluarga jika dicentang (hanya untuk keluarga baru)
            if ($mode === 'baru' && $request->boolean('sebagai_kepala_keluarga')) {
                $keluarga->update(['kepala_keluarga_id' => $umat->id]);
            }

            // 4. Buat akun User dengan status pending
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'umat_id'  => $umat->id,
                'status'   => 'pending',
            ]);

            // 5. Assign role 'umat'
            $roleUmat = Role::where('name', 'umat')->first();
            if ($roleUmat) {
                $user->roles()->attach($roleUmat->id);
            }
        });

        return redirect()->route('register.success');
    }
}
