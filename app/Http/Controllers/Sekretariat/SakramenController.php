<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Sakramen;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SakramenController extends Controller
{
    /**
     * Daftar semua sakramen (semua jenis).
     */
    public function index(): View
    {
        $sakramenList = Sakramen::with([
                'umat',
                'paroki',
                'klerus',
                'baptis',
                'komuniPertama',
                'krisma',
                'pernikahan',
                'minyakSuci',
            ])
            ->join('umat', 'sakramen.umat_id', '=', 'umat.id')
            ->orderBy('umat.nama')
            ->select('sakramen.*')
            ->paginate(20);

        return view('sekretariat.sakramen.index', compact('sakramenList'));
    }

    /**
     * Hapus sakramen beserta child-nya (cascade by DB).
     */
    public function destroy(Sakramen $sakramen): RedirectResponse
    {
        $sakramen->delete();

        return redirect()->route('sekretariat.sakramen.index')
            ->with('success', 'Data sakramen berhasil dihapus.');
    }
}
