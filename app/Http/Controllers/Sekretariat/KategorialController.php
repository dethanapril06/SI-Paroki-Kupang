<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Kategorial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KategorialController extends Controller
{
    public function index()
    {
        $kategorial = Kategorial::with(['ketuaUmat'])
            ->withCount('anggota')
            ->latest()
            ->get();

        return view('sekretariat.kategorial.index', compact('kategorial'));
    }

    public function create()
    {
        return view('sekretariat.kategorial.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        Kategorial::create($validated);

        return redirect()
            ->route('sekretariat.kategorial.index')
            ->with('success', 'Kategorial berhasil ditambahkan.');
    }

    public function show(Kategorial $kategorial)
    {
        $kategorial->load([
            'ketuaUmat',
            'anggotaKategorial.umat' => fn($q) => $q->orderBy('nama'),
        ]);

        return view('sekretariat.kategorial.show', compact('kategorial'));
    }

    public function edit(Kategorial $kategorial)
    {
        // Hanya anggota yang terdaftar di kategorial ini yang bisa jadi ketua
        $anggota = $kategorial->anggota()->orderBy('nama')->get();

        return view('sekretariat.kategorial.edit', compact('kategorial', 'anggota'));
    }

    public function update(Request $request, Kategorial $kategorial)
    {
        $validated = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'ketua_umat_id' => ['nullable', 'exists:umat,id'],
        ]);

        $oldKetuaId = $kategorial->getOriginal('ketua_umat_id');
        $newKetuaId = $validated['ketua_umat_id'] ?? null;

        $kategorial->update($validated);

        if ($oldKetuaId != $newKetuaId) {
            // Cabut role ketua_kategorial dari ketua lama jika dia tidak lagi memegang jabatan ketua Kategorial lain
            if ($oldKetuaId) {
                $stillKetua = Kategorial::where('ketua_umat_id', $oldKetuaId)->where('id', '!=', $kategorial->id)->exists();
                if (!$stillKetua) {
                    $oldUser = User::where('umat_id', $oldKetuaId)->first();
                    if ($oldUser) {
                        $roleId = DB::table('roles')->where('name', 'ketua_kategorial')->value('id');
                        if ($roleId) {
                            DB::table('user_roles')->where([
                                'user_id' => $oldUser->id,
                                'role_id' => $roleId,
                            ])->delete();
                        }
                    }
                }
            }

            // Pasang role ketua_kategorial ke ketua baru
            if ($newKetuaId) {
                $newUser = User::where('umat_id', $newKetuaId)->first();
                if ($newUser) {
                    $roleId = DB::table('roles')->where('name', 'ketua_kategorial')->value('id');
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
            ->route('sekretariat.kategorial.show', $kategorial)
            ->with('success', 'Data kategorial berhasil diperbarui.');
    }

    public function destroy(Kategorial $kategorial)
    {
        $ketuaId = $kategorial->ketua_umat_id;
        $kategorial->delete();

        if ($ketuaId) {
            $stillKetua = Kategorial::where('ketua_umat_id', $ketuaId)->exists();
            if (!$stillKetua) {
                $user = User::where('umat_id', $ketuaId)->first();
                if ($user) {
                    $roleId = DB::table('roles')->where('name', 'ketua_kategorial')->value('id');
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
            ->route('sekretariat.kategorial.index')
            ->with('success', 'Kategorial berhasil dihapus.');
    }
}
