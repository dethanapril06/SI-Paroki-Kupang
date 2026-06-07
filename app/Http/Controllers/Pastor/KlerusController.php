<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\Klerus;
use Illuminate\View\View;

class KlerusController extends Controller
{
    /**
     * Daftar klerus (read-only).
     */
    public function index(): View
    {
        $klerus = Klerus::latest()->get();

        return view('pastor.klerus.index', compact('klerus'));
    }
}
