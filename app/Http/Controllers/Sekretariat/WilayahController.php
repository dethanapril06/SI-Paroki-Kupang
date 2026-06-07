<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Kuasi;
use App\Models\MutasiKeluarga;
use App\Models\MutasiUmat;
use App\Models\Paroki;
use App\Models\Stasi;
use App\Models\Umat;
use App\Models\Wilayah;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wilayah = Wilayah::with(['paroki', 'kuasi', 'stasi'])->latest()->get();

        return view('sekretariat.wilayah.index', compact('wilayah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paroki = Paroki::orderBy('nama')->get();
        $kuasi = Kuasi::orderBy('nama')->get();
        $stasi = Stasi::orderBy('nama')->get();

        return view('sekretariat.wilayah.create', compact('paroki', 'kuasi', 'stasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:255'],
            'paroki_id' => ['nullable', 'exists:paroki,id'],
            'kuasi_id'  => ['nullable', 'exists:kuasi,id'],
            'stasi_id'  => ['nullable', 'exists:stasi,id'],
        ]);

        if (!$this->hasExactlyOneParent($validated)) {
            return back()
                ->withErrors(['paroki_id' => 'Wilayah wajib memiliki tepat 1 parent: Paroki, Kuasi, atau Stasi.'])
                ->withInput();
        }

        Wilayah::create($validated);

        return redirect()
            ->route('sekretariat.wilayah.index')
            ->with('success', 'Data wilayah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wilayah $wilayah)
    {
        $wilayah->load(['paroki', 'kuasi', 'stasi']);

        return view('sekretariat.wilayah.show', compact('wilayah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wilayah $wilayah)
    {
        $paroki = Paroki::orderBy('nama')->get();
        $kuasi  = Kuasi::orderBy('nama')->get();
        $stasi  = Stasi::orderBy('nama')->get();
        // Umat yang bisa dipilih sebagai ketua: umat dalam KUB yang ada di wilayah ini
        $umat = Umat::whereHas('keluarga.kub', fn($q) => $q->where('wilayah_id', $wilayah->id))
            ->orderBy('nama')
            ->get();

        return view('sekretariat.wilayah.edit', compact('wilayah', 'paroki', 'kuasi', 'stasi', 'umat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wilayah $wilayah)
    {
        $validated = $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'ketua_umat_id'  => ['nullable', 'exists:umat,id'],
            'paroki_id'      => ['nullable', 'exists:paroki,id'],
            'kuasi_id'       => ['nullable', 'exists:kuasi,id'],
            'stasi_id'       => ['nullable', 'exists:stasi,id'],
        ]);

        if (!$this->hasExactlyOneParent($validated)) {
            return back()
                ->withErrors(['paroki_id' => 'Wilayah wajib memiliki tepat 1 parent: Paroki, Kuasi, atau Stasi.'])
                ->withInput();
        }

        $wilayah->update($validated);

        return redirect()
            ->route('sekretariat.wilayah.index')
            ->with('success', 'Data wilayah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wilayah $wilayah)
    {
        // 1. Cek KUB
        if ($wilayah->kub()->exists()) {
            return back()->with('error', 'Wilayah "' . $wilayah->nama . '" tidak dapat dihapus karena masih memiliki KUB yang terhubung.');
        }

        // 2. Cek Mutasi Keluarga
        $hasMutasiKeluarga = MutasiKeluarga::where('wilayah_asal_id', $wilayah->id)
            ->orWhere('wilayah_tujuan_id', $wilayah->id)
            ->exists();
        if ($hasMutasiKeluarga) {
            return back()->with('error', 'Wilayah "' . $wilayah->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi keluarga.');
        }

        // 3. Cek Mutasi Umat
        $hasMutasiUmat = MutasiUmat::where('wilayah_asal_id', $wilayah->id)
            ->orWhere('wilayah_tujuan_id', $wilayah->id)
            ->exists();
        if ($hasMutasiUmat) {
            return back()->with('error', 'Wilayah "' . $wilayah->nama . '" tidak dapat dihapus karena masih terhubung dengan riwayat mutasi umat.');
        }

        try {
            $wilayah->delete();

            return redirect()
                ->route('sekretariat.wilayah.index')
                ->with('success', 'Data wilayah berhasil dihapus.');
        } catch (QueryException $e) {
            return back()->with('error', 'Wilayah "' . $wilayah->nama . '" tidak dapat dihapus karena masih memiliki relasi data lain di sistem.');
        }
    }

    private function hasExactlyOneParent(array $data): bool
    {
        $parentCount = collect([
            $data['paroki_id'] ?? null,
            $data['kuasi_id'] ?? null,
            $data['stasi_id'] ?? null,
        ])->filter(fn($value) => !empty($value))->count();

        return $parentCount === 1;
    }
}
