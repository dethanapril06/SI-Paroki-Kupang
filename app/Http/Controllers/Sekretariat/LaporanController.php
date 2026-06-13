<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Kub;
use App\Models\Mutasi;
use App\Models\Sakramen;
use App\Models\Umat;
use App\Models\Keluarga;
use App\Models\KeanggotaanDpp;
use App\Models\Kategorial;
use App\Models\Kematian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    private function logoPdfSrc(?string $path): ?string
    {
        $path = $path ? trim($path, " \t\n\r\0\x0B\"'") : null;

        if (! $path || ! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/' . $extension,
        };

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    private function pdfLogoData(): array
    {
        return [
            'logoKiri' => $this->logoPdfSrc(config('dompdf.logo_kiri_path')),
            'logoKanan' => $this->logoPdfSrc(config('dompdf.logo_kanan_path')),
        ];
    }

    /**
     * Tampilan Dashboard Utama Laporan Sekretariat
     */
    public function index(): View
    {
        $kubs = Kub::with('wilayah')->orderBy('nama')->get();
        $kategorials = Kategorial::orderBy('nama')->get();
        return view('sekretariat.laporan.index', compact('kubs', 'kategorials'));
    }

    /**
     * Ekspor PDF Laporan Administrasi Sakramen (Report 1)
     */
    public function sakramenPdf(Request $request)
    {
        $pdfLogos = $this->pdfLogoData();
        $subReport = $request->query('sub_report', 'rekap');
        $jenis = $request->query('jenis', 'semua');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalSelesai = $request->query('tanggal_selesai');
        $kubId = $request->query('kub_id');

        $selectedKub = $kubId ? Kub::find($kubId) : null;

        if ($subReport === 'belum_sakramen') {
            // Rancang query "Statistik Sakramen per KUB" (umat belum menerima inisiasi)
            $kubsData = Kub::with('wilayah')->get()->map(function ($kub) {
                // Query seluruh umat aktif hidup di KUB ini
                $umatQuery = Umat::where('status_almarhum', false)
                    ->where('status_keaktifan', 'aktif')
                    ->whereHas('keluarga', function($q) use ($kub) {
                        $q->where('kub_id', $kub->id);
                    });

                // Hitung total umat
                $totalUmat = (clone $umatQuery)->count();

                // Hitung umat belum baptis
                $belumBaptis = (clone $umatQuery)->whereDoesntHave('sakramen', function($q) {
                    $q->where('jenis_sakramen', 'BAPTIS');
                })->count();

                // Hitung umat belum komuni pertama
                $belumKomuni = (clone $umatQuery)->whereDoesntHave('sakramen', function($q) {
                    $q->where('jenis_sakramen', 'KOMUNI_PERTAMA');
                })->count();

                // Hitung umat belum krisma
                $belumKrisma = (clone $umatQuery)->whereDoesntHave('sakramen', function($q) {
                    $q->where('jenis_sakramen', 'KRISMA');
                })->count();

                $kub->total_umat = $totalUmat;
                $kub->belum_baptis = $belumBaptis;
                $kub->belum_komuni = $belumKomuni;
                $kub->belum_krisma = $belumKrisma;

                return $kub;
            })->sortByDesc('belum_baptis');

            $filters = [
                'sub_report' => 'belum_sakramen',
                'judul' => 'Laporan Statistik Umat Belum Menerima Sakramen Inisiasi per KUB',
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.sakramen', array_merge(compact('kubsData', 'filters'), $pdfLogos))
                ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Belum_Sakramen_per_KUB_' . date('Ymd_His') . '.pdf';
        } else {
            // Laporan Rekap atau Buku Register Sakramen Tahunan
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

            if ($subReport === 'register') {
                // Buku Register Sakramen Tahunan (default 1 tahun terakhir jika filter kosong)
                $startDate = $tanggalMulai ?: Carbon::now()->subYear()->toDateString();
                $endDate = $tanggalSelesai ?: Carbon::now()->toDateString();
                $query->whereBetween('tanggal_penerimaan', [$startDate, $endDate]);
                $filters = [
                    'sub_report' => 'register',
                    'judul' => 'Buku Register Sakramen Tahunan',
                    'jenis' => $jenis,
                    'tanggal_mulai' => $startDate,
                    'tanggal_selesai' => $endDate,
                    'kub' => $selectedKub ? $selectedKub->nama : 'Semua KUB',
                ];
            } else {
                // Rekapitulasi Sakramen standar
                if ($tanggalMulai) {
                    $query->whereDate('tanggal_penerimaan', '>=', $tanggalMulai);
                }
                if ($tanggalSelesai) {
                    $query->whereDate('tanggal_penerimaan', '<=', $tanggalSelesai);
                }
                $filters = [
                    'sub_report' => 'rekap',
                    'judul' => 'Laporan Rekapitulasi Penerimaan Sakramen',
                    'jenis' => $jenis,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'kub' => $selectedKub ? $selectedKub->nama : 'Semua KUB',
                ];
            }

            if ($kubId) {
                $query->whereHas('umat.keluarga', function ($q) use ($kubId) {
                    $q->where('kub_id', $kubId);
                });
            }

            $sakramenList = $query->orderBy('tanggal_penerimaan', 'desc')->get();

            // Hitung ringkasan jika Rekap
            $stats = [
                'total' => $sakramenList->count(),
                'baptis' => $sakramenList->where('jenis_sakramen', 'BAPTIS')->count(),
                'komuni' => $sakramenList->where('jenis_sakramen', 'KOMUNI_PERTAMA')->count(),
                'krisma' => $sakramenList->where('jenis_sakramen', 'KRISMA')->count(),
                'pernikahan' => $sakramenList->where('jenis_sakramen', 'PERNIKAHAN')->count(),
                'minyak_suci' => $sakramenList->where('jenis_sakramen', 'MINYAK_SUCI')->count(),
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.sakramen', array_merge(compact('sakramenList', 'stats', 'filters'), $pdfLogos))
                ->setPaper('a4', 'landscape');

            $filename = 'Laporan_Sakramen_' . $subReport . '_' . ($jenis !== 'semua' ? strtolower($jenis) : 'semua') . '_' . date('Ymd_His') . '.pdf';
        }

        if ($request->query('action') === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Ekspor PDF Laporan Demografi & Statistik Umat (Report 2)
     */
    public function umatPdf(Request $request)
    {
        $pdfLogos = $this->pdfLogoData();
        $subReport = $request->query('sub_report', 'rekap_kub');
        $kubId = $request->query('kub_id');
        $selectedKub = $kubId ? Kub::find($kubId) : null;

        if ($subReport === 'rekap_kub') {
            // Rekapitulasi Jumlah Umat & KK per KUB
            $query = Kub::with('wilayah');
            if ($kubId) {
                $query->where('id', $kubId);
            }
            
            $kubsData = $query->get()->map(function ($kub) {
                $keluargas = Keluarga::where('kub_id', $kub->id)->get();
                $totalKK = $keluargas->count();

                $umatQuery = Umat::where('status_almarhum', false)
                    ->where('status_keaktifan', 'aktif')
                    ->whereIn('keluarga_id', $keluargas->pluck('id'));

                $laki = (clone $umatQuery)->where('jenis_kelamin', 'Laki-laki')->count();
                $perempuan = (clone $umatQuery)->where('jenis_kelamin', 'Perempuan')->count();

                $kub->total_kk = $totalKK;
                $kub->total_laki = $laki;
                $kub->total_perempuan = $perempuan;
                $kub->total_umat = $laki + $perempuan;

                return $kub;
            });

            // Summary total paroki
            $statsSummary = [
                'total_kk' => $kubsData->sum('total_kk'),
                'total_laki' => $kubsData->sum('total_laki'),
                'total_perempuan' => $kubsData->sum('total_perempuan'),
                'total_umat' => $kubsData->sum('total_umat'),
            ];

            $filters = [
                'sub_report' => 'rekap_kub',
                'judul' => 'Laporan Rekapitulasi Umat & KK per KUB',
                'kub' => $selectedKub ? $selectedKub->nama : 'Semua KUB',
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.umat', array_merge(compact('kubsData', 'statsSummary', 'filters'), $pdfLogos))
                ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Rekap_KUB_Demografi_' . date('Ymd_His') . '.pdf';

        } elseif ($subReport === 'kelompok_usia') {
            // Statistik Umat Berdasarkan Kelompok Usia
            $query = Umat::aktif()->with('keluarga.kub.wilayah')
                ->where('status_almarhum', false)
                ->where('status_keaktifan', 'aktif');

            if ($kubId) {
                $query->whereHas('keluarga', function ($q) use ($kubId) {
                    $q->where('kub_id', $kubId);
                });
            }

            $umatList = $query->get()->map(function ($u) {
                $age = $u->tanggal_lahir ? $u->tanggal_lahir->age : 0;
                
                // Kelompok Usia
                if ($age < 12) {
                    $kelompok = 'Sekami / BIA (Anak)';
                    $code = 'BIA';
                } elseif ($age >= 12 && $age < 17) {
                    $kelompok = 'BIR (Remaja)';
                    $code = 'BIR';
                } elseif ($age >= 17 && $age <= 35 && (!in_array(strtolower($u->status_pernikahan), ['kawin', 'menikah']))) {
                    $kelompok = 'OMK (Orang Muda)';
                    $code = 'OMK';
                } elseif ($age >= 60) {
                    $kelompok = 'Lansia';
                    $code = 'LANSIA';
                } else {
                    $kelompok = 'Dewasa (Bapak/Ibu)';
                    $code = 'DEWASA';
                }

                $u->age = $age;
                $u->kelompok_usia = $kelompok;
                $u->kelompok_code = $code;

                return $u;
            });

            // Hitung statistik per kategori
            $statsSummary = [
                'BIA' => $umatList->where('kelompok_code', 'BIA')->count(),
                'BIR' => $umatList->where('kelompok_code', 'BIR')->count(),
                'OMK' => $umatList->where('kelompok_code', 'OMK')->count(),
                'DEWASA' => $umatList->where('kelompok_code', 'DEWASA')->count(),
                'LANSIA' => $umatList->where('kelompok_code', 'LANSIA')->count(),
                'total' => $umatList->count(),
            ];

            // Kelompokkan umat berdasarkan kategori
            $umatGrouped = $umatList->groupBy('kelompok_usia');

            $filters = [
                'sub_report' => 'kelompok_usia',
                'judul' => 'Laporan Statistik Demografi Umat Berdasarkan Kelompok Usia',
                'kub' => $selectedKub ? $selectedKub->nama : 'Semua KUB',
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.umat', array_merge(compact('umatGrouped', 'statsSummary', 'filters'), $pdfLogos))
                ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Kelompok_Usia_Umat_' . date('Ymd_His') . '.pdf';

        } else {
            // Lansia & Sakit (Prioritas Kunjungan)
            $query = Umat::aktif()->with('keluarga.kub.wilayah')
                ->where('status_almarhum', false)
                ->where('status_keaktifan', 'aktif');

            if ($kubId) {
                $query->whereHas('keluarga', function ($q) use ($kubId) {
                    $q->where('kub_id', $kubId);
                });
            }

            $umatList = $query->get()->filter(function ($u) {
                $age = $u->tanggal_lahir ? $u->tanggal_lahir->age : 0;
                $isLansia = $age >= 60;
                $isSakitAtauDisabilitas = $u->penyandang_disabilitas || 
                    stripos($u->keterangan_lain, 'sakit') !== false || 
                    stripos($u->keterangan_lain, 'stroke') !== false || 
                    stripos($u->keterangan_lain, 'lumpuh') !== false ||
                    stripos($u->keterangan_lain, 'uzur') !== false;

                $u->age = $age;
                $u->kondisi = $isSakitAtauDisabilitas ? 'Sakit / Disabilitas' : 'Lansia Sehat';

                return $isLansia || $isSakitAtauDisabilitas;
            })->sortBy(function($u) {
                return $u->keluarga->kub->nama ?? '';
            });

            $statsSummary = [
                'total' => $umatList->count(),
                'lansia' => $umatList->filter(fn($u) => $u->age >= 60)->count(),
                'disabilitas' => $umatList->filter(fn($u) => $u->penyandang_disabilitas || stripos($u->keterangan_lain, 'sakit') !== false)->count(),
            ];

            $filters = [
                'sub_report' => 'lansia_sakit',
                'judul' => 'Daftar Umat Lansia & Sakit (Prioritas Kunjungan Pastoral)',
                'kub' => $selectedKub ? $selectedKub->nama : 'Semua KUB',
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.umat', array_merge(compact('umatList', 'statsSummary', 'filters'), $pdfLogos))
                ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Umat_Lansia_Sakit_' . date('Ymd_His') . '.pdf';
        }

        if ($request->query('action') === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Ekspor PDF Laporan Mutasi & Dinamika Umat (Report 3)
     */
    public function mutasiPdf(Request $request)
    {
        $pdfLogos = $this->pdfLogoData();
        $subReport = $request->query('sub_report', 'dinamika');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalSelesai = $request->query('tanggal_selesai');

        if ($subReport === 'dinamika') {
            // Laporan Pertumbuhan Umat (Dinamika)
            // Hitung Kelahiran: Umat baru yang ditambahkan dengan tanggal_lahir di periode tersebut
            $lahirQuery = Umat::aktif()->where('status_almarhum', false);
            if ($tanggalMulai) {
                $lahirQuery->whereDate('tanggal_lahir', '>=', $tanggalMulai);
            }
            if ($tanggalSelesai) {
                $lahirQuery->whereDate('tanggal_lahir', '<=', $tanggalSelesai);
            }
            $kelahiranList = $lahirQuery->with('keluarga.kub')->orderBy('tanggal_lahir', 'desc')->get();

            // Hitung Kematian: Data kematian di periode tersebut
            $matiQuery = Kematian::with('umat.keluarga.kub');
            if ($tanggalMulai) {
                $matiQuery->whereDate('tanggal_meninggal', '>=', $tanggalMulai);
            }
            if ($tanggalSelesai) {
                $matiQuery->whereDate('tanggal_meninggal', '<=', $tanggalSelesai);
            }
            $kematianList = $matiQuery->orderBy('tanggal_meninggal', 'desc')->get();

            // Hitung Mutasi Masuk & Keluar yang disetujui
            $mutasiQuery = Mutasi::where('status', 'disetujui')
                ->with([
                    'mutasiUmat.umat', 
                    'mutasiUmat.kubAsal', 
                    'mutasiUmat.kubTujuan',
                    'mutasiKeluarga.keluarga.kepalaKeluarga',
                    'mutasiKeluarga.kubAsal',
                    'mutasiKeluarga.kubTujuan'
                ]);

            if ($tanggalMulai) {
                $mutasiQuery->whereDate('tanggal', '>=', $tanggalMulai);
            }
            if ($tanggalSelesai) {
                $mutasiQuery->whereDate('tanggal', '<=', $tanggalSelesai);
            }
            
            $mutasiList = $mutasiQuery->get();

            // Klasifikasikan mutasi masuk/keluar
            $mutasiMasuk = [];
            $mutasiKeluar = [];

            foreach ($mutasiList as $m) {
                if ($m->jenis === 'umat' && $m->mutasiUmat) {
                    $sub = $m->mutasiUmat->sub_jenis;
                    if ($sub === 'paroki' || $sub === 'keuskupan') {
                        // Jika ada asal_paroki/keuskupan asal, berarti masuk atau keluar
                        if ($m->mutasiUmat->paroki_asal_id || $m->mutasiUmat->keuskupan_asal_id) {
                            $mutasiMasuk[] = $m;
                        } else {
                            $mutasiKeluar[] = $m;
                        }
                    }
                } elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga) {
                    $sub = $m->mutasiKeluarga->sub_jenis;
                    if ($sub === 'paroki' || $sub === 'keuskupan') {
                        if ($m->mutasiKeluarga->paroki_asal_id || $m->mutasiKeluarga->keuskupan_asal_id) {
                            $mutasiMasuk[] = $m;
                        } else {
                            $mutasiKeluar[] = $m;
                        }
                    }
                }
            }

            $stats = [
                'lahir' => $kelahiranList->count(),
                'wafat' => $kematianList->count(),
                'masuk' => count($mutasiMasuk),
                'keluar' => count($mutasiKeluar),
            ];

            $filters = [
                'sub_report' => 'dinamika',
                'judul' => 'Laporan Pertumbuhan & Dinamika Umat Paroki',
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.mutasi', array_merge(compact('kelahiranList', 'kematianList', 'mutasiMasuk', 'mutasiKeluar', 'stats', 'filters'), $pdfLogos))
                ->setPaper('a4', 'landscape');

            $filename = 'Laporan_Dinamika_Pertumbuhan_Umat_' . date('Ymd_His') . '.pdf';

        } else {
            // Daftar Log Mutasi Disetujui
            $query = Mutasi::where('status', 'disetujui')
                ->with([
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

            if ($tanggalMulai) {
                $query->whereDate('tanggal', '>=', $tanggalMulai);
            }
            if ($tanggalSelesai) {
                $query->whereDate('tanggal', '<=', $tanggalSelesai);
            }

            $mutasiList = $query->orderBy('tanggal', 'desc')->get();

            $filters = [
                'sub_report' => 'log_disetujui',
                'judul' => 'Daftar Log Mutasi Kepindahan Resmi (Disetujui)',
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.mutasi', array_merge(compact('mutasiList', 'filters'), $pdfLogos))
                ->setPaper('a4', 'landscape');

            $filename = 'Laporan_Log_Mutasi_Disetujui_' . date('Ymd_His') . '.pdf';
        }

        if ($request->query('action') === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Ekspor PDF Laporan Struktur Organisasi & Kelompok (Report 4)
     */
    public function organisasiPdf(Request $request)
    {
        $pdfLogos = $this->pdfLogoData();
        $subReport = $request->query('sub_report', 'dpp');
        $kategorialId = $request->query('kategorial_id');

        $selectedKategorial = $kategorialId ? Kategorial::find($kategorialId) : null;

        if ($subReport === 'dpp') {
            // Profil DPP (Dewan Pastoral Paroki)
            $dppList = KeanggotaanDpp::with('umat.keluarga.kub')
                ->where('status_aktif', 'Aktif')
                ->orderByRaw("FIELD(jabatan, 'Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Koordinator Bidang', 'Anggota', 'Lainnya')")
                ->get();

            $filters = [
                'sub_report' => 'dpp',
                'judul' => 'Profil Pengurus Dewan Pastoral Paroki (DPP) Aktif',
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.organisasi', array_merge(compact('dppList', 'filters'), $pdfLogos))
                ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Profil_DPP_Aktif_' . date('Ymd_His') . '.pdf';

        } elseif ($subReport === 'rekap_kategorial') {
            // Rekapitulasi Kelompok Kategorial
            $kategorialList = Kategorial::with(['ketuaUmat', 'klerus'])
                ->withCount([
                    'anggota as anggota_aktif_count' => fn($q) => $q->where('status', 'Aktif')
                ])->orderBy('nama')->get();

            $filters = [
                'sub_report' => 'rekap_kategorial',
                'judul' => 'Laporan Rekapitulasi Anggota Kelompok Kategorial Paroki',
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.organisasi', array_merge(compact('kategorialList', 'filters'), $pdfLogos))
                ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Rekapitulasi_Kategorial_' . date('Ymd_His') . '.pdf';

        } else {
            // Detail Anggota Kelompok Kategorial tertentu
            if (!$selectedKategorial) {
                return back()->with('error', 'Kelompok Kategorial harus dipilih untuk melihat daftar anggota detail.');
            }

            $selectedKategorial->load('klerus');

            // Ambil anggota kategorial dari relation BelongsToMany beserta pivot
            $anggotaList = $selectedKategorial->anggota()
                ->with('keluarga.kub')
                ->wherePivot('status', 'Aktif')
                ->orderByPivot('jabatan')
                ->get();

            $filters = [
                'sub_report' => 'detail_kategorial',
                'judul' => 'Daftar Roster Pengurus & Anggota Kelompok: ' . $selectedKategorial->nama,
                'kategorial_nama' => $selectedKategorial->nama,
                'ketua' => $selectedKategorial->ketuaUmat ? $selectedKategorial->ketuaUmat->nama : 'Belum Ditentukan',
                'pastor_moderator' => $selectedKategorial->klerus ? $selectedKategorial->klerus->nama : 'Belum Ditentukan',
            ];

            $pdf = Pdf::loadView('sekretariat.laporan.pdf.organisasi', array_merge(compact('anggotaList', 'filters'), $pdfLogos))
                ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Anggota_Kategorial_' . str_replace(' ', '_', strtolower($selectedKategorial->nama)) . '_' . date('Ymd_His') . '.pdf';
        }

        if ($request->query('action') === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
