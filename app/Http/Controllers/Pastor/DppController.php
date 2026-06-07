<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\KeanggotaanDpp;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DppController extends Controller
{
    /**
     * Daftar anggota DPP (read-only).
     */
    public function index(Request $request): View
    {
        $query = KeanggotaanDpp::with('umat');

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        if ($request->filled('bidang_tugas')) {
            $query->where('bidang_tugas', 'like', '%' . $request->bidang_tugas . '%');
        }

        $anggota = $query->orderByRaw("FIELD(jabatan,
            'Ketua','Wakil Ketua','Sekretaris','Bendahara',
            'Koordinator Bidang','Anggota','Lainnya'
        )")->paginate(15)->withQueryString();

        return view('pastor.dpp.index', [
            'anggota'     => $anggota,
            'listJabatan' => KeanggotaanDpp::JABATAN,
            'listStatus'  => KeanggotaanDpp::STATUS,
            'filters'     => $request->only(['jabatan', 'status_aktif', 'bidang_tugas']),
        ]);
    }
}
