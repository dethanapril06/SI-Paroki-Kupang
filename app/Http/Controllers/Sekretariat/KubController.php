<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Kub;
use App\Models\MutasiKeluarga;
use App\Models\MutasiUmat;
use App\Models\Umat;
use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kub = Kub::with(['wilayah', 'ketuaUmat'])->latest()->get();

        return view('sekretariat.kub.index', compact('kub'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wilayah = Wilayah::orderBy('nama')->get();

        return view('sekretariat.kub.create', compact('wilayah'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'wilayah_id' => ['required', 'exists:wilayah,id'],
        ]);

        Kub::create($validated);

        return redirect()
            ->route('sekretariat.kub.index')
            ->with('success', 'Data KUB berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kub $kub)
    {
        $kub->load('wilayah');

        return view('sekretariat.kub.show', compact('kub'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kub $kub)
    {
        $wilayah = Wilayah::orderBy('nama')->get();
        $umat = Umat::aktif()->whereHas('keluarga', fn($q) => $q->where('kub_id', $kub->id))
            ->orderBy('nama')
            ->get();

        return view('sekretariat.kub.edit', compact('kub', 'wilayah', 'umat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kub $kub)
    {
        $validated = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'wilayah_id'    => ['required', 'exists:wilayah,id'],
            'ketua_umat_id' => ['nullable', 'exists:umat,id'],
        ]);

        $oldKetuaId = $kub->getOriginal('ketua_umat_id');
        $newKetuaId = $validated['ketua_umat_id'] ?? null;

        $kub->update($validated);

        if ($oldKetuaId != $newKetuaId) {
            // Cabut role ketua_kub dari ketua lama jika dia tidak lagi memegang jabatan ketua KUB lain
            if ($oldKetuaId) {
                $stillKetua = Kub::where('ketua_umat_id', $oldKetuaId)->where('id', '!=', $kub->id)->exists();
                if (!$stillKetua) {
                    $oldUser = User::where('umat_id', $oldKetuaId)->first();
                    if ($oldUser) {
                        $roleId = DB::table('roles')->where('name', 'ketua_kub')->value('id');
                        if ($roleId) {
                            DB::table('user_roles')->where([
                                'user_id' => $oldUser->id,
                                'role_id' => $roleId,
                            ])->delete();
                        }
                    }
                }
            }

            // Pasang role ketua_kub ke ketua baru
            if ($newKetuaId) {
                $newUser = User::where('umat_id', $newKetuaId)->first();
                if ($newUser) {
                    $roleId = DB::table('roles')->where('name', 'ketua_kub')->value('id');
                    if ($roleId) {
                        DB::table('user_roles')->insertOrIgnore([
                            'user_id'    => $newUser->id,
                            'role_id'    => $roleId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        return redirect()
            ->route('sekretariat.kub.index')
            ->with('success', 'Data KUB berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kub $kub)
    {
        // 1. Cek Keluarga
        if ($kub->keluarga()->exists()) {
            return back()->with('error', 'KUB "' . $kub->nama . '" tidak dapat dihapus karena masih memiliki data Keluarga yang terdaftar.');
        }

        // 2. Cek Mutasi Keluarga
        $hasMutasiKeluarga = MutasiKeluarga::where('kub_asal_id', $kub->id)
            ->orWhere('kub_tujuan_id', $kub->id)
            ->exists();
        if ($hasMutasiKeluarga) {
            return back()->with('error', 'KUB "' . $kub->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi keluarga.');
        }

        // 3. Cek Mutasi Umat
        $hasMutasiUmat = MutasiUmat::where('kub_asal_id', $kub->id)
            ->orWhere('kub_tujuan_id', $kub->id)
            ->exists();
        if ($hasMutasiUmat) {
            return back()->with('error', 'KUB "' . $kub->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi umat.');
        }

        try {
            $ketuaId = $kub->ketua_umat_id;
            $kub->delete();

            if ($ketuaId) {
                $stillKetua = Kub::where('ketua_umat_id', $ketuaId)->exists();
                if (!$stillKetua) {
                    $user = User::where('umat_id', $ketuaId)->first();
                    if ($user) {
                        $roleId = DB::table('roles')->where('name', 'ketua_kub')->value('id');
                        if ($roleId) {
                            DB::table('user_roles')->where([
                                'user_id' => $user->id,
                                'role_id' => $roleId,
                            ])->delete();
                        }
                    }
                }
            }

            return redirect()
                ->route('sekretariat.kub.index')
                ->with('success', 'Data KUB berhasil dihapus.');
        } catch (QueryException $e) {
            return back()->with('error', 'KUB "' . $kub->nama . '" tidak dapat dihapus karena masih memiliki relasi data lain di sistem.');
        }
    }
}
