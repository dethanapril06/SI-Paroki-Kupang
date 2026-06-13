<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UmatController extends Controller
{
    /**
     * Daftar semua umat (read-only direktori).
     */
    public function index(Request $request): View
    {
        $query = Umat::with(['keluarga.kub.wilayah', 'user'])
            ->aktif()
            ->orderBy('nama');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $umat = $query->get();

        return view('pastor.umat.index', compact('umat'));
    }

    /**
     * Detail umat.
     */
    public function show(Umat $umat): View
    {
        abort_unless($umat->status_keaktifan === 'aktif', 404);

        $umat->load([
            'keluarga.kub.wilayah',
            'user',
            'kategorial',
            'kubDiketuai',
            'kategorialDiketuai',
            'sakramen.paroki',
            'sakramen.klerus',
            'sakramen.baptis.klerus',
            'sakramen.baptis.bapakBaptis',
            'sakramen.baptis.ibuBaptis',
            'sakramen.komuniPertama',
            'sakramen.krisma.uskup',
            'sakramen.pernikahan.pasangan',
            'sakramen.minyakSuci',
        ]);

        return view('pastor.umat.show', compact('umat'));
    }
}
