<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Kub;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeluargaController extends Controller
{
    /**
     * Ambil KUB milik ketua yang sedang login.
     */
    private function getMyKub(): Kub
    {
        return Kub::where('ketua_umat_id', Auth::user()->umat_id)->firstOrFail();
    }

    /**
     * Pastikan keluarga ini benar-benar milik KUB sang ketua.
     */
    private function authorizeKeluarga(Keluarga $keluarga): void
    {
        $myKub = $this->getMyKub();

        if ((int) $keluarga->kub_id !== (int) $myKub->id) {
            abort(403, 'Anda tidak memiliki akses ke keluarga ini.');
        }
    }

    /**
     * Daftar semua keluarga dalam KUB milik ketua.
     */
    public function index()
    {
        $myKub = $this->getMyKub();

        $keluarga = Keluarga::with(['kepalaKeluarga' => fn($q) => $q->aktif()])
            ->withCount(['umat' => fn($q) => $q->aktif()])
            ->where('kub_id', $myKub->id)
            ->latest()
            ->get();

        return view('portal.keluarga.index', compact('keluarga', 'myKub'));
    }

    /**
     * Form tambah keluarga baru — kub_id otomatis dari KUB sang ketua.
     */
    public function create()
    {
        $myKub = $this->getMyKub();

        return view('portal.keluarga.create', compact('myKub'));
    }

    /**
     * Simpan keluarga baru, kub_id dikunci ke KUB sang ketua.
     */
    public function store(Request $request)
    {
        $myKub = $this->getMyKub();

        $validated = $request->validate([
            'alamat'                => ['required', 'string'],
            'status_tempat_tinggal' => ['required', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
        ]);

        // kub_id dikunci, tidak bisa dimanipulasi dari form
        $validated['kub_id'] = $myKub->id;

        $keluarga = Keluarga::create($validated);

        return redirect()
            ->route('portal.keluarga.show', $keluarga)
            ->with('success', 'Data keluarga berhasil ditambahkan. Silakan tambahkan anggota keluarga.');
    }

    /**
     * Detail keluarga beserta anggotanya.
     */
    public function show(Keluarga $keluarga)
    {
        $this->authorizeKeluarga($keluarga);

        $keluarga->load([
            'kub.wilayah',
            'kepalaKeluarga' => fn($q) => $q->aktif(),
            'umat' => fn($q) => $q->aktif()->with('sakramen')->orderBy('nama'),
        ]);

        return view('portal.keluarga.show', compact('keluarga'));
    }

    /**
     * Form edit keluarga.
     */
    public function edit(Keluarga $keluarga)
    {
        $this->authorizeKeluarga($keluarga);

        $anggota = Umat::aktif()
            ->where('keluarga_id', $keluarga->id)
            ->orderBy('nama')
            ->get();

        return view('portal.keluarga.edit', compact('keluarga', 'anggota'));
    }

    /**
     * Update keluarga — kub_id tidak bisa diubah oleh ketua.
     */
    public function update(Request $request, Keluarga $keluarga)
    {
        $this->authorizeKeluarga($keluarga);

        $validated = $request->validate([
            'alamat'                => ['required', 'string'],
            'status_tempat_tinggal' => ['required', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
            'kepala_keluarga_id'    => ['nullable', 'exists:umat,id'],
        ]);

        // Pastikan kepala keluarga yang dipilih adalah anggota keluarga ini
        if (!empty($validated['kepala_keluarga_id'])) {
            $isAnggota = Umat::where('id', $validated['kepala_keluarga_id'])
                ->aktif()
                ->where('keluarga_id', $keluarga->id)
                ->exists();

            if (!$isAnggota) {
                return back()->withErrors([
                    'kepala_keluarga_id' => 'Kepala keluarga harus merupakan anggota keluarga ini.',
                ]);
            }
        }

        $keluarga->update($validated);

        return redirect()
            ->route('portal.keluarga.show', $keluarga)
            ->with('success', 'Data keluarga berhasil diperbarui.');
    }

    /**
     * Hapus keluarga beserta seluruh anggotanya.
     */
    public function destroy(Keluarga $keluarga)
    {
        $this->authorizeKeluarga($keluarga);

        $keluarga->delete();

        return redirect()
            ->route('portal.keluarga.index')
            ->with('success', 'Data keluarga berhasil dihapus.');
    }
}
