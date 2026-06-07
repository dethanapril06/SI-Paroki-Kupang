<?php

namespace App\Http\Controllers\DewanPastoral;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sekretariat\KeanggotaanDppRequest;
use App\Models\KeanggotaanDpp;
use App\Models\Umat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KeanggotaanDppController extends Controller
{
    /**
     * Tampilkan daftar seluruh anggota DPP.
     * GET /dpp/keanggotaan
     */
    public function index(Request $request)
    {
        $query = KeanggotaanDpp::with('umat');

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        if ($request->filled('bidang_tugas')) {
            $query->where('bidang_tugas', 'like', '%' . $request->bidang_tugas . '%');
        }

        $anggota = $query->orderByRaw("FIELD(jabatan,
            'Ketua','Wakil Ketua','Sekretaris','Bendahara',
            'Koordinator Bidang','Anggota','Lainnya'
        )")->paginate(15)->withQueryString();

        return view('dewan_pastoral.anggota.index', [
            'anggota'     => $anggota,
            'listJabatan' => KeanggotaanDpp::JABATAN,
            'listStatus'  => KeanggotaanDpp::STATUS,
            'filters'     => $request->only(['jabatan', 'status_aktif', 'bidang_tugas']),
        ]);
    }

    /**
     * Form tambah anggota baru.
     * GET /dpp/keanggotaan/create
     */
    public function create()
    {
        // Hanya umat yang belum terdaftar sebagai anggota DPP
        $umatSudahDpp = KeanggotaanDpp::pluck('id_umat');
        $umatList = Umat::aktif()
                        ->whereNotIn('id', $umatSudahDpp)
                        ->where('status_almarhum', false)
                        ->orderBy('nama')
                        ->get(['id', 'nama']);

        return view('dewan_pastoral.anggota.create', [
            'umatList'    => $umatList,
            'listJabatan' => KeanggotaanDpp::JABATAN,
            'listStatus'  => KeanggotaanDpp::STATUS,
        ]);
    }

    /**
     * Simpan anggota baru + otomatis buat akun user dewan_pastoral (hanya jika Ketua).
     * POST /dpp/keanggotaan
     */
    public function store(KeanggotaanDppRequest $request)
    {
        $validated = $request->validated();
        $keanggotaan = KeanggotaanDpp::create($validated);

        if ($validated['jabatan'] === 'Ketua') {
            $umat = Umat::findOrFail($keanggotaan->id_umat);

            // Cari apakah sudah ada user account untuk Umat ini
            $user = User::where('umat_id', $umat->id)->first();

            if ($user) {
                // Jika sudah ada, tinggal sync role dewan_pastoral
                $roleId = DB::table('roles')->where('name', 'dewan_pastoral')->value('id');
                if ($roleId) {
                    DB::table('user_roles')->insertOrIgnore([
                        'user_id'    => $user->id,
                        'role_id'    => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                return redirect()
                    ->route('dewan_pastoral.keanggotaan.index')
                    ->with('success', "Anggota DPP (Ketua) berhasil ditambahkan. Umat sudah memiliki akun Portal Umat. Silakan login menggunakan email: {$user->email}");
            }

            // Jika belum ada, buat akun baru berformat nama.slug@paroki.com
            $emailBase = Str::slug($umat->nama, '.') . '@paroki.com';
            $email     = $emailBase;
            $counter   = 1;
            while (User::where('email', $email)->exists()) {
                $email = Str::slug($umat->nama, '.') . $counter . '@paroki.com';
                $counter++;
            }

            $user = User::create([
                'name'     => $umat->nama,
                'email'    => $email,
                'password' => Hash::make('password'),
                'umat_id'  => $umat->id,
            ]);

            // Berikan dual role: umat dan dewan_pastoral
            $roleIds = DB::table('roles')->whereIn('name', ['umat', 'dewan_pastoral'])->pluck('id');
            foreach ($roleIds as $roleId) {
                DB::table('user_roles')->insertOrIgnore([
                    'user_id'    => $user->id,
                    'role_id'    => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()
                ->route('dewan_pastoral.keanggotaan.index')
                ->with('success', "Anggota DPP (Ketua) berhasil ditambahkan. Akun portal baru berhasil dibuat. Email: {$email} | Password: password");
        }

        return redirect()
            ->route('dewan_pastoral.keanggotaan.index')
            ->with('success', "Anggota DPP berhasil ditambahkan.");
    }

    /**
     * Tampilkan detail satu anggota.
     * GET /dpp/keanggotaan/{keanggotaan}
     */
    public function show(KeanggotaanDpp $keanggotaan)
    {
        $keanggotaan->load('umat');

        $user = User::where('umat_id', $keanggotaan->id_umat)->first();

        return view('dewan_pastoral.anggota.show', compact('keanggotaan', 'user'));
    }

    /**
     * Form edit anggota.
     * GET /dpp/keanggotaan/{keanggotaan}/edit
     */
    public function edit(KeanggotaanDpp $keanggotaan)
    {
        $umatList = Umat::aktif()
                        ->where('status_almarhum', false)
                        ->orderBy('nama')
                        ->get(['id', 'nama']);

        return view('dewan_pastoral.anggota.edit', [
            'keanggotaan' => $keanggotaan,
            'umatList'    => $umatList,
            'listJabatan' => KeanggotaanDpp::JABATAN,
            'listStatus'  => KeanggotaanDpp::STATUS,
        ]);
    }

    /**
     * Simpan perubahan data anggota.
     * PUT /dpp/keanggotaan/{keanggotaan}
     */
    public function update(KeanggotaanDppRequest $request, KeanggotaanDpp $keanggotaan)
    {
        $oldJabatan = $keanggotaan->jabatan;
        $validated = $request->validated();
        $keanggotaan->update($validated);

        if ($validated['jabatan'] === 'Ketua' && $oldJabatan !== 'Ketua') {
            $umat = Umat::findOrFail($keanggotaan->id_umat);
            $user = User::where('umat_id', $umat->id)->first();

            if (!$user) {
                $emailBase = Str::slug($umat->nama, '.') . '@paroki.com';
                $email     = $emailBase;
                $counter   = 1;
                while (User::where('email', $email)->exists()) {
                    $email = Str::slug($umat->nama, '.') . $counter . '@paroki.com';
                    $counter++;
                }

                $user = User::create([
                    'name'     => $umat->nama,
                    'email'    => $email,
                    'password' => Hash::make('password'),
                    'umat_id'  => $umat->id,
                ]);

                $roleIds = DB::table('roles')->whereIn('name', ['umat', 'dewan_pastoral'])->pluck('id');
                foreach ($roleIds as $roleId) {
                    DB::table('user_roles')->insertOrIgnore([
                        'user_id'    => $user->id,
                        'role_id'    => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                $roleId = DB::table('roles')->where('name', 'dewan_pastoral')->value('id');
                if ($roleId) {
                    DB::table('user_roles')->insertOrIgnore([
                        'user_id'    => $user->id,
                        'role_id'    => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } elseif ($validated['jabatan'] !== 'Ketua' && $oldJabatan === 'Ketua') {
            $user = User::where('umat_id', $keanggotaan->id_umat)->first();
            if ($user) {
                $roleId = DB::table('roles')->where('name', 'dewan_pastoral')->value('id');
                if ($roleId) {
                    DB::table('user_roles')->where([
                        'user_id' => $user->id,
                        'role_id' => $roleId,
                    ])->delete();
                }
            }
        }

        return redirect()
            ->route('dewan_pastoral.keanggotaan.index')
            ->with('success', 'Data anggota DPP berhasil diperbarui.');
    }

    /**
     * Hapus anggota DPP + hapus akun user dewan_pastoral terkait.
     * DELETE /dpp/keanggotaan/{keanggotaan}
     */
    public function destroy(KeanggotaanDpp $keanggotaan)
    {
        // Cabut role dewan_pastoral dari user terkait
        $user = User::where('umat_id', $keanggotaan->id_umat)->first();
        if ($user) {
            $roleId = DB::table('roles')->where('name', 'dewan_pastoral')->value('id');
            if ($roleId) {
                DB::table('user_roles')->where([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ])->delete();
            }
        }

        $keanggotaan->delete();

        return redirect()
            ->route('dewan_pastoral.keanggotaan.index')
            ->with('success', 'Jabatan DPP berhasil dicabut. Akun login yang bersangkutan tetap aktif sebagai umat.');
    }
}
