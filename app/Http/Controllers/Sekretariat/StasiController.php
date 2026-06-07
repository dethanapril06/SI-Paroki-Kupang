<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Kuasi;
use App\Models\Paroki;
use App\Models\Stasi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class StasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stasi = Stasi::with(['paroki', 'kuasi'])->latest()->get();

        return view('sekretariat.stasi.index', compact('stasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paroki = Paroki::orderBy('nama')->get();
        $kuasi = Kuasi::orderBy('nama')->get();

        return view('sekretariat.stasi.create', compact('paroki', 'kuasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'koordinator' => ['required', 'string', 'max:255'],
            'paroki_id' => ['nullable', 'exists:paroki,id', 'required_without:kuasi_id'],
            'kuasi_id' => ['nullable', 'exists:kuasi,id', 'required_without:paroki_id'],
        ]);

        if (!empty($validated['paroki_id']) && !empty($validated['kuasi_id'])) {
            return back()
                ->withErrors(['paroki_id' => 'Stasi hanya boleh terhubung ke paroki atau kuasi, tidak keduanya.'])
                ->withInput();
        }

        Stasi::create($validated);

        return redirect()
            ->route('sekretariat.stasi.index')
            ->with('success', 'Data stasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stasi $stasi)
    {
        $stasi->load(['paroki', 'kuasi']);

        return view('sekretariat.stasi.show', compact('stasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stasi $stasi)
    {
        $paroki = Paroki::orderBy('nama')->get();
        $kuasi = Kuasi::orderBy('nama')->get();

        return view('sekretariat.stasi.edit', compact('stasi', 'paroki', 'kuasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stasi $stasi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'koordinator' => ['required', 'string', 'max:255'],
            'paroki_id' => ['nullable', 'exists:paroki,id', 'required_without:kuasi_id'],
            'kuasi_id' => ['nullable', 'exists:kuasi,id', 'required_without:paroki_id'],
        ]);

        if (!empty($validated['paroki_id']) && !empty($validated['kuasi_id'])) {
            return back()
                ->withErrors(['paroki_id' => 'Stasi hanya boleh terhubung ke paroki atau kuasi, tidak keduanya.'])
                ->withInput();
        }

        $stasi->update($validated);

        return redirect()
            ->route('sekretariat.stasi.index')
            ->with('success', 'Data stasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stasi $stasi)
    {
        // 1. Cek Wilayah
        if ($stasi->wilayah()->exists()) {
            return back()->with('error', 'Stasi "' . $stasi->nama . '" tidak dapat dihapus karena masih memiliki data Wilayah yang terhubung.');
        }

        try {
            $stasi->delete();

            return redirect()
                ->route('sekretariat.stasi.index')
                ->with('success', 'Data stasi berhasil dihapus.');
        } catch (QueryException $e) {
            return back()->with('error', 'Stasi "' . $stasi->nama . '" tidak dapat dihapus karena masih memiliki relasi data lain di sistem.');
        }
    }
}
