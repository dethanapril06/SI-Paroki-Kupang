<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKategorial;
use App\Models\Kategorial;
use App\Models\Umat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnggotaKategorialController extends Controller
{
    /**
     * Form tambah anggota ke kategorial tertentu.
     */
    public function create(Kategorial $kategorial)
    {
        // Umat yang belum terdaftar di kategorial ini
        $anggotaIds = $kategorial->anggota()->pluck('umat.id');
        $umat       = Umat::aktif()
            ->whereNotIn('id', $anggotaIds)
            ->orderBy('nama')
            ->get();

        return view('sekretariat.anggota-kategorial.create', compact('kategorial', 'umat'));
    }

    /**
     * Simpan anggota baru ke kategorial.
     */
    public function store(Request $request, Kategorial $kategorial)
    {
        $validated = $request->validate([
            'umat_id'      => ['required', 'exists:umat,id'],
            'jabatan'      => ['required', 'in:Ketua,Wakil Ketua,Sekretaris,Bendahara,Anggota'],
            'bidang_tugas' => ['nullable', 'string', 'max:255'],
            'tanggal_bergabung' => ['required', 'date'],
            'status'       => ['required', 'in:Aktif,Tidak Aktif'],
        ]);

        // Cek duplikat (unique constraint: umat_id + kategorial_id)
        $sudahAda = AnggotaKategorial::where('umat_id', $validated['umat_id'])
            ->where('kategorial_id', $kategorial->id)
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors(['umat_id' => 'Umat ini sudah terdaftar sebagai anggota kategorial ini.'])
                ->withInput();
        }

        $anggotaKategorial = AnggotaKategorial::create([
            'umat_id'       => $validated['umat_id'],
            'kategorial_id' => $kategorial->id,
            'jabatan'       => $validated['jabatan'],
            'bidang_tugas'  => $validated['bidang_tugas'] ?? null,
            'tanggal_bergabung' => $validated['tanggal_bergabung'],
            'status'        => $validated['status'],
        ]);

        $this->syncKetuaRole($anggotaKategorial->umat_id);

        return redirect()
            ->route('sekretariat.kategorial.show', $kategorial)
            ->with('success', 'Anggota berhasil ditambahkan ke kategorial.');
    }

    /**
     * Form edit data keanggotaan.
     */
    public function edit(AnggotaKategorial $anggotaKategorial)
    {
        $anggotaKategorial->load(['umat', 'kategorial']);

        return view('sekretariat.anggota-kategorial.edit', compact('anggotaKategorial'));
    }

    /**
     * Simpan perubahan data keanggotaan.
     */
    public function update(Request $request, AnggotaKategorial $anggotaKategorial)
    {
        $validated = $request->validate([
            'jabatan'      => ['required', 'in:Ketua,Wakil Ketua,Sekretaris,Bendahara,Anggota'],
            'bidang_tugas' => ['nullable', 'string', 'max:255'],
            'tanggal_bergabung' => ['required', 'date'],
            'status'       => ['required', 'in:Aktif,Tidak Aktif'],
        ]);

        $anggotaKategorial->update($validated);

        $this->syncKetuaRole($anggotaKategorial->umat_id);

        return redirect()
            ->route('sekretariat.kategorial.show', $anggotaKategorial->kategorial_id)
            ->with('success', 'Data keanggotaan berhasil diperbarui.');
    }

    /**
     * Hapus anggota dari kategorial.
     */
    public function destroy(AnggotaKategorial $anggotaKategorial)
    {
        $umatId = $anggotaKategorial->umat_id;
        $kategorialId = $anggotaKategorial->kategorial_id;
        $anggotaKategorial->delete();

        $this->syncKetuaRole($umatId);

        return redirect()
            ->route('sekretariat.kategorial.show', $kategorialId)
            ->with('success', 'Anggota berhasil dihapus dari kategorial.');
    }

    /**
     * Sinkronkan ketua_umat_id di tabel kategorial dan role ketua_kategorial user.
     */
    private function syncKetuaRole(int $umatId): void
    {
        // Dapatkan daftar kategorial_id di mana umat ini menjabat sebagai Ketua
        $ketuaKategorialIds = AnggotaKategorial::where('umat_id', $umatId)
            ->where('jabatan', 'Ketua')
            ->pluck('kategorial_id')
            ->toArray();

        if (count($ketuaKategorialIds) > 0) {
            // Update kategorial-kategorial tersebut agar ketua_umat_id = $umatId
            Kategorial::whereIn('id', $ketuaKategorialIds)
                ->update(['ketua_umat_id' => $umatId]);

            // Bersihkan kategorial lain yang ketua_umat_id-nya masih menunjuk ke umat ini tapi jabatannya bukan Ketua lagi
            Kategorial::where('ketua_umat_id', $umatId)
                ->whereNotIn('id', $ketuaKategorialIds)
                ->update(['ketua_umat_id' => null]);

            // Pastikan user mendapatkan role ketua_kategorial
            $user = User::where('umat_id', $umatId)->first();
            if ($user) {
                $roleId = DB::table('roles')->where('name', 'ketua_kategorial')->value('id');
                if ($roleId) {
                    DB::table('user_roles')->insertOrIgnore([
                        'user_id'    => $user->id,
                        'role_id'    => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } else {
            // Umat ini tidak menjabat Ketua di kategorial manapun -> bersihkan semua ketua_umat_id miliknya
            Kategorial::where('ketua_umat_id', $umatId)
                ->update(['ketua_umat_id' => null]);

            // Cabut role ketua_kategorial
            $user = User::where('umat_id', $umatId)->first();
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
}
