<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Klerus;
use App\Models\MinyakSuci;
use App\Models\Paroki;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MinyakSuciController extends Controller
{
    public function index(Request $request)
    {
        $minyakSuci = Sakramen::jenis('MINYAK_SUCI')
            ->with([
                'umat',
                'paroki',
                'klerus',
                'minyakSuci',
            ])
            ->when($request->umat_id, fn($q) => $q->where('umat_id', $request->umat_id))
            ->when($request->paroki_id, fn($q) => $q->where('paroki_id', $request->paroki_id))
            ->latest('tanggal_penerimaan')
            ->paginate(20);

        return view('sekretariat.sakramen.minyak-suci.index', compact('minyakSuci'));
    }

    public function create()
    {
        $umat   = Umat::aktif()->orderBy('nama')->get();
        $paroki = Paroki::orderBy('nama')->get();
        $klerus = Klerus::whereIn('jabatan', ['PASTOR', 'USKUP', 'DIAKON'])
            ->orderBy('nama')
            ->get();

        return view('sekretariat.sakramen.minyak-suci.create', compact(
            'umat',
            'paroki',
            'klerus',
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

            // --- MinyakSuci (child) ---
            // Jika klerus_id diisi → nama_pemberi tidak perlu
            // Jika klerus_id kosong → nama_pemberi wajib diisi
            'tempat_terima'    => ['required', 'string', 'max:255'],
            'nama_pemberi'     => [
                Rule::requiredIf(empty($request->klerus_id)),
                'nullable',
                'string',
                'max:255',
            ],
            'keterangan_sebab' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $sakramen = Sakramen::create([
                'umat_id'            => $validated['umat_id'],
                'jenis_sakramen'     => 'MINYAK_SUCI',
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            MinyakSuci::create([
                'sakramen_id'      => $sakramen->id,
                'tempat_terima'    => $validated['tempat_terima'],
                // Jika klerus terdaftar → nama_pemberi tidak disimpan (sudah via relasi)
                'nama_pemberi'     => empty($validated['klerus_id'])
                                      ? ($validated['nama_pemberi'] ?? null)
                                      : null,
                'keterangan_sebab' => $validated['keterangan_sebab'] ?? null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.minyak-suci.index')
            ->with('success', 'Data Minyak Suci berhasil disimpan.');
    }

    public function show(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'MINYAK_SUCI', 404);

        $sakramen->load([
            'umat',
            'paroki',
            'klerus',
            'minyakSuci',
        ]);

        return view('sekretariat.sakramen.minyak-suci.show', compact('sakramen'));
    }

    public function edit(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'MINYAK_SUCI', 404);

        $sakramen->load(['umat', 'paroki', 'klerus', 'minyakSuci']);

        $umat   = Umat::aktif()->orderBy('nama')->get();
        $paroki = Paroki::orderBy('nama')->get();
        $klerus = Klerus::whereIn('jabatan', ['PASTOR', 'USKUP', 'DIAKON'])
            ->orderBy('nama')
            ->get();

        return view('sekretariat.sakramen.minyak-suci.edit', compact(
            'sakramen',
            'umat',
            'paroki',
            'klerus',
        ));
    }

    public function update(Request $request, Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'MINYAK_SUCI', 404);

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

            // --- MinyakSuci (child) ---
            'tempat_terima'    => ['required', 'string', 'max:255'],
            'nama_pemberi'     => [
                Rule::requiredIf(empty($request->klerus_id)),
                'nullable',
                'string',
                'max:255',
            ],
            'keterangan_sebab' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $sakramen) {
            $sakramen->update([
                'umat_id'            => $validated['umat_id'],
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            $sakramen->minyakSuci->update([
                'tempat_terima'    => $validated['tempat_terima'],
                'nama_pemberi'     => empty($validated['klerus_id'])
                                      ? ($validated['nama_pemberi'] ?? null)
                                      : null,
                'keterangan_sebab' => $validated['keterangan_sebab'] ?? null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.minyak-suci.show', $sakramen)
            ->with('success', 'Data Minyak Suci berhasil diperbarui.');
    }

    public function destroy(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'MINYAK_SUCI', 404);

        $sakramen->delete();

        return redirect()
            ->route('sekretariat.sakramen.minyak-suci.index')
            ->with('success', 'Data Minyak Suci berhasil dihapus.');
    }
}
