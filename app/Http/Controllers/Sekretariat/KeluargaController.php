<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Kub;
use App\Models\Umat;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keluarga = Keluarga::with([
                'kub.wilayah',
                'kepalaKeluarga' => fn($q) => $q->aktif(),
            ])
            ->withCount(['umat' => fn($q) => $q->aktif()])
            ->latest()
            ->get();

        return view('sekretariat.keluarga.index', compact('keluarga'));
    }

    /**
     * Show the form for creating a new resource.
     * Kepala keluarga belum bisa dipilih saat create karena umat
     * belum ada — akan diisi setelah umat pertama ditambahkan.
     */
    public function create()
    {
        $kub = Kub::with('wilayah')->orderBy('nama')->get();

        return view('sekretariat.keluarga.create', compact('kub'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kub_id'               => ['required', 'exists:kub,id'],
            'alamat'               => ['required', 'string'],
            'status_tempat_tinggal' => ['required', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
        ]);

        $keluarga = Keluarga::create($validated);

        return redirect()
            ->route('sekretariat.keluarga.show', $keluarga)
            ->with('success', 'Data keluarga berhasil ditambahkan. Silakan tambahkan anggota keluarga.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Keluarga $keluarga)
    {
        $keluarga->load([
            'kub.wilayah',
            'kepalaKeluarga' => fn($q) => $q->aktif(),
            'umat' => fn($q) => $q->aktif()->with('sakramen')->orderBy('nama'),
        ]);

        return view('sekretariat.keluarga.show', compact('keluarga'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keluarga $keluarga)
    {
        $kub     = Kub::with('wilayah')->orderBy('nama')->get();
        $anggota = Umat::aktif()
            ->where('keluarga_id', $keluarga->id)
            ->orderBy('nama')
            ->get();

        return view('sekretariat.keluarga.edit', compact('keluarga', 'kub', 'anggota'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keluarga $keluarga)
    {
        $validated = $request->validate([
            'kub_id'                => ['required', 'exists:kub,id'],
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
                return back()->withErrors(['kepala_keluarga_id' => 'Kepala keluarga harus merupakan anggota keluarga ini.']);
            }
        }

        $keluarga->update($validated);

        return redirect()
            ->route('sekretariat.keluarga.show', $keluarga)
            ->with('success', 'Data keluarga berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keluarga $keluarga)
    {
        $keluarga->delete();

        return redirect()
            ->route('sekretariat.keluarga.index')
            ->with('success', 'Data keluarga berhasil dihapus.');
    }
}
