<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Baptis;
use App\Models\Klerus;
use App\Models\Paroki;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BaptisController extends Controller
{
    public function index(Request $request)
    {
        $baptis = Sakramen::jenis('BAPTIS')
            ->with([
                'umat',
                'paroki',
                'klerus',
                'baptis.klerus',
                'baptis.bapakBaptis',
                'baptis.ibuBaptis',
            ])
            ->when($request->umat_id, fn($q) => $q->where('umat_id', $request->umat_id))
            ->when($request->paroki_id, fn($q) => $q->where('paroki_id', $request->paroki_id))
            ->when($request->sumber_baptis, fn($q) => $q->whereHas('baptis', fn($q2) =>
                $q2->where('sumber_baptis', $request->sumber_baptis)
            ))
            ->latest('tanggal_penerimaan')
            ->paginate(20);

        return view('sekretariat.sakramen.baptis.index', compact('baptis'));
    }

    public function create()
    {
        $umat    = Umat::aktif()->orderBy('nama')->get();
        $paroki  = Paroki::orderBy('nama')->get();

        // Klerus Katolik untuk pemberi baptis Katolik
        $klerusKatolik = Klerus::whereIn('jabatan', ['Pastor', 'Uskup', 'Diakon'])
            ->orderBy('nama')
            ->get();

        // Umat untuk pilihan wali baptis
        $umatWali = Umat::aktif()->orderBy('nama')->get();

        return view('sekretariat.sakramen.baptis.create', compact(
            'umat',
            'paroki',
            'klerusKatolik',
            'umatWali',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // --- Sakramen (parent) ---
            'umat_id'            => ['required', 'exists:umat,id'],
            'tanggal_penerimaan' => ['required', 'date'],
            'paroki_id'          => ['nullable', 'exists:paroki,id'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
            'nomor_surat'        => ['nullable', 'string', 'unique:sakramen,nomor_surat'],

            // --- Baptis (child) ---
            'sumber_baptis' => ['required', Rule::in(['KATOLIK', 'PROTESTAN'])],

            // Pemberi baptis Katolik
            'baptis_klerus_id' => [
                Rule::requiredIf($request->sumber_baptis === 'KATOLIK'),
                'nullable',
                'exists:klerus,id',
            ],

            // Pemberi baptis Protestan
            'nama_pemberi_protestan' => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'string',
                'max:255',
            ],
            'nama_gereja_protestan' => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'string',
                'max:255',
            ],

            'tgl_baptis'           => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'date',
            ],
            'tgl_diterima_katolik' => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'date',
                'after_or_equal:tgl_baptis',
            ],

            'nama_baptis' => ['nullable', 'string', 'max:255'],

            // Wali baptis — salah satu dari FK atau nama manual
            'bapak_baptis_id'   => ['nullable', 'exists:umat,id'],
            'bapak_baptis_nama' => ['nullable', 'string', 'max:255'],
            'ibu_baptis_id'     => ['nullable', 'exists:umat,id'],
            'ibu_baptis_nama'   => ['nullable', 'string', 'max:255'],
        ]);

        // Pastikan salah satu wali baptis terisi (FK atau manual)
        $this->validateWaliBaptis($request);

        DB::transaction(function () use ($validated, $request) {
            $sakramen = Sakramen::create([
                'umat_id'            => $validated['umat_id'],
                'jenis_sakramen'     => 'BAPTIS',
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            Baptis::create([
                'sakramen_id'            => $sakramen->id,
                'sumber_baptis'          => $validated['sumber_baptis'],

                // Pemberi baptis
                'klerus_id'              => $request->sumber_baptis === 'KATOLIK'
                                            ? ($validated['baptis_klerus_id'] ?? null)
                                            : null,
                'nama_pemberi_protestan' => $request->sumber_baptis === 'PROTESTAN'
                                            ? ($validated['nama_pemberi_protestan'] ?? null)
                                            : null,
                'nama_gereja_protestan'  => $request->sumber_baptis === 'PROTESTAN'
                                            ? ($validated['nama_gereja_protestan'] ?? null)
                                            : null,

                // Tanggal
                'tgl_baptis'             => $request->sumber_baptis === 'KATOLIK' ? $validated['tanggal_penerimaan'] : ($validated['tgl_baptis'] ?? null),
                'tgl_diterima_katolik'   => $request->sumber_baptis === 'PROTESTAN'
                                            ? ($validated['tgl_diterima_katolik'] ?? null)
                                            : null,

                'nama_baptis'            => $validated['nama_baptis'] ?? null,

                // Wali baptis
                'bapak_baptis_id'        => $validated['bapak_baptis_id'] ?? null,
                'bapak_baptis_nama'      => empty($validated['bapak_baptis_id'])
                                            ? ($validated['bapak_baptis_nama'] ?? null)
                                            : null,
                'ibu_baptis_id'          => $validated['ibu_baptis_id'] ?? null,
                'ibu_baptis_nama'        => empty($validated['ibu_baptis_id'])
                                            ? ($validated['ibu_baptis_nama'] ?? null)
                                            : null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.baptis.index')
            ->with('success', 'Data baptis berhasil disimpan.');
    }

    public function show(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'BAPTIS', 404);

        $sakramen->load([
            'umat',
            'paroki',
            'klerus',
            'baptis.klerus',
            'baptis.bapakBaptis',
            'baptis.ibuBaptis',
        ]);

        return view('sekretariat.sakramen.baptis.show', compact('sakramen'));
    }

    public function edit(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'BAPTIS', 404);

        $sakramen->load([
            'baptis.klerus',
            'baptis.bapakBaptis',
            'baptis.ibuBaptis',
        ]);

        $umat          = Umat::aktif()->orderBy('nama')->get();
        $paroki        = Paroki::orderBy('nama')->get();
        $klerusKatolik = Klerus::whereIn('jabatan', ['Pastor', 'Uskup', 'Diakon'])
            ->orderBy('nama')
            ->get();
        $umatWali      = Umat::aktif()->orderBy('nama')->get();

        return view('sekretariat.sakramen.baptis.edit', compact(
            'sakramen',
            'umat',
            'paroki',
            'klerusKatolik',
            'umatWali',
        ));
    }

    public function update(Request $request, Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'BAPTIS', 404);

        $validated = $request->validate([
            // --- Sakramen (parent) ---
            'umat_id'            => ['required', 'exists:umat,id'],
            'tanggal_penerimaan' => ['required', 'date'],
            'paroki_id'          => ['nullable', 'exists:paroki,id'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
            'nomor_surat'        => [
                'nullable',
                'string',
                Rule::unique('sakramen', 'nomor_surat')->ignore($sakramen->id),
            ],

            // --- Baptis (child) ---
            'sumber_baptis' => ['required', Rule::in(['KATOLIK', 'PROTESTAN'])],

            'baptis_klerus_id' => [
                Rule::requiredIf($request->sumber_baptis === 'KATOLIK'),
                'nullable',
                'exists:klerus,id',
            ],

            'nama_pemberi_protestan' => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'string',
                'max:255',
            ],
            'nama_gereja_protestan' => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'string',
                'max:255',
            ],

            'tgl_baptis'           => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'date',
            ],
            'tgl_diterima_katolik' => [
                Rule::requiredIf($request->sumber_baptis === 'PROTESTAN'),
                'nullable',
                'date',
                'after_or_equal:tgl_baptis',
            ],

            'nama_baptis'     => ['nullable', 'string', 'max:255'],
            'bapak_baptis_id' => ['nullable', 'exists:umat,id'],
            'bapak_baptis_nama' => ['nullable', 'string', 'max:255'],
            'ibu_baptis_id'   => ['nullable', 'exists:umat,id'],
            'ibu_baptis_nama' => ['nullable', 'string', 'max:255'],
        ]);

        $this->validateWaliBaptis($request);

        DB::transaction(function () use ($validated, $request, $sakramen) {
            $sakramen->update([
                'umat_id'            => $validated['umat_id'],
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            $sakramen->baptis->update([
                'sumber_baptis'          => $validated['sumber_baptis'],

                'klerus_id'              => $request->sumber_baptis === 'KATOLIK'
                                            ? ($validated['baptis_klerus_id'] ?? null)
                                            : null,
                'nama_pemberi_protestan' => $request->sumber_baptis === 'PROTESTAN'
                                            ? ($validated['nama_pemberi_protestan'] ?? null)
                                            : null,
                'nama_gereja_protestan'  => $request->sumber_baptis === 'PROTESTAN'
                                            ? ($validated['nama_gereja_protestan'] ?? null)
                                            : null,

                'tgl_baptis'             => $request->sumber_baptis === 'KATOLIK' ? $validated['tanggal_penerimaan'] : ($validated['tgl_baptis'] ?? null),
                'tgl_diterima_katolik'   => $request->sumber_baptis === 'PROTESTAN'
                                            ? ($validated['tgl_diterima_katolik'] ?? null)
                                            : null,

                'nama_baptis'            => $validated['nama_baptis'] ?? null,

                'bapak_baptis_id'        => $validated['bapak_baptis_id'] ?? null,
                'bapak_baptis_nama'      => empty($validated['bapak_baptis_id'])
                                            ? ($validated['bapak_baptis_nama'] ?? null)
                                            : null,
                'ibu_baptis_id'          => $validated['ibu_baptis_id'] ?? null,
                'ibu_baptis_nama'        => empty($validated['ibu_baptis_id'])
                                            ? ($validated['ibu_baptis_nama'] ?? null)
                                            : null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.baptis.show', $sakramen)
            ->with('success', 'Data baptis berhasil diperbarui.');
    }

    public function destroy(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'BAPTIS', 404);

        // cascadeOnDelete di migration sudah handle hapus child baptis
        $sakramen->delete();

        return redirect()
            ->route('sekretariat.sakramen.baptis.index')
            ->with('success', 'Data baptis berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Private helper: validasi wali baptis
    // Minimal bapak atau ibu baptis harus terisi (FK atau manual)
    // ------------------------------------------------------------------
    private function validateWaliBaptis(Request $request): void
    {
        $bapakTerisi = $request->filled('bapak_baptis_id') || $request->filled('bapak_baptis_nama');
        $ibuTerisi   = $request->filled('ibu_baptis_id') || $request->filled('ibu_baptis_nama');

        if (! $bapakTerisi && ! $ibuTerisi) {
            abort(422, 'Minimal bapak baptis atau ibu baptis harus diisi.');
        }
    }
}
