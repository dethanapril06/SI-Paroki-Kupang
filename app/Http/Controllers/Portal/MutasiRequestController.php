<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Keuskupan;
use App\Models\Mutasi;
use App\Models\MutasiAgama;
use App\Models\MutasiKeluarga;
use App\Models\MutasiUmat;
use App\Models\Paroki;
use App\Models\Umat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiRequestController extends Controller
{
    // =========================================================================
    // Helper: ambil umat yang sedang login
    // =========================================================================

    private function umatLogin(): Umat
    {
        return auth()->user()->umat()->aktif()->with([
            'keluarga.kub.wilayah.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.kuasi.keuskupan',
            'keluarga.kub.wilayah.kuasi.keuskupan',
        ])->firstOrFail();
    }

    /**
     * Resolve paroki & keuskupan dari wilayah (semua jalur hierarki)
     */
    private function resolveHierarchy($wilayah): array
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

    // =========================================================================
    // Index: daftar semua request mutasi milik umat yang login
    // =========================================================================

    public function index(): View
    {
        $umat = auth()->user()->umat;

        $mutasiList = Mutasi::with([
                'mutasiUmat.umat',
                'mutasiKeluarga.keluarga.kepalaKeluarga',
                'mutasiAgama.umat',
                'diprosesOleh',
            ])
            ->where('pemohon_umat_id', $umat->id)
            ->latest()
            ->paginate(15);

        return view('portal.mutasi.index', compact('mutasiList'));
    }

    // =========================================================================
    // Show: detail status request
    // =========================================================================

    public function show(Mutasi $mutasi): View|RedirectResponse
    {
        $umat = auth()->user()->umat;

        // Pastikan request ini milik umat yang login
        abort_if((int) $mutasi->pemohon_umat_id !== (int) $umat->id, 403);

        $mutasi->load([
            'mutasiUmat.umat',
            'mutasiUmat.keluargaAsal',
            'mutasiUmat.keluargaTujuan',
            'mutasiUmat.parokiAsal', 'mutasiUmat.parokiTujuan',
            'mutasiUmat.keuskupanAsal', 'mutasiUmat.keuskupanTujuan',
            'mutasiKeluarga.keluarga.kepalaKeluarga',
            'mutasiKeluarga.kubAsal', 'mutasiKeluarga.kubTujuan',
            'mutasiKeluarga.wilayahAsal', 'mutasiKeluarga.wilayahTujuan',
            'mutasiKeluarga.parokiAsal', 'mutasiKeluarga.parokiTujuan',
            'mutasiKeluarga.keuskupanAsal', 'mutasiKeluarga.keuskupanTujuan',
            'mutasiAgama.umat',
            'diprosesOleh',
        ]);

        return view('portal.mutasi.show', compact('mutasi'));
    }

    // =========================================================================
    // Mutasi Umat (request untuk diri sendiri)
    // =========================================================================

    public function createUmat(): View
    {
        $umat          = $this->umatLogin();
        $keluargaAsal  = $umat->keluarga;
        $wilayah       = $keluargaAsal?->kub?->wilayah;
        [$paroki, $keuskupan] = $this->resolveHierarchy($wilayah);

        // Daftar keluarga lain dalam paroki yang sama (exclude keluarga sendiri)
        $keluargaList = Keluarga::with(['kepalaKeluarga', 'kub'])
            ->whereNot('id', $keluargaAsal?->id)
            ->orderBy('id')
            ->get();

        $parokiList    = Paroki::orderBy('nama')->get();
        $keuskupanList = Keuskupan::orderBy('nama')->get();

        return view('portal.mutasi.create-umat', compact(
            'umat', 'keluargaAsal', 'wilayah', 'paroki', 'keuskupan',
            'keluargaList', 'parokiList', 'keuskupanList'
        ));
    }

    public function storeUmat(Request $request): RedirectResponse
    {
        $umat = auth()->user()->umat;

        $validated = $request->validate([
            'sub_jenis'                  => ['required', 'in:pindah_keluarga_ada,pindah_keluarga_baru,paroki,keuskupan'],
            'tanggal'                    => ['required', 'date'],
            'nomor_surat'                => ['nullable', 'string', 'max:100'],
            'keterangan'                 => ['nullable', 'string', 'max:1000'],
            'keluarga_tujuan_id'         => ['nullable', 'exists:keluarga,id'],
            'alamat_baru'                => ['nullable', 'string'],
            'status_tempat_tinggal_baru' => ['nullable', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
            'jadikan_kepala'             => ['nullable', 'boolean'],
            'hubungan_keluarga_baru'     => ['nullable', 'in:Suami,Istri,Ayah,Ibu'],
            'paroki_tujuan_id'           => ['nullable', 'exists:paroki,id'],
            'keuskupan_tujuan_id'        => ['nullable', 'exists:keuskupan,id'],
        ]);

        // Ambil data hierarki asal
        $umatFull = Umat::aktif()->with([
            'keluarga.kub.wilayah.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.kuasi.keuskupan',
            'keluarga.kub.wilayah.kuasi.keuskupan',
        ])->findOrFail($umat->id);

        $keluargaAsal = $umatFull->keluarga;
        $kub          = $keluargaAsal?->kub;
        $wilayah      = $kub?->wilayah;
        [$paroki, $keuskupan] = $this->resolveHierarchy($wilayah);

        // Simpan record mutasi dengan status pending — data TIDAK berubah
        $mutasi = Mutasi::create([
            'jenis'           => 'umat',
            'tanggal'         => $validated['tanggal'],
            'keterangan'      => $validated['keterangan'] ?? null,
            'status'          => 'pending',
            'pemohon_umat_id' => $umat->id,
        ]);

        MutasiUmat::create([
            'mutasi_id'           => $mutasi->id,
            'umat_id'             => $umat->id,
            'sub_jenis'           => $validated['sub_jenis'],
            'nomor_surat'         => $validated['nomor_surat'] ?? null,
            'keluarga_asal_id'    => $keluargaAsal?->id,
            'keluarga_tujuan_id'  => $validated['keluarga_tujuan_id'] ?? null,
            'kub_asal_id'         => $kub?->id,
            'wilayah_asal_id'     => $wilayah?->id,
            'paroki_asal_id'      => $paroki?->id,
            'paroki_tujuan_id'    => $validated['paroki_tujuan_id'] ?? null,
            'keuskupan_asal_id'   => $keuskupan?->id,
            'keuskupan_tujuan_id' => $validated['keuskupan_tujuan_id'] ?? null,
            'alamat_baru'         => $validated['alamat_baru'] ?? null,
            'status_tempat_tinggal_baru' => $validated['status_tempat_tinggal_baru'] ?? null,
        ]);

        return redirect()->route('portal.mutasi.index')
            ->with('success', 'Request mutasi berhasil dikirim. Menunggu persetujuan sekretariat.');
    }

    // =========================================================================
    // Mutasi Keluarga (hanya kepala keluarga yang boleh)
    // =========================================================================

    public function createKeluarga(): View|RedirectResponse
    {
        $umat         = $this->umatLogin();
        $keluarga     = $umat->keluarga;

        // Hanya kepala keluarga yang boleh request mutasi keluarga
        if (!$keluarga || (int) $keluarga->kepala_keluarga_id !== (int) $umat->id) {
            return redirect()->route('portal.mutasi.index')
                ->with('error', 'Hanya kepala keluarga yang dapat mengajukan request mutasi keluarga.');
        }

        $wilayah = $keluarga->kub?->wilayah;
        [$paroki, $keuskupan] = $this->resolveHierarchy($wilayah);

        $parokiList    = Paroki::orderBy('nama')->get();
        $keuskupanList = Keuskupan::orderBy('nama')->get();

        return view('portal.mutasi.create-keluarga', compact(
            'umat', 'keluarga', 'wilayah', 'paroki', 'keuskupan',
            'parokiList', 'keuskupanList'
        ));
    }

    public function storeKeluarga(Request $request): RedirectResponse
    {
        $umat     = auth()->user()->umat;
        $keluarga = Keluarga::with([
            'kub.wilayah.paroki.keuskupan',
            'kub.wilayah.stasi.paroki.keuskupan',
            'kub.wilayah.stasi.kuasi.keuskupan',
            'kub.wilayah.kuasi.keuskupan',
        ])->findOrFail($umat->keluarga_id);

        // Hanya kepala keluarga
        abort_if((int) $keluarga->kepala_keluarga_id !== (int) $umat->id, 403,
            'Hanya kepala keluarga yang dapat mengajukan request mutasi keluarga.');

        $validated = $request->validate([
            'sub_jenis'           => ['required', 'in:keuskupan,paroki,wilayah,kub'],
            'tanggal'             => ['required', 'date'],
            'nomor_surat'         => ['nullable', 'string', 'max:100'],
            'paroki_tujuan_id'    => ['nullable', 'exists:paroki,id'],
            'keuskupan_tujuan_id' => ['nullable', 'exists:keuskupan,id'],
            'keterangan'          => ['nullable', 'string', 'max:1000'],
        ]);

        $kub     = $keluarga->kub;
        $wilayah = $kub?->wilayah;
        [$paroki, $keuskupan] = $this->resolveHierarchy($wilayah);

        // Simpan record mutasi dengan status pending — data TIDAK berubah
        $mutasi = Mutasi::create([
            'jenis'           => 'keluarga',
            'tanggal'         => $validated['tanggal'],
            'keterangan'      => $validated['keterangan'] ?? null,
            'status'          => 'pending',
            'pemohon_umat_id' => $umat->id,
        ]);

        MutasiKeluarga::create([
            'mutasi_id'           => $mutasi->id,
            'keluarga_id'         => $keluarga->id,
            'sub_jenis'           => $validated['sub_jenis'],
            'nomor_surat'         => $validated['nomor_surat'] ?? null,
            'kub_asal_id'         => $kub?->id,
            'wilayah_asal_id'     => $wilayah?->id,
            'paroki_asal_id'      => $paroki?->id,
            'keuskupan_asal_id'   => $keuskupan?->id,
            'kub_tujuan_id'       => null, // tidak diisi umat, sekretariat yang lengkapi
            'wilayah_tujuan_id'   => null,
            'paroki_tujuan_id'    => $validated['paroki_tujuan_id'] ?? null,
            'keuskupan_tujuan_id' => $validated['keuskupan_tujuan_id'] ?? null,
        ]);

        return redirect()->route('portal.mutasi.index')
            ->with('success', 'Request mutasi keluarga berhasil dikirim. Menunggu persetujuan sekretariat.');
    }

    // =========================================================================
    // Mutasi Agama (request untuk diri sendiri)
    // =========================================================================

    public function createAgama(): View
    {
        $umat = auth()->user()->umat;

        return view('portal.mutasi.create-agama', compact('umat'));
    }

    public function storeAgama(Request $request): RedirectResponse
    {
        $umat = auth()->user()->umat;

        $validated = $request->validate([
            'agama_tujuan' => ['required', 'in:protestan,hindu,budha,khonghucu,islam'],
            'tanggal'      => ['required', 'date'],
            'keterangan'   => ['nullable', 'string', 'max:1000'],
        ]);

        // Simpan record mutasi dengan status pending — umat TIDAK langsung di-soft delete
        $mutasi = Mutasi::create([
            'jenis'           => 'agama',
            'tanggal'         => $validated['tanggal'],
            'keterangan'      => $validated['keterangan'] ?? null,
            'status'          => 'pending',
            'pemohon_umat_id' => $umat->id,
        ]);

        MutasiAgama::create([
            'mutasi_id'    => $mutasi->id,
            'umat_id'      => $umat->id,
            'agama_tujuan' => $validated['agama_tujuan'],
        ]);

        return redirect()->route('portal.mutasi.index')
            ->with('success', 'Request mutasi agama berhasil dikirim. Menunggu persetujuan sekretariat.');
    }
    // =========================================================================
    // Mutasi Umat oleh Ketua KUB (mengajukan atas nama umat di KUB-nya)
    // =========================================================================

    public function createUmatKub(): View
    {
        $ketuaUmat = auth()->user()->umat;
        abort_if(!$ketuaUmat, 403, 'Akun belum terhubung ke data umat.');

        $kub = \App\Models\Kub::where('ketua_umat_id', $ketuaUmat->id)->firstOrFail();

        // Daftar umat aktif dalam KUB ini (kecuali ketua sendiri)
        $keluargaIds = Keluarga::where('kub_id', $kub->id)->pluck('id');
        $umatList    = \App\Models\Umat::aktif()
            ->whereIn('keluarga_id', $keluargaIds)
            ->orderBy('nama')
            ->with('keluarga.kepalaKeluarga')
            ->get();

        $keluargaList  = Keluarga::with(['kepalaKeluarga', 'kub'])->orderBy('id')->get();
        $parokiList    = Paroki::orderBy('nama')->get();
        $keuskupanList = Keuskupan::orderBy('nama')->get();

        return view('portal.mutasi.create-umat-kub', compact(
            'kub', 'umatList', 'keluargaList', 'parokiList', 'keuskupanList'
        ));
    }

    public function storeUmatKub(Request $request): RedirectResponse
    {
        $ketuaUmat = auth()->user()->umat;
        abort_if(!$ketuaUmat, 403);

        $kub = \App\Models\Kub::where('ketua_umat_id', $ketuaUmat->id)->firstOrFail();

        $validated = $request->validate([
            'umat_id'                    => ['required', 'exists:umat,id'],
            'sub_jenis'                  => ['required', 'in:pindah_keluarga_ada,pindah_keluarga_baru,paroki,keuskupan'],
            'tanggal'                    => ['required', 'date'],
            'nomor_surat'                => ['nullable', 'string', 'max:100'],
            'keterangan'                 => ['nullable', 'string', 'max:1000'],
            'keluarga_tujuan_id'         => ['nullable', 'exists:keluarga,id'],
            'alamat_baru'                => ['nullable', 'string'],
            'status_tempat_tinggal_baru' => ['nullable', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
            'paroki_tujuan_id'           => ['nullable', 'exists:paroki,id'],
            'keuskupan_tujuan_id'        => ['nullable', 'exists:keuskupan,id'],
        ]);

        // Pastikan umat yang dipilih benar-benar berada dalam KUB ketua ini
        $keluargaIds = Keluarga::where('kub_id', $kub->id)->pluck('id');
        $targetUmat  = \App\Models\Umat::aktif()
            ->whereIn('keluarga_id', $keluargaIds)
            ->findOrFail($validated['umat_id']);

        // Ambil hierarki asal dari umat target
        $umatFull = \App\Models\Umat::aktif()->with([
            'keluarga.kub.wilayah.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.paroki.keuskupan',
            'keluarga.kub.wilayah.stasi.kuasi.keuskupan',
            'keluarga.kub.wilayah.kuasi.keuskupan',
        ])->findOrFail($targetUmat->id);

        $keluargaAsal = $umatFull->keluarga;
        $kubAsal      = $keluargaAsal?->kub;
        $wilayah      = $kubAsal?->wilayah;
        [$paroki, $keuskupan] = $this->resolveHierarchy($wilayah);

        $mutasi = Mutasi::create([
            'jenis'           => 'umat',
            'tanggal'         => $validated['tanggal'],
            'keterangan'      => $validated['keterangan'] ?? null,
            'status'          => 'pending',
            'pemohon_umat_id' => $ketuaUmat->id,
        ]);

        MutasiUmat::create([
            'mutasi_id'                  => $mutasi->id,
            'umat_id'                    => $targetUmat->id,
            'sub_jenis'                  => $validated['sub_jenis'],
            'nomor_surat'                => $validated['nomor_surat'] ?? null,
            'keluarga_asal_id'           => $keluargaAsal?->id,
            'keluarga_tujuan_id'         => $validated['keluarga_tujuan_id'] ?? null,
            'kub_asal_id'                => $kubAsal?->id,
            'wilayah_asal_id'            => $wilayah?->id,
            'paroki_asal_id'             => $paroki?->id,
            'paroki_tujuan_id'           => $validated['paroki_tujuan_id'] ?? null,
            'keuskupan_asal_id'          => $keuskupan?->id,
            'keuskupan_tujuan_id'        => $validated['keuskupan_tujuan_id'] ?? null,
            'alamat_baru'                => $validated['alamat_baru'] ?? null,
            'status_tempat_tinggal_baru' => $validated['status_tempat_tinggal_baru'] ?? null,
        ]);

        return redirect()->route('portal.mutasi.index')
            ->with('success', "Request mutasi umat atas nama <strong>{$targetUmat->nama}</strong> berhasil dikirim. Menunggu persetujuan sekretariat.");
    }
}
