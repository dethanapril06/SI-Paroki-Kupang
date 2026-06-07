<?php

namespace App\Http\Controllers\Sekretariat;
use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Keuskupan;
use App\Models\Kub;
use App\Models\Mutasi;
use App\Models\MutasiKeluarga;
use App\Models\Paroki;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiKeluargaController extends Controller
{
    public function index(): View
    {
        $mutasiKeluargaList = MutasiKeluarga::with([
                'mutasi',
                'keluarga' => fn($q) => $q->withTrashed()->with([
                    'kepalaKeluarga' => fn($q) => $q->withTrashed(),
                ]),
                'kubAsal', 'kubTujuan',
                'wilayahAsal', 'wilayahTujuan',
                'parokiAsal', 'parokiTujuan',
                'keuskupanAsal', 'keuskupanTujuan',
            ])
            ->join('mutasi', 'mutasi.id', '=', 'mutasi_keluarga.mutasi_id')
            ->orderByDesc('mutasi.tanggal')
            ->select('mutasi_keluarga.*')
            ->paginate(20);

        return view('sekretariat.mutasi.keluarga.index', compact('mutasiKeluargaList'));
    }

    /**
     * Resolve paroki dan keuskupan dari wilayah,
     * dengan mempertimbangkan hierarki: wilayah → paroki/stasi/kuasi → keuskupan
     */
    private function buildKeluargaHierarchy($keluargaList): \Illuminate\Support\Collection
    {
        return $keluargaList->mapWithKeys(function ($k) {
            $wilayah = $k->kub?->wilayah;

            if ($wilayah?->paroki_id) {
                $paroki    = $wilayah->paroki;
                $keuskupan = $paroki?->keuskupan;
            } elseif ($wilayah?->stasi_id) {
                $stasi     = $wilayah->stasi;
                $paroki    = $stasi?->paroki;
                $keuskupan = $paroki?->keuskupan ?? $stasi?->kuasi?->keuskupan;
            } elseif ($wilayah?->kuasi_id) {
                $paroki    = null;
                $keuskupan = $wilayah->kuasi?->keuskupan;
            } else {
                $paroki    = null;
                $keuskupan = null;
            }

            return [
                $k->id => [
                    'kub_id'         => $k->kub_id,
                    'kub_nama'       => $k->kub?->nama,
                    'wilayah_id'     => $k->kub?->wilayah_id,
                    'wilayah_nama'   => $wilayah?->nama,
                    'paroki_id'      => $paroki?->id,
                    'paroki_nama'    => $paroki?->nama,
                    'keuskupan_id'   => $keuskupan?->id,
                    'keuskupan_nama' => $keuskupan?->nama,
                ],
            ];
        });
    }

    /**
     * Bangun data cascade untuk JS (kub→wilayah)
     */
    private function buildCascadeData($kubList, $wilayahList, $parokiList = null): array
    {
        $kubByWilayah = $kubList
            ->groupBy('wilayah_id')
            ->map(fn($g) => $g->map(fn($k) => ['id' => $k->id, 'nama' => $k->nama])->values());

        return compact('kubByWilayah');
    }

    /**
     * Eager load keluarga dengan semua kemungkinan jalur hierarki
     */
    private function getKeluargaList(): \Illuminate\Support\Collection
    {
        return Keluarga::with([
                'kepalaKeluarga',
                'kub.wilayah.paroki.keuskupan',
                'kub.wilayah.stasi.paroki.keuskupan',
                'kub.wilayah.stasi.kuasi.keuskupan',
                'kub.wilayah.kuasi.keuskupan',
            ])
            ->leftJoin('umat', 'keluarga.kepala_keluarga_id', '=', 'umat.id')
            ->orderBy('umat.nama')
            ->select('keluarga.*')
            ->get();
    }

    /**
     * Resolve paroki & keuskupan dari wilayah untuk digunakan di store()
     */
    private function resolveHierarchyFromWilayah($wilayah): array
    {
        if ($wilayah?->paroki_id) {
            $paroki    = $wilayah->paroki;
            $keuskupan = $paroki?->keuskupan;
        } elseif ($wilayah?->stasi_id) {
            $stasi     = $wilayah->stasi;
            $paroki    = $stasi?->paroki;
            $keuskupan = $paroki?->keuskupan ?? $stasi?->kuasi?->keuskupan;
        } elseif ($wilayah?->kuasi_id) {
            $paroki    = null;
            $keuskupan = $wilayah->kuasi?->keuskupan;
        } else {
            $paroki    = null;
            $keuskupan = null;
        }

        return [$paroki, $keuskupan];
    }

    public function create(): View
    {
        $keluargaList  = $this->getKeluargaList();
        $kubList       = Kub::orderBy('nama')->get();
        $wilayahList   = Wilayah::orderBy('nama')->get();
        $parokiList    = Paroki::orderBy('nama')->get();
        $keuskupanList = Keuskupan::orderBy('nama')->get();

        $keluargaHierarchy = $this->buildKeluargaHierarchy($keluargaList);
        $cascade           = $this->buildCascadeData($kubList, $wilayahList, $parokiList);

        return view('sekretariat.mutasi.keluarga.create',
            compact('keluargaList', 'kubList', 'wilayahList', 'parokiList', 'keuskupanList',
                    'keluargaHierarchy') + $cascade);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'keluarga_id'         => ['required', 'exists:keluarga,id'],
            'sub_jenis'           => ['required', 'in:keuskupan,paroki,wilayah,kub'],
            'tanggal'             => ['required', 'date'],
            'nomor_surat'         => ['nullable', 'string', 'max:100'],
            'kub_tujuan_id'       => ['nullable', 'exists:kub,id'],
            'wilayah_tujuan_id'   => ['nullable', 'exists:wilayah,id'],
            'paroki_tujuan_id'    => ['nullable', 'exists:paroki,id'],
            'keuskupan_tujuan_id' => ['nullable', 'exists:keuskupan,id'],
            'keterangan'          => ['nullable', 'string'],
        ]);

        $keluarga = Keluarga::with([
            'kub.wilayah.paroki.keuskupan',
            'kub.wilayah.stasi.paroki.keuskupan',
            'kub.wilayah.stasi.kuasi.keuskupan',
            'kub.wilayah.kuasi.keuskupan',
        ])->findOrFail($validated['keluarga_id']);

        $kub     = $keluarga->kub;
        $wilayah = $kub?->wilayah;

        [$paroki, $keuskupan] = $this->resolveHierarchyFromWilayah($wilayah);

        // Sekretariat membuat mutasi langsung → langsung disetujui + eksekusi
        // pemohon_umat_id diisi dengan kepala keluarga sebagai representasi keluarga
        $mutasi = Mutasi::create([
            'jenis'                 => 'keluarga',
            'tanggal'               => $validated['tanggal'],
            'keterangan'            => $validated['keterangan'] ?? null,
            'status'                => 'disetujui',
            'pemohon_umat_id'       => $keluarga->kepala_keluarga_id,
            'diproses_oleh_user_id' => auth()->id(),
            'diproses_pada'         => now(),
        ]);

        $mutasiKeluarga = MutasiKeluarga::create([
            'mutasi_id'           => $mutasi->id,
            'keluarga_id'         => $validated['keluarga_id'],
            'sub_jenis'           => $validated['sub_jenis'],
            'nomor_surat'         => $validated['nomor_surat'] ?? null,
            'kub_asal_id'         => $kub?->id,
            'wilayah_asal_id'     => $wilayah?->id,
            'paroki_asal_id'      => $paroki?->id,
            'keuskupan_asal_id'   => $keuskupan?->id,
            'kub_tujuan_id'       => $validated['kub_tujuan_id'],
            'wilayah_tujuan_id'   => $validated['wilayah_tujuan_id'] ?? null,
            'paroki_tujuan_id'    => $validated['paroki_tujuan_id'] ?? null,
            'keuskupan_tujuan_id' => $validated['keuskupan_tujuan_id'] ?? null,
        ]);

        // Langsung eksekusi karena dibuat oleh sekretariat
        $this->executeApproval($mutasiKeluarga);

        return redirect()->route('sekretariat.mutasi.keluarga.index')
            ->with('success', 'Mutasi keluarga berhasil dicatat.');
    }

    /**
     * Eksekusi perubahan data keluarga setelah disetujui.
     * Dipanggil dari store() (sekretariat langsung) dan approve() di MutasiController.
     */
    public function executeApproval(MutasiKeluarga $mutasiKeluarga): void
    {
        $mutasiKeluarga->load('keluarga');
        $keluarga = Keluarga::withTrashed()->findOrFail($mutasiKeluarga->keluarga_id);

        if (in_array($mutasiKeluarga->sub_jenis, ['kub', 'wilayah'])) {
            // Masih dalam paroki yang sama — cukup update KUB
            $keluarga->update(['kub_id' => $mutasiKeluarga->kub_tujuan_id]);
        } else {
            // Pindah paroki/keuskupan — soft delete umat beserta akun login, lalu soft delete keluarga
            $keluarga->umat()->get()->each(function ($umat) {
                // Hapus akun login yang terhubung dengan umat ini
                if ($umat->user) {
                    $umat->user->delete();
                }
                $umat->delete();
            });
            $keluarga->delete();
        }
    }

    public function show(MutasiKeluarga $mutasiKeluarga): View
    {
        $mutasiKeluarga->load([
            'mutasi.pemohon',
            'mutasi.diprosesOleh',
            'keluarga' => fn($q) => $q->withTrashed()->with([
                'kepalaKeluarga' => fn($q) => $q->withTrashed(),
            ]),
            'kubAsal', 'kubTujuan',
            'wilayahAsal', 'wilayahTujuan',
            'parokiAsal', 'parokiTujuan',
            'keuskupanAsal', 'keuskupanTujuan',
        ]);

        return view('sekretariat.mutasi.keluarga.show', compact('mutasiKeluarga'));
    }

    public function edit(MutasiKeluarga $mutasiKeluarga): View|RedirectResponse
    {
        // Cegah edit jika keluarga sudah soft deleted (pindah paroki/keuskupan)
        $keluarga = Keluarga::withTrashed()->find($mutasiKeluarga->keluarga_id);
        if ($keluarga?->trashed()) {
            return redirect()->route('sekretariat.mutasi.keluarga.show', $mutasiKeluarga)
                ->with('error', 'Keluarga ini sudah tidak aktif dan tidak dapat diedit.');
        }

        $mutasiKeluarga->load([
            'mutasi',
            'keluarga' => fn($q) => $q->withTrashed()->with([
                'kub.wilayah.paroki.keuskupan',
                'kub.wilayah.stasi.paroki.keuskupan',
                'kub.wilayah.stasi.kuasi.keuskupan',
                'kub.wilayah.kuasi.keuskupan',
            ]),
            'kubAsal', 'wilayahAsal', 'parokiAsal', 'keuskupanAsal',
        ]);

        $keluargaList      = $this->getKeluargaList();
        $kubList           = Kub::orderBy('nama')->get();
        $wilayahList       = Wilayah::orderBy('nama')->get();
        $parokiList        = Paroki::orderBy('nama')->get();
        $keuskupanList     = Keuskupan::orderBy('nama')->get();
        $keluargaHierarchy = $this->buildKeluargaHierarchy($keluargaList);
        $cascade           = $this->buildCascadeData($kubList, $wilayahList, $parokiList);

        return view('sekretariat.mutasi.keluarga.edit',
            compact('mutasiKeluarga', 'keluargaList', 'kubList', 'wilayahList', 'parokiList', 'keuskupanList',
                    'keluargaHierarchy') + $cascade);
    }

    public function update(Request $request, MutasiKeluarga $mutasiKeluarga): RedirectResponse
    {
        $validated = $request->validate([
            'keluarga_id'         => ['required', 'exists:keluarga,id'],
            'sub_jenis'           => ['required', 'in:keuskupan,paroki,wilayah,kub'],
            'tanggal'             => ['required', 'date'],
            'nomor_surat'         => ['nullable', 'string', 'max:100'],
            'kub_tujuan_id'       => ['nullable', 'exists:kub,id'],
            'wilayah_tujuan_id'   => ['nullable', 'exists:wilayah,id'],
            'paroki_tujuan_id'    => ['nullable', 'exists:paroki,id'],
            'keuskupan_tujuan_id' => ['nullable', 'exists:keuskupan,id'],
            'keterangan'          => ['nullable', 'string'],
        ]);

        $mutasiKeluarga->mutasi->update([
            'tanggal'    => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Asal TIDAK diupdate — biarkan seperti saat pertama dicatat
        $mutasiKeluarga->update([
            'keluarga_id'         => $validated['keluarga_id'],
            'sub_jenis'           => $validated['sub_jenis'],
            'nomor_surat'         => $validated['nomor_surat'] ?? null,
            'kub_tujuan_id'       => $validated['kub_tujuan_id'],
            'wilayah_tujuan_id'   => $validated['wilayah_tujuan_id'] ?? null,
            'paroki_tujuan_id'    => $validated['paroki_tujuan_id'] ?? null,
            'keuskupan_tujuan_id' => $validated['keuskupan_tujuan_id'] ?? null,
        ]);

        $keluarga = Keluarga::findOrFail($validated['keluarga_id']);
        if (in_array($validated['sub_jenis'], ['kub', 'wilayah'])) {
            $keluarga->update(['kub_id' => $validated['kub_tujuan_id']]);
        } else {
            $keluarga->update(['kub_id' => null]);
        }

        return redirect()->route('sekretariat.mutasi.keluarga.index')
            ->with('success', 'Mutasi keluarga berhasil diperbarui.');
    }

    public function destroy(MutasiKeluarga $mutasiKeluarga): RedirectResponse
    {
        $keluarga = Keluarga::withTrashed()->find($mutasiKeluarga->keluarga_id);

        if ($keluarga?->trashed()) {
            // Keluarga di-soft delete karena pindah paroki/keuskupan → restore semua anggota + akun login
            $keluarga->umat()->withTrashed()->get()->each(function ($umat) {
                // Restore akun login yang ikut dihapus saat executeApproval
                if ($umat->user()->withTrashed()->exists()) {
                    $umat->user()->withTrashed()->first()->restore();
                }
                $umat->restore();
            });
            $keluarga->restore();
        }

        // Kembalikan kub_id ke posisi asal
        $keluarga->update(['kub_id' => $mutasiKeluarga->kub_asal_id]);

        $mutasiKeluarga->mutasi->delete(); // cascade hapus mutasi_keluarga juga

        return redirect()->route('sekretariat.mutasi.keluarga.index')
            ->with('success', 'Mutasi keluarga berhasil dihapus.');
    }
}