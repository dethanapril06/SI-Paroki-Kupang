<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Klerus;
use App\Models\Kuasi;
use App\Models\Paroki;
use Illuminate\Http\Request;

class KuasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kuasi = Kuasi::with(['paroki', 'klerus'])->latest()->get();

        return view('sekretariat.kuasi.index', compact('kuasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paroki = Paroki::orderBy('nama')->get();
        $klerus = Klerus::orderBy('nama')->get();

        return view('sekretariat.kuasi.create', compact('paroki', 'klerus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'paroki_id' => ['required', 'exists:paroki,id'],
            'klerus_id' => ['nullable', 'exists:klerus,id'],
        ]);

        Kuasi::create($validated);

        return redirect()
            ->route('sekretariat.kuasi.index')
            ->with('success', 'Data kuasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kuasi $kuasi)
    {
        $kuasi->load(['paroki', 'klerus', 'stasi']);

        return view('sekretariat.kuasi.show', compact('kuasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kuasi $kuasi)
    {
        $paroki = Paroki::orderBy('nama')->get();
        $klerus = Klerus::orderBy('nama')->get();

        return view('sekretariat.kuasi.edit', compact('kuasi', 'paroki', 'klerus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kuasi $kuasi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'paroki_id' => ['required', 'exists:paroki,id'],
            'klerus_id' => ['nullable', 'exists:klerus,id'],
        ]);

        $kuasi->update($validated);

        return redirect()
            ->route('sekretariat.kuasi.index')
            ->with('success', 'Data kuasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kuasi $kuasi)
    {
        $kuasi->delete();

        return redirect()
            ->route('sekretariat.kuasi.index')
            ->with('success', 'Data kuasi berhasil dihapus.');
    }
}
