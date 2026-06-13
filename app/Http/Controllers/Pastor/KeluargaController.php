<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeluargaController extends Controller
{
    /**
     * Daftar keluarga (read-only direktori).
     */
    public function index(): View
    {
        $keluarga = Keluarga::with([
                'kub.wilayah',
                'kepalaKeluarga' => fn($q) => $q->aktif(),
            ])
            ->withCount(['umat' => fn($q) => $q->aktif()])
            ->latest()
            ->get();

        return view('pastor.keluarga.index', compact('keluarga'));
    }

    /**
     * Detail keluarga.
     */
    public function show(Keluarga $keluarga): View
    {
        $keluarga->load([
            'kub.wilayah',
            'kepalaKeluarga' => fn($q) => $q->aktif(),
            'umat' => fn($q) => $q->aktif()->with('sakramen')->orderBy('nama'),
        ]);

        return view('pastor.keluarga.show', compact('keluarga'));
    }
}
