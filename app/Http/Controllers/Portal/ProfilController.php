<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    /**
     * Tampilkan form edit data pribadi umat yang sedang login.
     */
    public function edit()
    {
        $umat = $this->getMyUmat();

        if (!$umat) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Data umat belum terhubung ke akun ini. Hubungi sekretariat.');
        }

        return view('portal.profil.edit', compact('umat'));
    }

    /**
     * Simpan perubahan data pribadi.
     * Semua field personal bisa diedit sendiri.
     * Yang TIDAK bisa diubah sendiri: keluarga_id (harus via mutasi).
     */
    public function update(Request $request)
    {
        $umat = $this->getMyUmat();

        if (!$umat) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Data umat tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama'                   => ['required', 'string', 'max:255'],
            'tempat_lahir'           => ['required', 'string', 'max:255'],
            'tanggal_lahir'          => ['required', 'date'],
            'jenis_kelamin'          => ['required', 'in:Laki-laki,Perempuan'],
            'nama_ayah'              => ['nullable', 'string', 'max:255'],
            'nama_ibu'               => ['nullable', 'string', 'max:255'],
            'status_pernikahan'      => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'no_telepon'             => ['nullable', 'string', 'max:20'],
            'golongan_darah'         => ['nullable', 'in:A,B,AB,O'],
            'pendidikan'             => ['nullable', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'pekerjaan'              => ['nullable', 'string', 'max:255'],
            'penyandang_disabilitas' => ['boolean'],
        ]);

        $validated['penyandang_disabilitas'] = $request->boolean('penyandang_disabilitas');

        $umat->update($validated);

        return redirect()->route('portal.dashboard')
            ->with('success', 'Data pribadi berhasil diperbarui.');
    }

    /**
     * Helper: ambil data Umat milik user yang sedang login.
     */
    private function getMyUmat(): ?Umat
    {
        $umatId = Auth::user()->umat_id;
        return $umatId ? Umat::find($umatId) : null;
    }
}
