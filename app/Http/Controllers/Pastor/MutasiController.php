<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\Mutasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiController extends Controller
{
    /**
     * Daftar riwayat mutasi (read-only).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'semua');

        $query = Mutasi::with([
                'mutasiUmat.umat',
                'mutasiKeluarga.keluarga',
                'mutasiAgama.umat',
                'pemohon',
                'diprosesOleh',
            ])
            ->latest();

        if (in_array($status, ['pending', 'disetujui', 'ditolak'])) {
            $query->where('status', $status);
        }

        $mutasiList = $query->paginate(20)->withQueryString();

        return view('pastor.mutasi.index', compact('mutasiList', 'status'));
    }
}
