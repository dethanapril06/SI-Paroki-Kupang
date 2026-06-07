<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\KeanggotaanDpp;
use App\Models\Keluarga;
use App\Models\Kematian;
use App\Models\Kub;
use App\Models\Mutasi;
use App\Models\Sakramen;
use App\Models\Umat;
use App\Models\Wilayah;
use App\Models\Kategorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = intval($request->query('tahun', now()->year));

        // Get the minimum year from tables to build dynamic year select range
        $minYearSakramen = Sakramen::selectRaw('MIN(YEAR(tanggal_penerimaan)) as min_year')->value('min_year');
        $minYearMutasi = Mutasi::selectRaw('MIN(YEAR(tanggal)) as min_year')->value('min_year');
        $minYearKematian = Kematian::selectRaw('MIN(YEAR(tanggal_meninggal)) as min_year')->value('min_year');

        $years = array_filter([$minYearSakramen, $minYearMutasi, $minYearKematian]);
        $minYear = !empty($years) ? min($years) : now()->year - 4;
        $minYear = max($minYear, now()->year - 10);
        $minYear = min($minYear, now()->year);

        $daftarTahun = range(now()->year, $minYear);

        // ── Stat Cards ────────────────────────────────────────────────────────
        $totalUmat      = Umat::where('status_almarhum', false)->where('status_keaktifan', 'aktif')->count();
        $totalKeluarga  = Keluarga::count();
        $totalWilayah   = Wilayah::count();
        $totalKub       = Kub::count();

        $baptisTahunIni   = Sakramen::where('jenis_sakramen', 'BAPTIS')
                                ->whereYear('tanggal_penerimaan', $tahun)
                                ->count();

        $kematianTahunIni = Kematian::whereYear('tanggal_meninggal', $tahun)->count();

        $mutasiTahunIni   = Mutasi::whereYear('tanggal', $tahun)->count();
        $mutasiUmat       = Mutasi::where('jenis', 'umat')->whereYear('tanggal', $tahun)->count();
        $mutasiKeluarga   = Mutasi::where('jenis', 'keluarga')->whereYear('tanggal', $tahun)->count();
        $mutasiAgama      = Mutasi::where('jenis', 'agama')->whereYear('tanggal', $tahun)->count();

        $totalDppAktif  = KeanggotaanDpp::where('status_aktif', 'Aktif')->count();

        // ── Jenis Kelamin ─────────────────────────────────────────────────────
        $lakiLaki  = Umat::where('jenis_kelamin', 'Laki-laki')->where('status_almarhum', false)->where('status_keaktifan', 'aktif')->count();
        $perempuan = Umat::where('jenis_kelamin', 'Perempuan')->where('status_almarhum', false)->where('status_keaktifan', 'aktif')->count();

        // ── Umat per Wilayah (chart bar) ──────────────────────────────────────
        $umatPerWilayah = Wilayah::addSelect([
            'total_umat' => Umat::selectRaw('COUNT(*)')
                ->join('keluarga', 'keluarga.id', '=', 'umat.keluarga_id')
                ->join('kub', 'kub.id', '=', 'keluarga.kub_id')
                ->whereColumn('kub.wilayah_id', 'wilayah.id')
                ->where('umat.status_almarhum', false)
                ->where('umat.status_keaktifan', 'aktif')
                ->whereNull('umat.deleted_at'),
        ])->get();

        $wilayahLabels = $umatPerWilayah->pluck('nama')->toArray();
        $wilayahData   = $umatPerWilayah->pluck('total_umat')->toArray();

        // ── Sakramen per Jenis tahun ini (chart bar horizontal) ───────────────
        $sakramenPerJenis = Sakramen::select('jenis_sakramen', DB::raw('count(*) as total'))
            ->whereYear('tanggal_penerimaan', $tahun)
            ->groupBy('jenis_sakramen')
            ->pluck('total', 'jenis_sakramen');

        $jenisSakramen = ['BAPTIS', 'KOMUNI_PERTAMA', 'KRISMA', 'PERNIKAHAN', 'MINYAK_SUCI'];
        $sakramenLabels = array_map(fn($j) => str_replace('_', ' ', $j), $jenisSakramen);
        $sakramenData   = array_map(fn($j) => $sakramenPerJenis[$j] ?? 0, $jenisSakramen);

        // ── Tabel terbaru ─────────────────────────────────────────────────────
        $kematianTerbaru = Kematian::with(['umat.keluarga.kub.wilayah'])
                            ->orderByDesc('tanggal_meninggal')
                            ->limit(6)
                            ->get();

        $sakramenTerbaru = Sakramen::with(['umat', 'klerus', 'baptis', 'minyakSuci', 'komuniPertama', 'krisma'])
                            ->orderByDesc('tanggal_penerimaan')
                            ->limit(7)
                            ->get();

        $dppAktif = KeanggotaanDpp::with('umat')
                        ->where('status_aktif', 'Aktif')
                        ->orderByRaw("FIELD(jabatan,
                            'Ketua','Wakil Ketua','Sekretaris','Bendahara',
                            'Koordinator Bidang','Anggota','Lainnya'
                        )")
                        ->limit(7)
                        ->get();

        // ── Kategorial aktif ──────────────────────────────────────────────────
        $kategorialList = Kategorial::withCount([
                            'anggota as anggota_aktif_count' => fn($q) =>
                                $q->where('status', 'Aktif'),
                        ])->orderByDesc('anggota_aktif_count')->get();

        return view('sekretariat.dashboard', compact(
            'totalUmat', 'totalKeluarga', 'totalWilayah', 'totalKub',
            'baptisTahunIni', 'kematianTahunIni',
            'mutasiTahunIni', 'mutasiUmat', 'mutasiKeluarga', 'mutasiAgama',
            'totalDppAktif',
            'lakiLaki', 'perempuan',
            'wilayahLabels', 'wilayahData',
            'sakramenLabels', 'sakramenData',
            'kematianTerbaru', 'sakramenTerbaru', 'dppAktif',
            'kategorialList',
            'tahun', 'daftarTahun',
        ));
    }
}