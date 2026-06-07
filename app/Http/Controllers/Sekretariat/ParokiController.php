<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Keuskupan;
use App\Models\Klerus;
use App\Models\MutasiKeluarga;
use App\Models\MutasiUmat;
use App\Models\Paroki;
use App\Models\Sakramen;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ParokiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paroki = Paroki::with(['keuskupan', 'klerus'])->latest()->paginate(10);

        return view('sekretariat.paroki.index', compact('paroki'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $keuskupan = Keuskupan::orderBy('nama')->get();
        $klerus = Klerus::orderBy('nama')->get();

        return view('sekretariat.paroki.create', compact('keuskupan', 'klerus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'keuskupan_id' => ['required', 'exists:keuskupan,id'],
            'klerus_id' => ['nullable', 'exists:klerus,id'],
        ]);

        Paroki::create($validated);

        return redirect()
            ->route('sekretariat.paroki.index')
            ->with('success', 'Data paroki berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paroki $paroki)
    {
        $paroki->load(['keuskupan', 'klerus', 'kuasi', 'stasi']);

        return view('sekretariat.paroki.show', compact('paroki'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paroki $paroki)
    {
        $keuskupan = Keuskupan::orderBy('nama')->get();
        $klerus = Klerus::orderBy('nama')->get();

        return view('sekretariat.paroki.edit', compact('paroki', 'keuskupan', 'klerus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paroki $paroki)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'keuskupan_id' => ['required', 'exists:keuskupan,id'],
            'klerus_id' => ['nullable', 'exists:klerus,id'],
        ]);

        $paroki->update($validated);

        return redirect()
            ->route('sekretariat.paroki.index')
            ->with('success', 'Data paroki berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paroki $paroki)
    {
        // 1. Cek Wilayah
        if ($paroki->wilayah()->exists()) {
            return back()->with('error', 'Paroki "' . $paroki->nama . '" tidak dapat dihapus karena masih memiliki Wilayah yang terhubung.');
        }

        // 2. Cek Stasi
        if ($paroki->stasi()->exists()) {
            return back()->with('error', 'Paroki "' . $paroki->nama . '" tidak dapat dihapus karena masih memiliki Stasi yang terhubung.');
        }

        // 3. Cek Kuasi
        if ($paroki->kuasi()->exists()) {
            return back()->with('error', 'Paroki "' . $paroki->nama . '" tidak dapat dihapus karena masih memiliki Kuasi yang terhubung.');
        }

        // 4. Cek Sakramen
        if (Sakramen::where('paroki_id', $paroki->id)->exists()) {
            return back()->with('error', 'Paroki "' . $paroki->nama . '" tidak dapat dihapus karena masih terhubung dengan data penerimaan sakramen.');
        }

        // 5. Cek Mutasi Keluarga
        $hasMutasiKeluarga = MutasiKeluarga::where('paroki_asal_id', $paroki->id)
            ->orWhere('paroki_tujuan_id', $paroki->id)
            ->exists();
        if ($hasMutasiKeluarga) {
            return back()->with('error', 'Paroki "' . $paroki->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi keluarga.');
        }

        // 6. Cek Mutasi Umat
        $hasMutasiUmat = MutasiUmat::where('paroki_asal_id', $paroki->id)
            ->orWhere('paroki_tujuan_id', $paroki->id)
            ->exists();
        if ($hasMutasiUmat) {
            return back()->with('error', 'Paroki "' . $paroki->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi umat.');
        }

        try {
            $paroki->delete();

            return redirect()
                ->route('sekretariat.paroki.index')
                ->with('success', 'Data paroki berhasil dihapus.');
        } catch (QueryException $e) {
            return back()->with('error', 'Paroki "' . $paroki->nama . '" tidak dapat dihapus karena masih memiliki relasi data lain di sistem.');
        }
    }
}
