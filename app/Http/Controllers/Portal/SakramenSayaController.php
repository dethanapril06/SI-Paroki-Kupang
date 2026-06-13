<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Baptis;
use App\Models\Klerus;
use App\Models\KomuniPertama;
use App\Models\Krisma;
use App\Models\MinyakSuci;
use App\Models\Paroki;
use App\Models\Pernikahan;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SakramenSayaController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────

    private function getMyUmat(): ?Umat
    {
        $umatId = Auth::user()->umat_id;
        return $umatId ? Umat::aktif()->with('keluarga.kub')->find($umatId) : null;
    }

    private function getParokiId(?Umat $umat): ?int
    {
        // Ambil paroki dari KUB umat, fallback ke paroki pertama
        return Paroki::first()?->id;
    }

    private function getSakramen(Umat $umat, string $jenis): ?Sakramen
    {
        return Sakramen::with(['klerus'])
            ->where('umat_id', $umat->id)
            ->where('jenis_sakramen', $jenis)
            ->first();
    }

    private function getUmatList(?Umat $currentUmat = null)
    {
        return Umat::aktif()
            ->when($currentUmat, fn ($q) => $q->where('id', '!=', $currentUmat->id))
            ->orderBy('nama')
            ->get();
    }

    private function validatePasangan(Request $request): void
    {
        $request->validate([
            'pasangan_id'    => ['nullable', 'exists:umat,id'],
            'pasangan_nama'  => [Rule::requiredIf(fn () => ! $request->filled('pasangan_id')), 'nullable', 'string', 'max:255'],
            'pasangan_agama' => [Rule::requiredIf(fn () => ! $request->filled('pasangan_id')), 'nullable', 'string', 'max:100'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // INDEX — Overview semua sakramen
    // ─────────────────────────────────────────────────────────────────────

    public function index()
    {
        $umat = $this->getMyUmat();

        if (!$umat) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Data umat belum terhubung ke akun Anda.');
        }

        $sakramenList = Sakramen::where('umat_id', $umat->id)
            ->with(['klerus', 'baptis', 'komuniPertama', 'krisma', 'pernikahan', 'minyakSuci'])
            ->get()
            ->keyBy('jenis_sakramen');

        return view('portal.sakramen-saya.index', compact('umat', 'sakramenList'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // BAPTIS
    // ─────────────────────────────────────────────────────────────────────

    public function showBaptis()
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? Sakramen::with([
            'klerus',
            'paroki',
            'baptis.klerus',
            'baptis.bapakBaptis',
            'baptis.ibuBaptis'
        ])
            ->where('umat_id', $umat->id)
            ->where('jenis_sakramen', 'BAPTIS')
            ->first() : null;
        $baptis   = $sakramen?->baptis;
        $klerusList = Klerus::whereIn('jabatan', ['Pastor', 'Uskup', 'Diakon'])
            ->orderBy('nama')
            ->get();
        $parokiList = Paroki::orderBy('nama')->get();
        $umatWali   = Umat::aktif()->orderBy('nama')->get();

        return view('portal.sakramen-saya.baptis.show', compact('umat', 'sakramen', 'baptis', 'klerusList', 'parokiList', 'umatWali'));
    }

    public function storeBaptis(Request $request)
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat, 403);

        if ($this->getSakramen($umat, 'BAPTIS')) {
            return redirect()->route('portal.sakramen-saya.baptis')->with('error', 'Data baptis sudah ada.');
        }

        $data = $request->validate([
            // --- Sakramen (parent) ---
            'tanggal_penerimaan'      => ['required', 'date'],
            'paroki_id'               => ['nullable', 'exists:paroki,id'],
            'klerus_id'               => ['nullable', 'exists:klerus,id'],
            'nomor_surat'             => ['nullable', 'string', 'unique:sakramen,nomor_surat'],

            // --- Baptis (child) ---
            'sumber_baptis'           => ['required', 'in:KATOLIK,PROTESTAN'],
            'nama_baptis'             => ['nullable', 'string', 'max:255'],
            'tgl_baptis'              => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date'],
            'nama_pemberi_protestan'  => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'nama_gereja_protestan'   => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'tgl_diterima_katolik'    => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date', 'after_or_equal:tgl_baptis'],
            'bapak_baptis_id'         => ['nullable', 'exists:umat,id'],
            'bapak_baptis_nama'       => ['nullable', 'string', 'max:255'],
            'ibu_baptis_id'           => ['nullable', 'exists:umat,id'],
            'ibu_baptis_nama'         => ['nullable', 'string', 'max:255'],
        ]);

        $this->validateWaliBaptis($request);

        DB::transaction(function () use ($umat, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $umat->id,
                'jenis_sakramen'     => 'BAPTIS',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $data['paroki_id'] ?? $this->getParokiId($umat),
                'klerus_id'          => $data['sumber_baptis'] === 'KATOLIK' ? ($data['klerus_id'] ?? null) : null,
                'nomor_surat'        => $data['nomor_surat'] ?? null,
            ]);

            Baptis::create([
                'sakramen_id'            => $sakramen->id,
                'sumber_baptis'          => $data['sumber_baptis'],
                'nama_baptis'            => $data['nama_baptis'] ?? null,
                'tgl_baptis'             => $data['sumber_baptis'] === 'KATOLIK' ? $data['tanggal_penerimaan'] : ($data['tgl_baptis'] ?? null),
                'klerus_id'              => $data['sumber_baptis'] === 'KATOLIK' ? ($data['klerus_id'] ?? null) : null,
                'nama_pemberi_protestan' => $data['sumber_baptis'] === 'PROTESTAN' ? ($data['nama_pemberi_protestan'] ?? null) : null,
                'nama_gereja_protestan'  => $data['sumber_baptis'] === 'PROTESTAN' ? ($data['nama_gereja_protestan'] ?? null) : null,
                'tgl_diterima_katolik'   => $data['sumber_baptis'] === 'PROTESTAN' ? ($data['tgl_diterima_katolik'] ?? null) : null,
                'bapak_baptis_id'        => $data['bapak_baptis_id'] ?? null,
                'bapak_baptis_nama'      => empty($data['bapak_baptis_id']) ? ($data['bapak_baptis_nama'] ?? null) : null,
                'ibu_baptis_id'          => $data['ibu_baptis_id'] ?? null,
                'ibu_baptis_nama'        => empty($data['ibu_baptis_id']) ? ($data['ibu_baptis_nama'] ?? null) : null,
            ]);
        });

        return redirect()->route('portal.sakramen-saya.baptis')
            ->with('success', 'Data Baptis berhasil disimpan.');
    }

    public function editBaptis()
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? Sakramen::with([
            'klerus',
            'paroki',
            'baptis.klerus',
            'baptis.bapakBaptis',
            'baptis.ibuBaptis'
        ])
            ->where('umat_id', $umat->id)
            ->where('jenis_sakramen', 'BAPTIS')
            ->first() : null;
        abort_if(!$sakramen, 404, 'Data baptis belum ada.');
        $baptis   = $sakramen->baptis;
        $klerusList = Klerus::whereIn('jabatan', ['Pastor', 'Uskup', 'Diakon'])
            ->orderBy('nama')
            ->get();
        $parokiList = Paroki::orderBy('nama')->get();
        $umatWali   = Umat::aktif()->orderBy('nama')->get();

        return view('portal.sakramen-saya.baptis.form', compact('umat', 'sakramen', 'baptis', 'klerusList', 'parokiList', 'umatWali'));
    }

    public function updateBaptis(Request $request)
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'BAPTIS') : null;
        abort_if(!$sakramen, 404);

        $data = $request->validate([
            // --- Sakramen (parent) ---
            'tanggal_penerimaan'      => ['required', 'date'],
            'paroki_id'               => ['nullable', 'exists:paroki,id'],
            'klerus_id'               => ['nullable', 'exists:klerus,id'],
            'nomor_surat'             => ['nullable', 'string', Rule::unique('sakramen', 'nomor_surat')->ignore($sakramen->id)],

            // --- Baptis (child) ---
            'sumber_baptis'           => ['required', 'in:KATOLIK,PROTESTAN'],
            'nama_baptis'             => ['nullable', 'string', 'max:255'],
            'tgl_baptis'              => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date'],
            'nama_pemberi_protestan'  => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'nama_gereja_protestan'   => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'tgl_diterima_katolik'    => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date', 'after_or_equal:tgl_baptis'],
            'bapak_baptis_id'         => ['nullable', 'exists:umat,id'],
            'bapak_baptis_nama'       => ['nullable', 'string', 'max:255'],
            'ibu_baptis_id'           => ['nullable', 'exists:umat,id'],
            'ibu_baptis_nama'         => ['nullable', 'string', 'max:255'],
        ]);

        $this->validateWaliBaptis($request);

        DB::transaction(function () use ($sakramen, $data) {
            $sakramen->update([
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $data['paroki_id'] ?? $sakramen->paroki_id,
                'klerus_id'          => $data['sumber_baptis'] === 'KATOLIK' ? ($data['klerus_id'] ?? null) : null,
                'nomor_surat'        => $data['nomor_surat'] ?? null,
            ]);
            $sakramen->baptis->update([
                'sumber_baptis'          => $data['sumber_baptis'],
                'nama_baptis'            => $data['nama_baptis'] ?? null,
                'tgl_baptis'             => $data['sumber_baptis'] === 'KATOLIK' ? $data['tanggal_penerimaan'] : ($data['tgl_baptis'] ?? null),
                'klerus_id'              => $data['sumber_baptis'] === 'KATOLIK' ? ($data['klerus_id'] ?? null) : null,
                'nama_pemberi_protestan' => $data['sumber_baptis'] === 'PROTESTAN' ? ($data['nama_pemberi_protestan'] ?? null) : null,
                'nama_gereja_protestan'  => $data['sumber_baptis'] === 'PROTESTAN' ? ($data['nama_gereja_protestan'] ?? null) : null,
                'tgl_diterima_katolik'   => $data['sumber_baptis'] === 'PROTESTAN' ? ($data['tgl_diterima_katolik'] ?? null) : null,
                'bapak_baptis_id'        => $data['bapak_baptis_id'] ?? null,
                'bapak_baptis_nama'      => empty($data['bapak_baptis_id']) ? ($data['bapak_baptis_nama'] ?? null) : null,
                'ibu_baptis_id'          => $data['ibu_baptis_id'] ?? null,
                'ibu_baptis_nama'        => empty($data['ibu_baptis_id']) ? ($data['ibu_baptis_nama'] ?? null) : null,
            ]);
        });

        return redirect()->route('portal.sakramen-saya.baptis')
            ->with('success', 'Data Baptis berhasil diperbarui.');
    }

    private function validateWaliBaptis(Request $request): void
    {
        $bapakTerisi = $request->filled('bapak_baptis_id') || $request->filled('bapak_baptis_nama');
        $ibuTerisi   = $request->filled('ibu_baptis_id') || $request->filled('ibu_baptis_nama');

        if (! $bapakTerisi && ! $ibuTerisi) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'bapak_baptis_nama' => 'Minimal bapak baptis atau ibu baptis harus diisi.',
                'ibu_baptis_nama' => 'Minimal bapak baptis atau ibu baptis harus diisi.',
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // KOMUNI PERTAMA
    // ─────────────────────────────────────────────────────────────────────

    public function showKomuni()
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'KOMUNI_PERTAMA') : null;
        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.sakramen-saya.komuni.show', compact('umat', 'sakramen', 'klerusList'));
    }

    public function storeKomuni(Request $request)
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat, 403);

        if ($this->getSakramen($umat, 'KOMUNI_PERTAMA')) {
            return redirect()->route('portal.sakramen-saya.komuni')->with('error', 'Data komuni pertama sudah ada.');
        }

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
        ]);

        DB::transaction(function () use ($umat, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $umat->id,
                'jenis_sakramen'     => 'KOMUNI_PERTAMA',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId($umat),
                'klerus_id'          => $data['klerus_id'] ?? null,
            ]);
            KomuniPertama::create(['sakramen_id' => $sakramen->id]);
        });

        return redirect()->route('portal.sakramen-saya.komuni')
            ->with('success', 'Data Komuni Pertama berhasil disimpan.');
    }

    public function editKomuni()
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'KOMUNI_PERTAMA') : null;
        abort_if(!$sakramen, 404, 'Data komuni pertama belum ada.');
        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.sakramen-saya.komuni.form', compact('umat', 'sakramen', 'klerusList'));
    }

    public function updateKomuni(Request $request)
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'KOMUNI_PERTAMA') : null;
        abort_if(!$sakramen, 404);

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
        ]);

        $sakramen->update([
            'tanggal_penerimaan' => $data['tanggal_penerimaan'],
            'klerus_id'          => $data['klerus_id'] ?? null,
        ]);

        return redirect()->route('portal.sakramen-saya.komuni')
            ->with('success', 'Data Komuni Pertama berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // KRISMA
    // ─────────────────────────────────────────────────────────────────────

    public function showKrisma()
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'KRISMA') : null;
        $krisma   = $sakramen?->krisma;
        $uskupList  = Klerus::where('jabatan', 'uskup')->orderBy('nama')->get();

        return view('portal.sakramen-saya.krisma.show', compact('umat', 'sakramen', 'krisma', 'uskupList'));
    }

    public function storeKrisma(Request $request)
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat, 403);

        if ($this->getSakramen($umat, 'KRISMA')) {
            return redirect()->route('portal.sakramen-saya.krisma')->with('error', 'Data krisma sudah ada.');
        }

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'nama_krisma'        => ['nullable', 'string', 'max:255'],
            'uskup_id'           => ['nullable', 'exists:klerus,id'],
        ]);

        DB::transaction(function () use ($umat, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $umat->id,
                'jenis_sakramen'     => 'KRISMA',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId($umat),
                'klerus_id'          => $data['uskup_id'] ?? null,
            ]);
            Krisma::create([
                'sakramen_id' => $sakramen->id,
                'uskup_id'    => $data['uskup_id'] ?? null,
                'nama_krisma' => $data['nama_krisma'] ?? null,
            ]);
        });

        return redirect()->route('portal.sakramen-saya.krisma')
            ->with('success', 'Data Krisma berhasil disimpan.');
    }

    public function editKrisma()
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'KRISMA') : null;
        abort_if(!$sakramen, 404, 'Data krisma belum ada.');
        $krisma    = $sakramen->krisma;
        $uskupList = Klerus::where('jabatan', 'uskup')->orderBy('nama')->get();

        return view('portal.sakramen-saya.krisma.form', compact('umat', 'sakramen', 'krisma', 'uskupList'));
    }

    public function updateKrisma(Request $request)
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'KRISMA') : null;
        abort_if(!$sakramen, 404);

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'nama_krisma'        => ['nullable', 'string', 'max:255'],
            'uskup_id'           => ['nullable', 'exists:klerus,id'],
        ]);

        DB::transaction(function () use ($sakramen, $data) {
            $sakramen->update([
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'klerus_id'          => $data['uskup_id'] ?? null,
            ]);
            $sakramen->krisma->update([
                'uskup_id'    => $data['uskup_id'] ?? null,
                'nama_krisma' => $data['nama_krisma'] ?? null,
            ]);
        });

        return redirect()->route('portal.sakramen-saya.krisma')
            ->with('success', 'Data Krisma berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // PERNIKAHAN
    // ─────────────────────────────────────────────────────────────────────

    public function showPernikahan()
    {
        $umat       = $this->getMyUmat();
        $sakramen   = $umat ? $this->getSakramen($umat, 'PERNIKAHAN') : null;
        $pernikahan = $sakramen?->pernikahan;
        $umatList   = $umat ? $this->getUmatList($umat) : collect();

        return view('portal.sakramen-saya.pernikahan.show', compact('umat', 'sakramen', 'pernikahan', 'umatList'));
    }

    public function storePernikahan(Request $request)
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat, 403);

        if ($this->getSakramen($umat, 'PERNIKAHAN')) {
            return redirect()->route('portal.sakramen-saya.pernikahan')->with('error', 'Data pernikahan sudah ada.');
        }

        $data = $request->validate([
            'tanggal_penerimaan'    => ['required', 'date'],
            'pasangan_id'           => ['nullable', 'exists:umat,id', Rule::notIn([$umat->id])],
            'pasangan_nama'         => ['nullable', 'string', 'max:255'],
            'pasangan_agama'        => ['nullable', 'string', 'max:100'],
            'jenis_pernikahan'      => ['required', Rule::in(array_keys(Pernikahan::JENIS))],
            'tanggal_nikah_katolik' => ['nullable', 'date'],
            'tanggal_catatan_sipil' => ['nullable', 'date'],
            'izin_beda_gereja'      => ['boolean'],
            'dispensasi'            => ['boolean'],
        ]);
        $this->validatePasangan($request);

        DB::transaction(function () use ($umat, $data, $request) {
            $sakramen = Sakramen::create([
                'umat_id'            => $umat->id,
                'jenis_sakramen'     => 'PERNIKAHAN',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId($umat),
            ]);
            Pernikahan::create([
                'sakramen_id'           => $sakramen->id,
                'pasangan_id'           => $data['pasangan_id'] ?? null,
                'pasangan_nama'         => empty($data['pasangan_id']) ? ($data['pasangan_nama'] ?? null) : null,
                'pasangan_agama'        => empty($data['pasangan_id']) ? ($data['pasangan_agama'] ?? null) : null,
                'jenis_pernikahan'      => $data['jenis_pernikahan'],
                'tanggal_nikah_katolik' => $data['tanggal_nikah_katolik'] ?? null,
                'tanggal_catatan_sipil' => $data['tanggal_catatan_sipil'] ?? null,
                'izin_beda_gereja'      => $request->boolean('izin_beda_gereja'),
                'dispensasi'            => $request->boolean('dispensasi'),
            ]);
        });

        return redirect()->route('portal.sakramen-saya.pernikahan')
            ->with('success', 'Data Pernikahan berhasil disimpan.');
    }

    public function editPernikahan()
    {
        $umat       = $this->getMyUmat();
        $sakramen   = $umat ? $this->getSakramen($umat, 'PERNIKAHAN') : null;
        abort_if(!$sakramen, 404, 'Data pernikahan belum ada.');
        $pernikahan = $sakramen->pernikahan;
        $umatList   = $this->getUmatList($umat);

        return view('portal.sakramen-saya.pernikahan.form', compact('umat', 'sakramen', 'pernikahan', 'umatList'));
    }

    public function updatePernikahan(Request $request)
    {
        $umat     = $this->getMyUmat();
        $sakramen = $umat ? $this->getSakramen($umat, 'PERNIKAHAN') : null;
        abort_if(!$sakramen, 404);

        $data = $request->validate([
            'tanggal_penerimaan'    => ['required', 'date'],
            'pasangan_id'           => ['nullable', 'exists:umat,id', Rule::notIn([$umat->id])],
            'pasangan_nama'         => ['nullable', 'string', 'max:255'],
            'pasangan_agama'        => ['nullable', 'string', 'max:100'],
            'jenis_pernikahan'      => ['required', Rule::in(array_keys(Pernikahan::JENIS))],
            'tanggal_nikah_katolik' => ['nullable', 'date'],
            'tanggal_catatan_sipil' => ['nullable', 'date'],
            'izin_beda_gereja'      => ['boolean'],
            'dispensasi'            => ['boolean'],
        ]);
        $this->validatePasangan($request);

        DB::transaction(function () use ($sakramen, $data, $request) {
            $sakramen->update(['tanggal_penerimaan' => $data['tanggal_penerimaan']]);
            $sakramen->pernikahan->update([
                'pasangan_id'           => $data['pasangan_id'] ?? null,
                'pasangan_nama'         => empty($data['pasangan_id']) ? ($data['pasangan_nama'] ?? null) : null,
                'pasangan_agama'        => empty($data['pasangan_id']) ? ($data['pasangan_agama'] ?? null) : null,
                'jenis_pernikahan'      => $data['jenis_pernikahan'],
                'tanggal_nikah_katolik' => $data['tanggal_nikah_katolik'] ?? null,
                'tanggal_catatan_sipil' => $data['tanggal_catatan_sipil'] ?? null,
                'izin_beda_gereja'      => $request->boolean('izin_beda_gereja'),
                'dispensasi'            => $request->boolean('dispensasi'),
            ]);
        });

        return redirect()->route('portal.sakramen-saya.pernikahan')
            ->with('success', 'Data Pernikahan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // MINYAK SUCI
    // ─────────────────────────────────────────────────────────────────────

    public function indexMinyakSuci()
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat, 403);

        $daftarMinyakSuci = Sakramen::where('umat_id', $umat->id)
            ->where('jenis_sakramen', 'MINYAK_SUCI')
            ->with(['klerus', 'minyakSuci'])
            ->orderByDesc('tanggal_penerimaan')
            ->get();

        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.sakramen-saya.minyak-suci.index', compact('umat', 'daftarMinyakSuci', 'klerusList'));
    }

    public function storeMinyakSuci(Request $request)
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat, 403);

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
            'tempat_terima'      => ['nullable', 'string', 'max:255'],
            'nama_pemberi'       => ['nullable', 'string', 'max:255'],
            'keterangan_sebab'   => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($umat, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $umat->id,
                'jenis_sakramen'     => 'MINYAK_SUCI',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId($umat),
                'klerus_id'          => $data['klerus_id'] ?? null,
            ]);
            MinyakSuci::create([
                'sakramen_id'      => $sakramen->id,
                'tempat_terima'    => $data['tempat_terima'] ?? null,
                'nama_pemberi'     => $data['nama_pemberi'] ?? null,
                'keterangan_sebab' => $data['keterangan_sebab'] ?? null,
            ]);
        });

        return redirect()->route('portal.sakramen-saya.minyak-suci')
            ->with('success', 'Data Minyak Suci berhasil ditambahkan.');
    }

    public function editMinyakSuci(Sakramen $sakramen)
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat || (int) $sakramen->umat_id !== (int) $umat->id, 403);
        $sakramen->load(['klerus', 'minyakSuci']);
        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.sakramen-saya.minyak-suci.form', compact('umat', 'sakramen', 'klerusList'));
    }

    public function updateMinyakSuci(Request $request, Sakramen $sakramen)
    {
        $umat = $this->getMyUmat();
        abort_if(!$umat || (int) $sakramen->umat_id !== (int) $umat->id, 403);

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
            'tempat_terima'      => ['nullable', 'string', 'max:255'],
            'nama_pemberi'       => ['nullable', 'string', 'max:255'],
            'keterangan_sebab'   => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($sakramen, $data) {
            $sakramen->update([
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'klerus_id'          => $data['klerus_id'] ?? null,
            ]);
            $sakramen->minyakSuci->update([
                'tempat_terima'    => $data['tempat_terima'] ?? null,
                'nama_pemberi'     => $data['nama_pemberi'] ?? null,
                'keterangan_sebab' => $data['keterangan_sebab'] ?? null,
            ]);
        });

        return redirect()->route('portal.sakramen-saya.minyak-suci')
            ->with('success', 'Data Minyak Suci berhasil diperbarui.');
    }
}
