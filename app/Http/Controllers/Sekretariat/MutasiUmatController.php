<?php

namespace App\Http\Controllers\Sekretariat;
use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Keuskupan;
use App\Models\Mutasi;
use App\Models\MutasiUmat;
use App\Models\Paroki;
use App\Models\Umat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiUmatController extends Controller
{
    public function index(): View
    {
        $mutasiUmatList = MutasiUmat::with([
                'mutasi',
                'umat' => fn($q) => $q->withTrashed(),
                'keluargaAsal.kepalaKeluarga',
                'keluargaTujuan.kepalaKeluarga',
                'parokiAsal', 'parokiTujuan',
                'keuskupanAsal', 'keuskupanTujuan',
            ])
            ->join('mutasi', 'mutasi.id', '=', 'mutasi_umat.mutasi_id')
            ->orderByDesc('mutasi.tanggal')
            ->select('mutasi_umat.*')
            ->paginate(20);

        return view('sekretariat.mutasi.umat.index', compact('mutasiUmatList'));
    }

    /**
     * Resolve paroki & keuskupan dari wilayah (semua jalur hierarki)
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

    /**
     * Eager load umat dengan semua kemungkinan jalur hierarki
     */
    private function getUmatList(): \Illuminate\Support\Collection
    {
        return Umat::aktif()->with([
                'keluarga.kub.wilayah.paroki.keuskupan',
                'keluarga.kub.wilayah.stasi.paroki.keuskupan',
                'keluarga.kub.wilayah.stasi.kuasi.keuskupan',
                'keluarga.kub.wilayah.kuasi.keuskupan',
            ])
            ->orderBy('nama')
            ->get();
    }

    /**
     * Eager load keluarga dengan semua kemungkinan jalur hierarki
     */
    private function getKeluargaList(): \Illuminate\Support\Collection
    {
        return Keluarga::with([
                'kepalaKeluarga' => fn($q) => $q->aktif(),
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
     * Build umatHierarchy dengan resolve hierarki lengkap
     */
    private function buildUmatHierarchy($umatList): \Illuminate\Support\Collection
    {
        return $umatList->mapWithKeys(function ($u) {
            $wilayah = $u->keluarga?->kub?->wilayah;
            [$paroki, $keuskupan] = $this->resolveHierarchyFromWilayah($wilayah);

            return [
                $u->id => [
                    'keluarga_id'    => $u->keluarga_id,
                    'kub_id'         => $u->keluarga?->kub_id,
                    'kub_nama'       => $u->keluarga?->kub?->nama,
                    'wilayah_id'     => $u->keluarga?->kub?->wilayah_id,
                    'wilayah_nama'   => $wilayah?->nama,
                    'paroki_id'      => $paroki?->id,
                    'paroki_nama'    => $paroki?->nama,
                    'keuskupan_id'   => $keuskupan?->id,
                    'keuskupan_nama' => $keuskupan?->nama,
                ],
            ];
        });
    }

    public function create(): View
    {
        $umatList      = $this->getUmatList();
        $keluargaList  = $this->getKeluargaList();
        $parokiList    = Paroki::orderBy('nama')->get();
        $keuskupanList = Keuskupan::orderBy('nama')->get();

        $umatHierarchy = $this->buildUmatHierarchy($umatList);

        return view('sekretariat.mutasi.umat.create',
            compact('umatList', 'keluargaList', 'parokiList', 'keuskupanList', 'umatHierarchy'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'umat_id'              => ['required', 'exists:umat,id'],
            'sub_jenis'            => ['required', 'in:pindah_keluarga_ada,pindah_keluarga_baru,paroki,keuskupan'],
            'tanggal'              => ['required', 'date'],
            'nomor_surat'          => ['nullable', 'string', 'max:100'],
            'keterangan'           => ['nullable', 'string'],

            // Pindah keluarga yang sudah ada
            'keluarga_tujuan_id'   => ['nullable', 'exists:keluarga,id'],

            // Pindah keluarga baru
            'alamat_baru'          => ['required', 'string'],
            'status_tempat_tinggal_baru' => ['required', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
            'jadikan_kepala'       => ['nullable', 'boolean'],
            'hubungan_keluarga_baru' => ['nullable', 'in:Suami,Istri,Ayah,Ibu'],

            // Pindah paroki/keuskupan
            'paroki_tujuan_id'     => ['nullable', 'exists:paroki,id'],
            'keuskupan_tujuan_id'  => ['nullable', 'exists:keuskupan,id'],
        ]);

        $umat = Umat::aktif()->with([
            'keluarga.kub.wilayah.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.kuasi.keuskupan',
            'keluarga.kub.wilayah.kuasi.keuskupan',
        ])->findOrFail($validated['umat_id']);

        $keluargaAsal = $umat->keluarga;
        $kub          = $keluargaAsal?->kub;
        $wilayah      = $kub?->wilayah;

        [$paroki, $keuskupan] = $this->resolveHierarchyFromWilayah($wilayah);

        // Sekretariat membuat mutasi langsung → langsung disetujui + eksekusi
        // pemohon_umat_id diisi dengan umat yang bersangkutan (bukan akun sekretariat)
        $mutasi = Mutasi::create([
            'jenis'                 => 'umat',
            'tanggal'               => $validated['tanggal'],
            'keterangan'            => $validated['keterangan'] ?? null,
            'status'                => 'disetujui',
            'pemohon_umat_id'       => $validated['umat_id'],
            'diproses_oleh_user_id' => auth()->id(),
            'diproses_pada'         => now(),
        ]);

        $mutasiUmat = MutasiUmat::create([
            'mutasi_id'           => $mutasi->id,
            'umat_id'             => $validated['umat_id'],
            'sub_jenis'           => $validated['sub_jenis'],
            'nomor_surat'         => $validated['nomor_surat'] ?? null,
            'keluarga_asal_id'    => $keluargaAsal?->id,
            'keluarga_tujuan_id'  => null, // diisi di executeApproval
            'kub_asal_id'         => $kub?->id,
            'wilayah_asal_id'     => $wilayah?->id,
            'paroki_asal_id'      => $paroki?->id,
            'paroki_tujuan_id'    => $validated['paroki_tujuan_id'] ?? null,
            'keuskupan_asal_id'   => $keuskupan?->id,
            'keuskupan_tujuan_id' => $validated['keuskupan_tujuan_id'] ?? null,
        ]);

        // Langsung eksekusi karena dibuat oleh sekretariat
        $this->executeApproval($mutasiUmat, $validated);

        return redirect()->route('sekretariat.mutasi.umat.index')
            ->with('success', 'Mutasi umat berhasil dicatat.');
    }

    /**
     * Eksekusi perubahan data umat setelah disetujui.
     * Dipanggil dari store() (sekretariat langsung) dan approve() di MutasiController.
     */
    public function executeApproval(MutasiUmat $mutasiUmat, array $data = []): void
    {
        $mutasiUmat->load(['mutasi', 'umat', 'keluargaAsal']);

        $umat         = Umat::withTrashed()->findOrFail($mutasiUmat->umat_id);
        $subJenis     = $mutasiUmat->sub_jenis;
        $keluargaAsal = $mutasiUmat->keluargaAsal;

        if ($subJenis === 'pindah_keluarga_ada') {
            $keluargaTujuanId = $data['keluarga_tujuan_id'] ?? $mutasiUmat->keluarga_tujuan_id;
            $umat->update(['keluarga_id' => $keluargaTujuanId]);
            $mutasiUmat->update(['keluarga_tujuan_id' => $keluargaTujuanId]);

        } elseif ($subJenis === 'pindah_keluarga_baru') {
            $keluargaBaru = Keluarga::create([
                'kub_id'                => $keluargaAsal?->kub_id,
                'kepala_keluarga_id'    => null,
                'alamat'                => $data['alamat_baru'] ?? $mutasiUmat->alamat_baru,
                'status_tempat_tinggal' => $data['status_tempat_tinggal_baru'] ?? $mutasiUmat->status_tempat_tinggal_baru,
            ]);

            $umat->update(['keluarga_id' => $keluargaBaru->id]);
            $mutasiUmat->update(['keluarga_tujuan_id' => $keluargaBaru->id]);

            if (!empty($data['jadikan_kepala']) || $subJenis === 'pindah_keluarga_baru') {
                $keluargaBaru->update(['kepala_keluarga_id' => $umat->id]);
                $umat->update(['hubungan_keluarga' => $data['hubungan_keluarga_baru'] ?? 'Suami']);
            }

        } else {
            // Pindah paroki/keuskupan — hapus akun login lalu soft delete umat
            $umat->update(['status_keaktifan' => 'non-aktif']);

            // Hapus akun login yang terhubung dengan umat ini
            if ($umat->user) {
                $umat->user->delete();
            }

            $umat->delete();
        }
    }

    public function show(MutasiUmat $mutasiUmat): View
    {
        $mutasiUmat->load([
            'mutasi.pemohon',
            'mutasi.diprosesOleh',
            'umat' => fn($q) => $q->withTrashed(),
            'keluargaAsal.kepalaKeluarga',
            'keluargaTujuan.kepalaKeluarga',
            'parokiAsal', 'parokiTujuan',
            'keuskupanAsal', 'keuskupanTujuan',
        ]);

        return view('sekretariat.mutasi.umat.show', compact('mutasiUmat'));
    }

    public function edit(MutasiUmat $mutasiUmat): View|RedirectResponse
    {
        // Hanya pindah_keluarga_ada yang bisa diedit
        if (!in_array($mutasiUmat->sub_jenis, ['pindah_keluarga_ada'])) {
            return redirect()->route('sekretariat.mutasi.umat.show', $mutasiUmat)
                ->with('error', 'Jenis mutasi ini tidak dapat diedit.');
        }

        // Cegah edit jika umat sudah soft deleted
        $umat = Umat::withTrashed()->find($mutasiUmat->umat_id);
        if ($umat?->trashed()) {
            return redirect()->route('sekretariat.mutasi.umat.show', $mutasiUmat)
                ->with('error', 'Umat ini sudah tidak aktif dan tidak dapat diedit.');
        }

        $mutasiUmat->load([
            'mutasi',
            'umat' => fn($q) => $q->withTrashed(),
            'keluargaAsal', 'keluargaTujuan',
        ]);

        $umatList      = $this->getUmatList();
        $keluargaList  = $this->getKeluargaList();
        $umatHierarchy = $this->buildUmatHierarchy($umatList);

        return view('sekretariat.mutasi.umat.edit',
            compact('mutasiUmat', 'umatList', 'keluargaList', 'umatHierarchy'));
    }

    public function update(Request $request, MutasiUmat $mutasiUmat): RedirectResponse
    {
        $validated = $request->validate([
            'umat_id'            => ['required', 'exists:umat,id'],
            'tanggal'            => ['required', 'date'],
            'nomor_surat'        => ['nullable', 'string', 'max:100'],
            'keluarga_tujuan_id' => ['required', 'exists:keluarga,id'],
            'keterangan'         => ['nullable', 'string'],
        ]);

        $mutasiUmat->mutasi->update([
            'tanggal'    => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Asal TIDAK diupdate
        $mutasiUmat->update([
            'umat_id'            => $validated['umat_id'],
            'nomor_surat'        => $validated['nomor_surat'] ?? null,
            'keluarga_tujuan_id' => $validated['keluarga_tujuan_id'],
        ]);

        $umat = Umat::findOrFail($validated['umat_id']);
        $umat->update(['keluarga_id' => $validated['keluarga_tujuan_id']]);

        return redirect()->route('sekretariat.mutasi.umat.index')
            ->with('success', 'Mutasi umat berhasil diperbarui.');
    }

    public function destroy(MutasiUmat $mutasiUmat): RedirectResponse
    {
        $umat = Umat::withTrashed()->find($mutasiUmat->umat_id);

        if ($umat?->trashed()) {
            // Restore akun login yang ikut dihapus saat executeApproval
            if ($umat->user()->withTrashed()->exists()) {
                $umat->user()->withTrashed()->first()->restore();
            }

            // Restore umat yang di-soft delete karena pindah paroki/keuskupan
            $umat->restore();
            $umat->update(['status_keaktifan' => 'aktif']);
        }

        // Kembalikan keluarga_id ke asal
        $umat?->update(['keluarga_id' => $mutasiUmat->keluarga_asal_id]);

        // Jika pindah_keluarga_baru, hapus keluarga baru yang dibuat
        if ($mutasiUmat->sub_jenis === 'pindah_keluarga_baru' && $mutasiUmat->keluarga_tujuan_id) {
            $keluargaBaru = Keluarga::find($mutasiUmat->keluarga_tujuan_id);
            if ($keluargaBaru && $keluargaBaru->umat()->count() === 0) {
                $keluargaBaru->delete();
            }
        }

        $mutasiUmat->mutasi->delete();

        return redirect()->route('sekretariat.mutasi.umat.index')
            ->with('success', 'Mutasi umat berhasil dihapus.');
    }
}
