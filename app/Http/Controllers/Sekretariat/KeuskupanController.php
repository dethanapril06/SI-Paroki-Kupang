<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Keuskupan;
use App\Models\Klerus;
use App\Models\MutasiKeluarga;
use App\Models\MutasiUmat;
use App\Models\Paroki;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KeuskupanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keuskupan = Keuskupan::with('klerus')->latest()->get();

        return view('sekretariat.keuskupan.index', compact('keuskupan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $klerus = Klerus::orderBy('nama')->get();

        return view('sekretariat.keuskupan.create', compact('klerus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'klerus_id' => ['nullable', 'exists:klerus,id'],
        ]);

        Keuskupan::create($validated);

        return redirect()
            ->route('sekretariat.keuskupan.index')
            ->with('success', 'Data keuskupan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Keuskupan $keuskupan)
    {
        $keuskupan->load(['klerus', 'paroki']);

        return view('sekretariat.keuskupan.show', compact('keuskupan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keuskupan $keuskupan)
    {
        $klerus = Klerus::orderBy('nama')->get();

        return view('sekretariat.keuskupan.edit', compact('keuskupan', 'klerus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keuskupan $keuskupan)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'klerus_id' => ['nullable', 'exists:klerus,id'],
        ]);

        $keuskupan->update($validated);

        return redirect()
            ->route('sekretariat.keuskupan.index')
            ->with('success', 'Data keuskupan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keuskupan $keuskupan)
    {
        // 1. Cek Paroki
        if ($keuskupan->paroki()->exists()) {
            return back()->with('error', 'Keuskupan "' . $keuskupan->nama . '" tidak dapat dihapus karena masih memiliki data Paroki yang terdaftar.');
        }

        // 2. Cek Mutasi Keluarga
        $hasMutasiKeluarga = MutasiKeluarga::where('keuskupan_asal_id', $keuskupan->id)
            ->orWhere('keuskupan_tujuan_id', $keuskupan->id)
            ->exists();
        if ($hasMutasiKeluarga) {
            return back()->with('error', 'Keuskupan "' . $keuskupan->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi keluarga.');
        }

        // 3. Cek Mutasi Umat
        $hasMutasiUmat = MutasiUmat::where('keuskupan_asal_id', $keuskupan->id)
            ->orWhere('keuskupan_tujuan_id', $keuskupan->id)
            ->exists();
        if ($hasMutasiUmat) {
            return back()->with('error', 'Keuskupan "' . $keuskupan->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi umat.');
        }

        try {
            $keuskupan->delete();

            return redirect()
                ->route('sekretariat.keuskupan.index')
                ->with('success', 'Data keuskupan berhasil dihapus.');
        } catch (QueryException $e) {
            return back()->with('error', 'Keuskupan "' . $keuskupan->nama . '" tidak dapat dihapus karena masih memiliki relasi data lain di sistem.');
        }
    }
}
