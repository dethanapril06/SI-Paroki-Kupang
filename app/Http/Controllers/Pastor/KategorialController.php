<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\Kategorial;
use Illuminate\View\View;

class KategorialController extends Controller
{
    /**
     * Daftar kategorial (read-only).
     */
    public function index(): View
    {
        $kategorial = Kategorial::with(['ketuaUmat'])
            ->withCount('anggota')
            ->latest()
            ->get();

        return view('pastor.kategorial.index', compact('kategorial'));
    }
}
