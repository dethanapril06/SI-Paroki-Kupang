<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\KomuniPertama;
use App\Models\Klerus;
use App\Models\Paroki;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KomuniPertamaController extends Controller
{
    public function index(Request $request)
    {
        $komuniPertama = Sakramen::jenis('KOMUNI_PERTAMA')
            ->with([
                'umat',
                'paroki',
                'klerus',
                'komuniPertama',
            ])
            ->when($request->umat_id, fn($q) => $q->where('umat_id', $request->umat_id))
            ->when($request->paroki_id, fn($q) => $q->where('paroki_id', $request->paroki_id))
            ->latest('tanggal_penerimaan')
            ->paginate(20);

        return view('sekretariat.sakramen.komuni-pertama.index', compact('komuniPertama'));
    }

    public function create()
    {
        $umat   = Umat::aktif()->orderBy('nama')->get();
        $paroki = Paroki::orderBy('nama')->get();
        $klerus = Klerus::whereIn('jabatan', ['PASTOR', 'USKUP', 'DIAKON'])
            ->orderBy('nama')
            ->get();

        return view('sekretariat.sakramen.komuni-pertama.create', compact(
            'umat',
            'paroki',
            'klerus',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'umat_id'            => ['required', 'exists:umat,id'],
            'tanggal_penerimaan' => ['required', 'date'],
            'paroki_id'          => ['nullable', 'exists:paroki,id'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
            'nomor_surat'        => ['nullable', 'string', 'unique:sakramen,nomor_surat'],
        ]);

        // Satu umat hanya boleh menerima komuni pertama sekali
        $sudahAda = Sakramen::jenis('KOMUNI_PERTAMA')
            ->where('umat_id', $validated['umat_id'])
            ->exists();

        abort_if($sudahAda, 422, 'Umat ini sudah tercatat menerima Komuni Pertama.');

        DB::transaction(function () use ($validated) {
            $sakramen = Sakramen::create([
                'umat_id'            => $validated['umat_id'],
                'jenis_sakramen'     => 'KOMUNI_PERTAMA',
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            KomuniPertama::create([
                'sakramen_id' => $sakramen->id,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.komuni-pertama.index')
            ->with('success', 'Data Komuni Pertama berhasil disimpan.');
    }

    public function show(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KOMUNI_PERTAMA', 404);

        $sakramen->load([
            'umat',
            'paroki',
            'klerus',
            'komuniPertama',
        ]);

        return view('sekretariat.sakramen.komuni-pertama.show', compact('sakramen'));
    }

    public function edit(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KOMUNI_PERTAMA', 404);

        $sakramen->load(['umat', 'paroki', 'klerus', 'komuniPertama']);

        $umat   = Umat::aktif()->orderBy('nama')->get();
        $paroki = Paroki::orderBy('nama')->get();
        $klerus = Klerus::whereIn('jabatan', ['PASTOR', 'USKUP', 'DIAKON'])
            ->orderBy('nama')
            ->get();

        return view('sekretariat.sakramen.komuni-pertama.edit', compact(
            'sakramen',
            'umat',
            'paroki',
            'klerus',
        ));
    }

    public function update(Request $request, Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KOMUNI_PERTAMA', 404);

        $validated = $request->validate([
            'umat_id'            => ['required', 'exists:umat,id'],
            'tanggal_penerimaan' => ['required', 'date'],
            'paroki_id'          => ['nullable', 'exists:paroki,id'],
            'klerus_id'          => ['nullable', 'exists:klerus,id'],
            'nomor_surat'        => [
                'nullable',
                'string',
                Rule::unique('sakramen', 'nomor_surat')->ignore($sakramen->id),
            ],
        ]);

        // Cek duplikat jika umat_id diganti
        $sudahAda = Sakramen::jenis('KOMUNI_PERTAMA')
            ->where('umat_id', $validated['umat_id'])
            ->where('id', '!=', $sakramen->id)
            ->exists();

        abort_if($sudahAda, 422, 'Umat ini sudah tercatat menerima Komuni Pertama.');

        // Hanya update sakramen (parent), child komuni_pertama tidak ada kolom tambahan
        $sakramen->update([
            'umat_id'            => $validated['umat_id'],
            'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
            'paroki_id'          => $validated['paroki_id'] ?? null,
            'klerus_id'          => $validated['klerus_id'] ?? null,
            'nomor_surat'        => $validated['nomor_surat'] ?? null,
        ]);

        return redirect()
            ->route('sekretariat.sakramen.komuni-pertama.show', $sakramen)
            ->with('success', 'Data Komuni Pertama berhasil diperbarui.');
    }

    public function destroy(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KOMUNI_PERTAMA', 404);

        $sakramen->delete();

        return redirect()
            ->route('sekretariat.sakramen.komuni-pertama.index')
            ->with('success', 'Data Komuni Pertama berhasil dihapus.');
    }
}
