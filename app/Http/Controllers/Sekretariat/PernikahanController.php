<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Klerus;
use App\Models\Paroki;
use App\Models\Pernikahan;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PernikahanController extends Controller
{
    public function index(Request $request)
    {
        $pernikahan = Sakramen::jenis('PERNIKAHAN')
            ->with([
                'umat',
                'paroki',
                'klerus',
                'pernikahan.pasangan',
            ])
            ->when($request->paroki_id, fn($q) => $q->where('paroki_id', $request->paroki_id))
            ->when($request->jenis_pernikahan, fn($q) => $q->whereHas('pernikahan', fn($q2) =>
                $q2->where('jenis_pernikahan', $request->jenis_pernikahan)
            ))
            ->latest('tanggal_penerimaan')
            ->paginate(20);

        $jenisList = Pernikahan::JENIS;

        return view('sekretariat.sakramen.pernikahan.index', compact('pernikahan', 'jenisList'));
    }

    public function create()
    {
        $umat      = Umat::aktif()->orderBy('nama')->get();
        $paroki    = Paroki::orderBy('nama')->get();
        $klerus    = Klerus::whereIn('jabatan', ['PASTOR', 'USKUP', 'DIAKON'])
                        ->orderBy('nama')
                        ->get();
        $jenisList = Pernikahan::JENIS;

        return view('sekretariat.sakramen.pernikahan.create', compact(
            'umat',
            'paroki',
            'klerus',
            'jenisList',
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

            // --- Pernikahan (child) ---
            'jenis_pernikahan' => ['required', Rule::in(array_keys(Pernikahan::JENIS))],

            // Pasangan — FK atau manual, salah satu wajib
            'pasangan_id'    => ['nullable', 'exists:umat,id'],
            'pasangan_nama'  => ['nullable', 'string', 'max:255'],
            'pasangan_agama' => ['nullable', 'string', 'max:255'],

            'izin_beda_gereja'      => ['boolean'],
            'dispensasi'            => ['boolean'],
            'tanggal_nikah_katolik' => ['nullable', 'date'],
            'tanggal_catatan_sipil' => ['nullable', 'date'],
        ]);

        $this->validatePasangan($request);

        DB::transaction(function () use ($validated, $request) {
            $sakramen = Sakramen::create([
                'umat_id'            => $validated['umat_id'],
                'jenis_sakramen'     => 'PERNIKAHAN',
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            Pernikahan::create([
                'sakramen_id'      => $sakramen->id,
                'jenis_pernikahan' => $validated['jenis_pernikahan'],

                'pasangan_id'    => $validated['pasangan_id'] ?? null,
                'pasangan_nama'  => empty($validated['pasangan_id'])
                                    ? ($validated['pasangan_nama'] ?? null)
                                    : null,
                'pasangan_agama' => empty($validated['pasangan_id'])
                                    ? ($validated['pasangan_agama'] ?? null)
                                    : null,

                'izin_beda_gereja'      => $request->boolean('izin_beda_gereja'),
                'dispensasi'            => $request->boolean('dispensasi'),
                'tanggal_nikah_katolik' => $validated['tanggal_nikah_katolik'] ?? null,
                'tanggal_catatan_sipil' => $validated['tanggal_catatan_sipil'] ?? null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.pernikahan.index')
            ->with('success', 'Data Pernikahan berhasil disimpan.');
    }

    public function show(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'PERNIKAHAN', 404);

        $sakramen->load([
            'umat',
            'paroki',
            'klerus',
            'pernikahan.pasangan',
        ]);

        return view('sekretariat.sakramen.pernikahan.show', compact('sakramen'));
    }

    public function edit(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'PERNIKAHAN', 404);

        $sakramen->load([
            'umat',
            'paroki',
            'klerus',
            'pernikahan.pasangan',
        ]);

        $umat      = Umat::aktif()->orderBy('nama')->get();
        $paroki    = Paroki::orderBy('nama')->get();
        $klerus    = Klerus::whereIn('jabatan', ['PASTOR', 'USKUP', 'DIAKON'])
                        ->orderBy('nama')
                        ->get();
        $jenisList = Pernikahan::JENIS;

        return view('sekretariat.sakramen.pernikahan.edit', compact(
            'sakramen',
            'umat',
            'paroki',
            'klerus',
            'jenisList',
        ));
    }

    public function update(Request $request, Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'PERNIKAHAN', 404);

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

            // --- Pernikahan (child) ---
            'jenis_pernikahan' => ['required', Rule::in(array_keys(Pernikahan::JENIS))],

            'pasangan_id'    => ['nullable', 'exists:umat,id'],
            'pasangan_nama'  => ['nullable', 'string', 'max:255'],
            'pasangan_agama' => ['nullable', 'string', 'max:255'],

            'izin_beda_gereja'      => ['boolean'],
            'dispensasi'            => ['boolean'],
            'tanggal_nikah_katolik' => ['nullable', 'date'],
            'tanggal_catatan_sipil' => ['nullable', 'date'],
        ]);

        $this->validatePasangan($request);

        DB::transaction(function () use ($validated, $request, $sakramen) {
            $sakramen->update([
                'umat_id'            => $validated['umat_id'],
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            $sakramen->pernikahan->update([
                'jenis_pernikahan' => $validated['jenis_pernikahan'],

                'pasangan_id'    => $validated['pasangan_id'] ?? null,
                'pasangan_nama'  => empty($validated['pasangan_id'])
                                    ? ($validated['pasangan_nama'] ?? null)
                                    : null,
                'pasangan_agama' => empty($validated['pasangan_id'])
                                    ? ($validated['pasangan_agama'] ?? null)
                                    : null,

                'izin_beda_gereja'      => $request->boolean('izin_beda_gereja'),
                'dispensasi'            => $request->boolean('dispensasi'),
                'tanggal_nikah_katolik' => $validated['tanggal_nikah_katolik'] ?? null,
                'tanggal_catatan_sipil' => $validated['tanggal_catatan_sipil'] ?? null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.pernikahan.show', $sakramen)
            ->with('success', 'Data Pernikahan berhasil diperbarui.');
    }

    public function destroy(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'PERNIKAHAN', 404);

        $sakramen->delete();

        return redirect()
            ->route('sekretariat.sakramen.pernikahan.index')
            ->with('success', 'Data Pernikahan berhasil dihapus.');
    }

    private function validatePasangan(Request $request): void
    {
        $pasanganTerisi = $request->filled('pasangan_id') || $request->filled('pasangan_nama');

        abort_if(! $pasanganTerisi, 422, 'Data pasangan wajib diisi.');
    }
}
