<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Kategorial;
use App\Models\Kub;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * Daftar semua pengguna sistem.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'umat', 'klerus'])->latest();

        // Filter by role via relasi
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        // Cari berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pengguna = $query->get();

        // Lampirkan info jabatan (ketua KUB / kategorial) ke setiap user
        // agar kolom "Jabatan" bisa ditampilkan di tabel tanpa query tambahan
        $pengguna->each(function (User $user) {
            $user->jabatan_kub        = $user->umat_id
                ? Kub::where('ketua_umat_id', $user->umat_id)->first(['id', 'nama'])
                : null;
            $user->jabatan_kategorial = $user->umat_id
                ? Kategorial::where('ketua_umat_id', $user->umat_id)->get(['id', 'nama'])
                : collect();
        });

        $roles = Role::orderBy('name')->get();

        return view('sekretariat.pengguna.index', compact('pengguna', 'roles'));
    }

    /**
     * Form edit email & role pengguna.
     */
    public function edit(User $user)
    {
        $user->load('roles');

        // Deteksi jabatan dari data sistem (untuk info)
        $jabatanKub        = $user->umat_id
            ? Kub::where('ketua_umat_id', $user->umat_id)->first()
            : null;
        $jabatanKategorial = $user->umat_id
            ? Kategorial::where('ketua_umat_id', $user->umat_id)->get()
            : collect();

        return view('sekretariat.pengguna.edit', compact(
            'user', 'jabatanKub', 'jabatanKategorial'
        ));
    }

    /**
     * Reset password pengguna ke "password".
     */
    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('password'),
        ]);

        return redirect()
            ->route('sekretariat.pengguna.index')
            ->with('success', "Password {$user->name} berhasil direset ke \"password\".");
    }

    /**
     * Update email pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);

        $user->update(['email' => $validated['email']]);

        return redirect()
            ->route('sekretariat.pengguna.index')
            ->with('success', "Data akun {$user->name} berhasil diperbarui.");
    }
}
