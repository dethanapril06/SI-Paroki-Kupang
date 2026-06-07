<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Kub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KubController extends Controller
{
    /**
     * Ambil KUB milik ketua yang sedang login.
     */
    private function getMyKub(): Kub
    {
        $umatId = Auth::user()->umat_id;

        return Kub::where('ketua_umat_id', $umatId)->firstOrFail();
    }

    /**
     * Tampilkan detail KUB milik ketua yang sedang login.
     */
    public function show()
    {
        $kub = $this->getMyKub();
        $kub->load(['wilayah', 'ketuaUmat', 'keluarga.kepalaKeluarga']);

        return view('portal.kub.show', compact('kub'));
    }

    /**
     * Tampilkan form edit KUB milik ketua yang sedang login.
     */
    public function edit()
    {
        $kub = $this->getMyKub();

        return view('portal.kub.edit', compact('kub'));
    }

    /**
     * Update nama KUB milik ketua yang sedang login.
     */
    public function update(Request $request)
    {
        $kub = $this->getMyKub();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $kub->update($validated);

        return redirect()
            ->route('portal.kub.show')
            ->with('success', 'Nama KUB berhasil diperbarui.');
    }
}
