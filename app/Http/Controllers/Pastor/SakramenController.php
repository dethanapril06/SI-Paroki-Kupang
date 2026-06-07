<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\Sakramen;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SakramenController extends Controller
{
    /**
     * Daftar sakramen dengan filter jenis_sakramen.
     */
    public function index(Request $request): View
    {
        $jenis = $request->query('jenis', 'semua');

        $query = Sakramen::with([
                'umat',
                'paroki',
                'klerus',
                'baptis',
                'komuniPertama',
                'krisma',
                'pernikahan',
                'minyakSuci',
            ])
            ->latest('tanggal_penerimaan');

        if (in_array(strtoupper($jenis), ['BAPTIS', 'KOMUNI_PERTAMA', 'KRISMA', 'PERNIKAHAN', 'MINYAK_SUCI'])) {
            $query->where('jenis_sakramen', strtoupper($jenis));
        }

        $sakramenList = $query->paginate(20)->withQueryString();

        return view('pastor.sakramen.index', compact('sakramenList', 'jenis'));
    }
}
