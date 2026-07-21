<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Baptis;
use App\Models\Keluarga;
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

class SakramenAnggotaController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Pastikan user yang login adalah kepala keluarga dari anggota ini.
     * Return Umat (anggota) jika valid.
     */
    private function authorizeKepala(Umat $anggota): void
    {
        $umatId = Auth::user()->umat_id;

        if (!$umatId) {
            abort(403, 'Akun Anda belum terhubung ke data umat.');
        }

        // Cari keluarga dimana user ini adalah kepala keluarga
        $keluarga = Keluarga::where('kepala_keluarga_id', $umatId)->first();

        if (!$keluarga) {
            abort(403, 'Hanya kepala keluarga yang dapat mengelola sakramen anggota keluarga.');
        }

        // Pastikan anggota benar-benar dalam keluarga yang dipimpin user
        if ((int) $anggota->keluarga_id !== (int) $keluarga->id) {
            abort(403, 'Anggota ini tidak berada dalam keluarga Anda.');
        }

        // Kepala keluarga tidak mengelola sakramen dirinya sendiri lewat sini
        if ((int) $anggota->id === (int) $umatId) {
            abort(403, 'Gunakan menu "Sakramen Saya" untuk mengelola data sakramen Anda sendiri.');
        }
    }

    private function getParokiId(): ?int
    {
        return Paroki::first()?->id;
    }

    private function getSakramen(Umat $anggota, string $jenis): ?Sakramen
    {
        return Sakramen::with(['klerus'])
            ->where('umat_id', $anggota->id)
            ->where('jenis_sakramen', $jenis)
            ->first();
    }

    private function getUmatList(Umat $anggota)
    {
        return Umat::aktif()
            ->where('id', '!=', $anggota->id)
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
    // INDEX — Overview semua sakramen anggota
    // ─────────────────────────────────────────────────────────────────────

    public function index(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramenList = Sakramen::where('umat_id', $anggota->id)
            ->with(['klerus', 'baptis', 'komuniPertama', 'krisma', 'pernikahan', 'minyakSuci'])
            ->get()
            ->keyBy('jenis_sakramen');

        return view('portal.keluarga-saya.sakramen-anggota.index', compact('anggota', 'sakramenList'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // BAPTIS
    // ─────────────────────────────────────────────────────────────────────

    public function showBaptis(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen = Sakramen::with(['klerus', 'paroki', 'baptis.klerus', 'baptis.bapakBaptis', 'baptis.ibuBaptis'])
            ->where('umat_id', $anggota->id)
            ->where('jenis_sakramen', 'BAPTIS')
            ->first();
        $baptis     = $sakramen?->baptis;
        $klerusList = Klerus::whereIn('jabatan', ['Pastor', 'Uskup', 'Diakon'])->orderBy('nama')->get();
        $parokiList = Paroki::orderBy('nama')->get();
        $umatWali   = Umat::aktif()->orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.baptis.show', compact('anggota', 'sakramen', 'baptis', 'klerusList', 'parokiList', 'umatWali'));
    }

    public function storeBaptis(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        if ($this->getSakramen($anggota, 'BAPTIS')) {
            return redirect()->route('portal.sakramen-anggota.baptis', $anggota)
                ->with('error', 'Data baptis sudah ada.');
        }

        $data = $request->validate([
            'tanggal_penerimaan'     => ['required', 'date'],
            'paroki_id'              => ['nullable', 'exists:paroki,id'],
            'klerus_id'              => ['nullable', 'exists:klerus,id'],
            'nomor_surat'            => ['nullable', 'string', 'unique:sakramen,nomor_surat'],
            'sumber_baptis'          => ['required', 'in:KATOLIK,PROTESTAN'],
            'nama_baptis'            => ['nullable', 'string', 'max:255'],
            'tgl_baptis'             => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date'],
            'nama_pemberi_protestan' => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'nama_gereja_protestan'  => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'tgl_diterima_katolik'   => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date', 'after_or_equal:tgl_baptis'],
            'bapak_baptis_id'        => ['nullable', 'exists:umat,id'],
            'bapak_baptis_nama'      => ['nullable', 'string', 'max:255'],
            'ibu_baptis_id'          => ['nullable', 'exists:umat,id'],
            'ibu_baptis_nama'        => ['nullable', 'string', 'max:255'],
        ]);

        $this->validateWaliBaptis($request);

        DB::transaction(function () use ($anggota, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $anggota->id,
                'jenis_sakramen'     => 'BAPTIS',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $data['paroki_id'] ?? $this->getParokiId(),
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

        return redirect()->route('portal.sakramen-anggota.baptis', $anggota)
            ->with('success', 'Data Baptis berhasil disimpan.');
    }

    public function editBaptis(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen = Sakramen::with(['klerus', 'paroki', 'baptis.klerus', 'baptis.bapakBaptis', 'baptis.ibuBaptis'])
            ->where('umat_id', $anggota->id)
            ->where('jenis_sakramen', 'BAPTIS')
            ->first();
        abort_if(!$sakramen, 404, 'Data baptis belum ada.');

        $baptis     = $sakramen->baptis;
        $klerusList = Klerus::whereIn('jabatan', ['Pastor', 'Uskup', 'Diakon'])->orderBy('nama')->get();
        $parokiList = Paroki::orderBy('nama')->get();
        $umatWali   = Umat::aktif()->orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.baptis.form', compact('anggota', 'sakramen', 'baptis', 'klerusList', 'parokiList', 'umatWali'));
    }

    public function updateBaptis(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen = $this->getSakramen($anggota, 'BAPTIS');
        abort_if(!$sakramen, 404);

        $data = $request->validate([
            'tanggal_penerimaan'     => ['required', 'date'],
            'paroki_id'              => ['nullable', 'exists:paroki,id'],
            'klerus_id'              => ['nullable', 'exists:klerus,id'],
            'nomor_surat'            => ['nullable', 'string', Rule::unique('sakramen', 'nomor_surat')->ignore($sakramen->id)],
            'sumber_baptis'          => ['required', 'in:KATOLIK,PROTESTAN'],
            'nama_baptis'            => ['nullable', 'string', 'max:255'],
            'tgl_baptis'             => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date'],
            'nama_pemberi_protestan' => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'nama_gereja_protestan'  => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'string', 'max:255'],
            'tgl_diterima_katolik'   => [Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'), 'nullable', 'date', 'after_or_equal:tgl_baptis'],
            'bapak_baptis_id'        => ['nullable', 'exists:umat,id'],
            'bapak_baptis_nama'      => ['nullable', 'string', 'max:255'],
            'ibu_baptis_id'          => ['nullable', 'exists:umat,id'],
            'ibu_baptis_nama'        => ['nullable', 'string', 'max:255'],
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

        return redirect()->route('portal.sakramen-anggota.baptis', $anggota)
            ->with('success', 'Data Baptis berhasil diperbarui.');
    }

    private function validateWaliBaptis(Request $request): void
    {
        $bapakTerisi = $request->filled('bapak_baptis_id') || $request->filled('bapak_baptis_nama');
        $ibuTerisi   = $request->filled('ibu_baptis_id') || $request->filled('ibu_baptis_nama');

        if (! $bapakTerisi && ! $ibuTerisi) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'bapak_baptis_nama' => 'Minimal bapak baptis atau ibu baptis harus diisi.',
                'ibu_baptis_nama'   => 'Minimal bapak baptis atau ibu baptis harus diisi.',
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // KOMUNI PERTAMA
    // ─────────────────────────────────────────────────────────────────────

    public function showKomuni(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen   = $this->getSakramen($anggota, 'KOMUNI_PERTAMA');
        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.komuni.show', compact('anggota', 'sakramen', 'klerusList'));
    }

    public function storeKomuni(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        if ($this->getSakramen($anggota, 'KOMUNI_PERTAMA')) {
            return redirect()->route('portal.sakramen-anggota.komuni', $anggota)
                ->with('error', 'Data komuni pertama sudah ada.');
        }

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
        ]);

        DB::transaction(function () use ($anggota, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $anggota->id,
                'jenis_sakramen'     => 'KOMUNI_PERTAMA',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId(),
                'klerus_id'          => $data['klerus_id'] ?? null,
            ]);
            KomuniPertama::create(['sakramen_id' => $sakramen->id]);
        });

        return redirect()->route('portal.sakramen-anggota.komuni', $anggota)
            ->with('success', 'Data Komuni Pertama berhasil disimpan.');
    }

    public function editKomuni(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen = $this->getSakramen($anggota, 'KOMUNI_PERTAMA');
        abort_if(!$sakramen, 404, 'Data komuni pertama belum ada.');
        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.komuni.form', compact('anggota', 'sakramen', 'klerusList'));
    }

    public function updateKomuni(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen = $this->getSakramen($anggota, 'KOMUNI_PERTAMA');
        abort_if(!$sakramen, 404);

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
        ]);

        $sakramen->update([
            'tanggal_penerimaan' => $data['tanggal_penerimaan'],
            'klerus_id'          => $data['klerus_id'] ?? null,
        ]);

        return redirect()->route('portal.sakramen-anggota.komuni', $anggota)
            ->with('success', 'Data Komuni Pertama berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // KRISMA
    // ─────────────────────────────────────────────────────────────────────

    public function showKrisma(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen  = $this->getSakramen($anggota, 'KRISMA');
        $krisma    = $sakramen?->krisma;
        $uskupList = Klerus::where('jabatan', 'uskup')->orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.krisma.show', compact('anggota', 'sakramen', 'krisma', 'uskupList'));
    }

    public function storeKrisma(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        if ($this->getSakramen($anggota, 'KRISMA')) {
            return redirect()->route('portal.sakramen-anggota.krisma', $anggota)
                ->with('error', 'Data krisma sudah ada.');
        }

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'nama_krisma'        => ['nullable', 'string', 'max:255'],
            'uskup_id'           => ['nullable', 'exists:klerus,id'],
        ]);

        DB::transaction(function () use ($anggota, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $anggota->id,
                'jenis_sakramen'     => 'KRISMA',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId(),
                'klerus_id'          => $data['uskup_id'] ?? null,
            ]);
            Krisma::create([
                'sakramen_id' => $sakramen->id,
                'uskup_id'    => $data['uskup_id'] ?? null,
                'nama_krisma' => $data['nama_krisma'] ?? null,
            ]);
        });

        return redirect()->route('portal.sakramen-anggota.krisma', $anggota)
            ->with('success', 'Data Krisma berhasil disimpan.');
    }

    public function editKrisma(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen  = $this->getSakramen($anggota, 'KRISMA');
        abort_if(!$sakramen, 404, 'Data krisma belum ada.');
        $krisma    = $sakramen->krisma;
        $uskupList = Klerus::where('jabatan', 'uskup')->orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.krisma.form', compact('anggota', 'sakramen', 'krisma', 'uskupList'));
    }

    public function updateKrisma(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen = $this->getSakramen($anggota, 'KRISMA');
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

        return redirect()->route('portal.sakramen-anggota.krisma', $anggota)
            ->with('success', 'Data Krisma berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // PERNIKAHAN
    // ─────────────────────────────────────────────────────────────────────

    public function showPernikahan(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen   = $this->getSakramen($anggota, 'PERNIKAHAN');
        $pernikahan = $sakramen?->pernikahan;
        $umatList   = $this->getUmatList($anggota);

        return view('portal.keluarga-saya.sakramen-anggota.pernikahan.show', compact('anggota', 'sakramen', 'pernikahan', 'umatList'));
    }

    public function storePernikahan(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        if ($this->getSakramen($anggota, 'PERNIKAHAN')) {
            return redirect()->route('portal.sakramen-anggota.pernikahan', $anggota)
                ->with('error', 'Data pernikahan sudah ada.');
        }

        $data = $request->validate([
            'tanggal_penerimaan'    => ['required', 'date'],
            'pasangan_id'           => ['nullable', 'exists:umat,id', Rule::notIn([$anggota->id])],
            'pasangan_nama'         => ['nullable', 'string', 'max:255'],
            'pasangan_agama'        => ['nullable', 'string', 'max:100'],
            'jenis_pernikahan'      => ['required', Rule::in(array_keys(Pernikahan::JENIS))],
            'tanggal_nikah_katolik' => ['nullable', 'date'],
            'tanggal_catatan_sipil' => ['nullable', 'date'],
            'izin_beda_gereja'      => ['boolean'],
            'dispensasi'            => ['boolean'],
        ]);
        $this->validatePasangan($request);

        DB::transaction(function () use ($anggota, $data, $request) {
            $sakramen = Sakramen::create([
                'umat_id'            => $anggota->id,
                'jenis_sakramen'     => 'PERNIKAHAN',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId(),
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

        return redirect()->route('portal.sakramen-anggota.pernikahan', $anggota)
            ->with('success', 'Data Pernikahan berhasil disimpan.');
    }

    public function editPernikahan(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen   = $this->getSakramen($anggota, 'PERNIKAHAN');
        abort_if(!$sakramen, 404, 'Data pernikahan belum ada.');
        $pernikahan = $sakramen->pernikahan;
        $umatList   = $this->getUmatList($anggota);

        return view('portal.keluarga-saya.sakramen-anggota.pernikahan.form', compact('anggota', 'sakramen', 'pernikahan', 'umatList'));
    }

    public function updatePernikahan(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $sakramen = $this->getSakramen($anggota, 'PERNIKAHAN');
        abort_if(!$sakramen, 404);

        $data = $request->validate([
            'tanggal_penerimaan'    => ['required', 'date'],
            'pasangan_id'           => ['nullable', 'exists:umat,id', Rule::notIn([$anggota->id])],
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

        return redirect()->route('portal.sakramen-anggota.pernikahan', $anggota)
            ->with('success', 'Data Pernikahan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // MINYAK SUCI
    // ─────────────────────────────────────────────────────────────────────

    public function indexMinyakSuci(Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $daftarMinyakSuci = Sakramen::where('umat_id', $anggota->id)
            ->where('jenis_sakramen', 'MINYAK_SUCI')
            ->with(['klerus', 'minyakSuci'])
            ->orderByDesc('tanggal_penerimaan')
            ->get();

        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.minyak-suci.index', compact('anggota', 'daftarMinyakSuci', 'klerusList'));
    }

    public function storeMinyakSuci(Request $request, Umat $anggota)
    {
        $this->authorizeKepala($anggota);

        $data = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
            'tempat_terima'      => ['nullable', 'string', 'max:255'],
            'nama_pemberi'       => ['nullable', 'string', 'max:255'],
            'keterangan_sebab'   => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($anggota, $data) {
            $sakramen = Sakramen::create([
                'umat_id'            => $anggota->id,
                'jenis_sakramen'     => 'MINYAK_SUCI',
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'paroki_id'          => $this->getParokiId(),
                'klerus_id'          => $data['klerus_id'] ?? null,
            ]);
            MinyakSuci::create([
                'sakramen_id'      => $sakramen->id,
                'tempat_terima'    => $data['tempat_terima'] ?? null,
                'nama_pemberi'     => $data['nama_pemberi'] ?? null,
                'keterangan_sebab' => $data['keterangan_sebab'] ?? null,
            ]);
        });

        return redirect()->route('portal.sakramen-anggota.minyak-suci', $anggota)
            ->with('success', 'Data Minyak Suci berhasil ditambahkan.');
    }

    public function editMinyakSuci(Umat $anggota, Sakramen $sakramen)
    {
        $this->authorizeKepala($anggota);
        abort_if((int) $sakramen->umat_id !== (int) $anggota->id, 403);
        $sakramen->load(['klerus', 'minyakSuci']);
        $klerusList = Klerus::orderBy('nama')->get();

        return view('portal.keluarga-saya.sakramen-anggota.minyak-suci.form', compact('anggota', 'sakramen', 'klerusList'));
    }

    public function updateMinyakSuci(Request $request, Umat $anggota, Sakramen $sakramen)
    {
        $this->authorizeKepala($anggota);
        abort_if((int) $sakramen->umat_id !== (int) $anggota->id, 403);

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

        return redirect()->route('portal.sakramen-anggota.minyak-suci', $anggota)
            ->with('success', 'Data Minyak Suci berhasil diperbarui.');
    }
}
