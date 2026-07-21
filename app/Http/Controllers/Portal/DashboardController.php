<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Kategorial;
use App\Models\Kematian;
use App\Models\Keluarga;
use App\Models\Kub;
use App\Models\Mutasi;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user()->loadMissing('roles');

        // ── Data dasar umat (semua role portal) ──────────────────────────────
        $umat = $user->umat_id
            ? Umat::aktif()->with([
                'keluarga.kub.wilayah',
                'sakramen',
                'baptis.baptis',
                'komuniPertama.komuniPertama',
                'krisma.krisma',
                'pernikahan.pernikahan',
                'kategorial' => fn($q) => $q->wherePivot('status', 'Aktif'),
                'kematian',
            ])->find($user->umat_id)
            : null;

        $keluarga = $umat?->keluarga;
        $kub      = $keluarga?->kub;
        $wilayah  = $kub?->wilayah;

        $sakramenDiterima = $umat?->sakramen ?? collect();
        $kategorialAktif  = $umat?->kategorial ?? collect();

        $mutasiTerbaru = Mutasi::with(['mutasiUmat', 'mutasiKeluarga', 'mutasiAgama'])
            ->where('pemohon_umat_id', $umat?->id)
            ->latest()->take(5)->get();
        $pendingMutasi = Mutasi::where('pemohon_umat_id', $umat?->id)->pending()->count();

        // ── Data KUB (hanya jika ketua_kub) ─────────────────────────────────
        $kubSaya      = null;
        $kubStats     = null;
        $daftarKeluarga = collect();

        if ($user->isKetuaKub()) {
            $kubSaya = Kub::where('ketua_umat_id', $user->umat_id)->first();

            if ($kubSaya) {
                $keluargaIds = Keluarga::where('kub_id', $kubSaya->id)->pluck('id');
                $umatIds     = Umat::aktif()->whereIn('keluarga_id', $keluargaIds)->pluck('id');
                $tahunIni    = now()->year;

                $kubStats = [
                    'total_umat'     => Umat::whereIn('keluarga_id', $keluargaIds)->where('status_almarhum', false)->where('status_keaktifan', 'aktif')->count(),
                    'total_keluarga' => $keluargaIds->count(),
                    'laki_laki'      => Umat::whereIn('keluarga_id', $keluargaIds)->where('jenis_kelamin', 'Laki-laki')->where('status_almarhum', false)->where('status_keaktifan', 'aktif')->count(),
                    'perempuan'      => Umat::whereIn('keluarga_id', $keluargaIds)->where('jenis_kelamin', 'Perempuan')->where('status_almarhum', false)->where('status_keaktifan', 'aktif')->count(),
                    'mutasi_tahun'   => Mutasi::whereYear('tanggal', $tahunIni)->where(function ($q) use ($umatIds, $keluargaIds) {
                        $q->whereHas('mutasiUmat', fn($m) => $m->whereIn('umat_id', $umatIds))
                          ->orWhereHas('mutasiKeluarga', fn($m) => $m->whereIn('keluarga_id', $keluargaIds))
                          ->orWhereHas('mutasiAgama', fn($m) => $m->whereIn('umat_id', $umatIds));
                    })->count(),
                    'baptis_tahun'   => Sakramen::whereIn('umat_id', $umatIds)->where('jenis_sakramen', 'BAPTIS')->whereYear('tanggal_penerimaan', $tahunIni)->count(),
                    'kematian_tahun' => Kematian::whereIn('umat_id', $umatIds)->whereYear('tanggal_meninggal', $tahunIni)->count(),
                ];

                $daftarKeluarga = Keluarga::with(['kepalaKeluarga' => fn($q) => $q->aktif()])
                    ->withCount(['umat as total_umat' => fn($q) => $q->aktif()->where('status_almarhum', false)])
                    ->where('kub_id', $kubSaya->id)->latest()->get();
            }
        }

        // ── Statistik sakramen keluarga (hanya jika kepala keluarga) ─────────
        $isKetuaKeluarga       = false;
        $keluargaStats         = null;
        $anggotaSakramenDetail = collect();

        if ($umat && $keluarga && (int) $keluarga->kepala_keluarga_id === (int) $umat->id) {
            $isKetuaKeluarga = true;

            // Muat semua anggota aktif beserta sakramennya
            $semuaAnggota = Umat::aktif()
                ->where('keluarga_id', $keluarga->id)
                ->with('sakramen')
                ->get();

            $jenisList = ['BAPTIS', 'KOMUNI_PERTAMA', 'KRISMA', 'PERNIKAHAN', 'MINYAK_SUCI'];
            $totalAnggota = $semuaAnggota->count();

            $keluargaStats = [];
            foreach ($jenisList as $jenis) {
                $sudah = $semuaAnggota->filter(
                    fn($u) => $u->sakramen->pluck('jenis_sakramen')->contains($jenis)
                )->count();
                $keluargaStats[$jenis] = [
                    'sudah' => $sudah,
                    'total' => $totalAnggota,
                    'persen' => $totalAnggota > 0 ? round($sudah / $totalAnggota * 100) : 0,
                ];
            }

            $anggotaSakramenDetail = $semuaAnggota;
        }

        // ── Data Kategorial (hanya jika ketua_kategorial) ────────────────────
        $kategorialDiPimpin = collect();
        if ($user->isKetuaKategorial()) {
            $kategorialDiPimpin = Kategorial::where('ketua_umat_id', $user->umat_id)
                ->withCount(['anggota as anggota_aktif' => fn($q) => $q->where('anggota_kategorial.status', 'Aktif')])
                ->get();
        }

        return view('portal.dashboard', compact(
            'umat', 'keluarga', 'kub', 'wilayah',
            'sakramenDiterima', 'kategorialAktif',
            'mutasiTerbaru', 'pendingMutasi',
            'kubSaya', 'kubStats', 'daftarKeluarga',
            'kategorialDiPimpin',
            'isKetuaKeluarga', 'keluargaStats', 'anggotaSakramenDetail',
        ));
    }
}
