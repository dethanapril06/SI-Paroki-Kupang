<?php

namespace App\Http\Controllers\Pastor;

use App\Http\Controllers\Controller;
use App\Models\Kub;
use App\Models\Mutasi;
use App\Models\Sakramen;
use App\Models\Umat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    /**
     * Tampilan Dashboard Utama Laporan Pastor
     */
    public function index(): View
    {
        $kubs = Kub::with('wilayah')->orderBy('nama')->get();
        return view('pastor.laporan.index', compact('kubs'));
    }

    /**
     * Ekspor PDF Laporan Administrasi Sakramen
     */
    public function sakramenPdf(Request $request)
    {
        $jenis = $request->query('jenis', 'semua');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalSelesai = $request->query('tanggal_selesai');
        $kubId = $request->query('kub_id');

        $query = Sakramen::with([
            'umat.keluarga.kub',
            'paroki',
            'klerus',
            'baptis',
            'komuniPertama',
            'krisma',
            'pernikahan',
            'minyakSuci'
        ])->whereHas('umat', fn($q) => $q->aktif());

        if (in_array(strtoupper($jenis), ['BAPTIS', 'KOMUNI_PERTAMA', 'KRISMA', 'PERNIKAHAN', 'MINYAK_SUCI'])) {
            $query->where('jenis_sakramen', strtoupper($jenis));
        }

        if ($tanggalMulai) {
            $query->whereDate('tanggal_penerimaan', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $query->whereDate('tanggal_penerimaan', '<=', $tanggalSelesai);
        }

        if ($kubId) {
            $query->whereHas('umat.keluarga', function ($q) use ($kubId) {
                $q->where('kub_id', $kubId);
            });
        }

        $sakramenList = $query->orderBy('tanggal_penerimaan', 'desc')->get();

        $selectedKub = $kubId ? Kub::find($kubId) : null;
        $filters = [
            'jenis' => $jenis,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'kub' => $selectedKub ? $selectedKub->nama : 'Semua KUB',
        ];

        $pdf = Pdf::loadView('pastor.laporan.pdf.sakramen', compact('sakramenList', 'filters'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_Sakramen_' . ($jenis !== 'semua' ? strtolower($jenis) : 'semua') . '_' . date('Ymd_His') . '.pdf';

        if ($request->query('action') === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Ekspor PDF Laporan Demografi & Statistik Umat
     */
    public function umatPdf(Request $request)
    {
        $kubId = $request->query('kub_id');
        $jenisKelamin = $request->query('jenis_kelamin', 'semua');
        $statusAlmarhum = $request->query('status_almarhum', 'semua');

        $query = Umat::aktif()->with(['keluarga.kub.wilayah']);

        if ($kubId) {
            $query->whereHas('keluarga', function ($q) use ($kubId) {
                $q->where('kub_id', $kubId);
            });
        }

        if (in_array(strtoupper($jenisKelamin), ['L', 'P'])) {
            $query->where('jenis_kelamin', strtoupper($jenisKelamin));
        }

        if ($statusAlmarhum !== 'semua') {
            $query->where('status_almarhum', $statusAlmarhum === '1');
        } else {
            $query->where('status_almarhum', false); // default hanya yang hidup
        }

        $umatList = $query->orderBy('nama', 'asc')->get();

        // Hitung Statistik Ringkas
        $totalUmat = $umatList->count();
        $totalLaki = $umatList->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = $umatList->where('jenis_kelamin', 'P')->count();
        
        // Cari Jumlah KK dari umat yang terdaftar
        $keluargaIds = $umatList->pluck('keluarga_id')->filter()->unique();
        $totalKeluarga = $keluargaIds->count();

        $stats = [
            'total_umat' => $totalUmat,
            'total_laki' => $totalLaki,
            'total_perempuan' => $totalPerempuan,
            'total_keluarga' => $totalKeluarga,
        ];

        $selectedKub = $kubId ? Kub::find($kubId) : null;
        $filters = [
            'kub' => $selectedKub ? $selectedKub->nama : 'Semua KUB',
            'jenis_kelamin' => $jenisKelamin === 'semua' ? 'Laki-laki & Perempuan' : ($jenisKelamin === 'L' ? 'Laki-laki' : 'Perempuan'),
            'status_almarhum' => $statusAlmarhum === 'semua' ? 'Hidup' : ($statusAlmarhum === '1' ? 'Almarhum' : 'Hidup'),
        ];

        $pdf = Pdf::loadView('pastor.laporan.pdf.umat', compact('umatList', 'stats', 'filters'))
            ->setPaper('a4', 'portrait');

        $filename = 'Laporan_Umat_' . ($selectedKub ? str_replace(' ', '_', strtolower($selectedKub->nama)) : 'semua') . '_' . date('Ymd_His') . '.pdf';

        if ($request->query('action') === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Ekspor PDF Laporan Mutasi & Dinamika Umat
     */
    public function mutasiPdf(Request $request)
    {
        $jenis = $request->query('jenis', 'semua');
        $status = $request->query('status', 'semua');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalSelesai = $request->query('tanggal_selesai');

        $query = Mutasi::with([
            'mutasiUmat.umat',
            'mutasiUmat.kubAsal',
            'mutasiUmat.kubTujuan',
            'mutasiKeluarga.keluarga.kepalaKeluarga',
            'mutasiKeluarga.kubAsal',
            'mutasiKeluarga.kubTujuan',
            'mutasiAgama.umat',
            'pemohon',
            'diprosesOleh'
        ]);

        if (in_array(strtolower($jenis), ['umat', 'keluarga', 'agama'])) {
            $query->where('jenis', strtolower($jenis));
        }

        if (in_array(strtolower($status), ['pending', 'disetujui', 'ditolak'])) {
            $query->where('status', strtolower($status));
        }

        if ($tanggalMulai) {
            $query->whereDate('tanggal', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $query->whereDate('tanggal', '<=', $tanggalSelesai);
        }

        $mutasiList = $query->orderBy('tanggal', 'desc')->get();

        $filters = [
            'jenis' => $jenis,
            'status' => $status,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
        ];

        $pdf = Pdf::loadView('pastor.laporan.pdf.mutasi', compact('mutasiList', 'filters'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_Mutasi_' . $jenis . '_' . date('Ymd_His') . '.pdf';

        if ($request->query('action') === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
