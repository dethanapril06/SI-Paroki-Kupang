<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Klerus;
use App\Models\Krisma;
use App\Models\Paroki;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KrismaController extends Controller
{
    public function index(Request $request)
    {
        $krisma = Sakramen::jenis('KRISMA')
            ->with([
                'umat',
                'paroki',
                'klerus',
                'krisma.uskup',
            ])
            ->when($request->umat_id, fn($q) => $q->where('umat_id', $request->umat_id))
            ->when($request->paroki_id, fn($q) => $q->where('paroki_id', $request->paroki_id))
            ->latest('tanggal_penerimaan')
            ->paginate(20);

        return view('sekretariat.sakramen.krisma.index', compact('krisma'));
    }

    public function create()
    {
        $umat   = Umat::aktif()->orderBy('nama')->get();
        $paroki = Paroki::orderBy('nama')->get();

        // Klerus pemimpin misa (pastor/diakon)
        $klerus = Klerus::whereIn('jabatan', ['PASTOR', 'DIAKON'])
            ->orderBy('nama')
            ->get();

        // Uskup yang menerimakan krisma
        $uskup = Klerus::where('jabatan', 'USKUP')
            ->orderBy('nama')
            ->get();

        return view('sekretariat.sakramen.krisma.create', compact(
            'umat',
            'paroki',
            'klerus',
            'uskup',
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

            // --- Krisma (child) ---
            'uskup_id'   => ['nullable', 'exists:klerus,id'],
            'nama_krisma' => ['nullable', 'string', 'max:255'],
        ]);

        // Satu umat hanya boleh menerima krisma sekali
        $sudahAda = Sakramen::jenis('KRISMA')
            ->where('umat_id', $validated['umat_id'])
            ->exists();

        abort_if($sudahAda, 422, 'Umat ini sudah tercatat menerima Krisma.');

        DB::transaction(function () use ($validated) {
            $sakramen = Sakramen::create([
                'umat_id'            => $validated['umat_id'],
                'jenis_sakramen'     => 'KRISMA',
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            Krisma::create([
                'sakramen_id' => $sakramen->id,
                'uskup_id'   => $validated['uskup_id'] ?? null,
                'nama_krisma' => $validated['nama_krisma'] ?? null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.krisma.index')
            ->with('success', 'Data Krisma berhasil disimpan.');
    }

    public function show(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KRISMA', 404);

        $sakramen->load([
            'umat',
            'paroki',
            'klerus',
            'krisma.uskup',
        ]);

        return view('sekretariat.sakramen.krisma.show', compact('sakramen'));
    }

    public function edit(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KRISMA', 404);

        $sakramen->load(['umat', 'paroki', 'klerus', 'krisma.uskup']);

        $umat   = Umat::aktif()->orderBy('nama')->get();
        $paroki = Paroki::orderBy('nama')->get();
        $klerus = Klerus::whereIn('jabatan', ['PASTOR', 'DIAKON'])
            ->orderBy('nama')
            ->get();
        $uskup  = Klerus::where('jabatan', 'USKUP')
            ->orderBy('nama')
            ->get();

        return view('sekretariat.sakramen.krisma.edit', compact(
            'sakramen',
            'umat',
            'paroki',
            'klerus',
            'uskup',
        ));
    }

    public function update(Request $request, Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KRISMA', 404);

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

            // --- Krisma (child) ---
            'uskup_id'   => ['nullable', 'exists:klerus,id'],
            'nama_krisma' => ['nullable', 'string', 'max:255'],
        ]);

        // Cek duplikat jika umat_id diganti
        $sudahAda = Sakramen::jenis('KRISMA')
            ->where('umat_id', $validated['umat_id'])
            ->where('id', '!=', $sakramen->id)
            ->exists();

        abort_if($sudahAda, 422, 'Umat ini sudah tercatat menerima Krisma.');

        DB::transaction(function () use ($validated, $sakramen) {
            $sakramen->update([
                'umat_id'            => $validated['umat_id'],
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'paroki_id'          => $validated['paroki_id'] ?? null,
                'klerus_id'          => $validated['klerus_id'] ?? null,
                'nomor_surat'        => $validated['nomor_surat'] ?? null,
            ]);

            $sakramen->krisma->update([
                'uskup_id'   => $validated['uskup_id'] ?? null,
                'nama_krisma' => $validated['nama_krisma'] ?? null,
            ]);
        });

        return redirect()
            ->route('sekretariat.sakramen.krisma.show', $sakramen)
            ->with('success', 'Data Krisma berhasil diperbarui.');
    }

    public function destroy(Sakramen $sakramen)
    {
        abort_if($sakramen->jenis_sakramen !== 'KRISMA', 404);

        $sakramen->delete();

        return redirect()
            ->route('sekretariat.sakramen.krisma.index')
            ->with('success', 'Data Krisma berhasil dihapus.');
    }
}
