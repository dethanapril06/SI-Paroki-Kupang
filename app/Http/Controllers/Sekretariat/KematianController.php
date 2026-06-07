<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Kematian;
use App\Models\Umat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KematianController extends Controller
{
    public function index(Request $request)
    {
        $kematian = Kematian::with(['umat'])
            ->when($request->search, fn($q) => $q->whereHas('umat', fn($q2) =>
                $q2->where('nama', 'like', '%' . $request->search . '%')
            ))
            ->latest('tanggal_meninggal')
            ->paginate(20);

        return view('sekretariat.kematian.index', compact('kematian'));
    }

    public function create()
    {
        // Hanya umat yang belum tercatat meninggal dan belum ada di tabel kematian
        $umat   = Umat::aktif()
                      ->where('status_almarhum', false)
                      ->whereDoesntHave('kematian')
                      ->orderBy('nama')
                      ->get();

        return view('sekretariat.kematian.create', compact('umat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'umat_id'            => ['required', 'exists:umat,id', 'unique:kematian,umat_id'],
            'tanggal_meninggal'  => ['required', 'date'],
            'tempat_meninggal'   => ['nullable', 'string', 'max:255'],
            'sebab_kematian'     => ['nullable', 'string', 'max:255'],
            'tanggal_pemakaman'  => ['nullable', 'date', 'after_or_equal:tanggal_meninggal'],
            'tempat_pemakaman'   => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            Kematian::create($validated);

            // Update status almarhum di tabel umat
            Umat::where('id', $validated['umat_id'])
                ->update(['status_almarhum' => true]);

            // Hapus akun login (User) almarhum agar tidak bisa login lagi
            User::where('umat_id', $validated['umat_id'])->delete();
        });

        return redirect()
            ->route('sekretariat.kematian.index')
            ->with('success', 'Data kematian berhasil disimpan.');
    }

    public function show(Kematian $kematian)
    {
        $kematian->load(['umat']);

        return view('sekretariat.kematian.show', compact('kematian'));
    }

    public function edit(Kematian $kematian)
    {
        $kematian->load(['umat']);

        // Semua umat aktif + umat yang sedang diedit (agar muncul di dropdown)
        $umat   = Umat::aktif()
                      ->where(fn($q) =>
                        $q->where('status_almarhum', false)
                          ->orWhere('id', $kematian->umat_id)
                      )
                      ->orderBy('nama')
                      ->get();

        return view('sekretariat.kematian.edit', compact('kematian', 'umat'));
    }

    public function update(Request $request, Kematian $kematian)
    {
        $validated = $request->validate([
            'umat_id'            => ['required', 'exists:umat,id',
                                     Rule::unique('kematian', 'umat_id')->ignore($kematian->id)],
            'tanggal_meninggal'  => ['required', 'date'],
            'tempat_meninggal'   => ['nullable', 'string', 'max:255'],
            'sebab_kematian'     => ['nullable', 'string', 'max:255'],
            'tanggal_pemakaman'  => ['nullable', 'date', 'after_or_equal:tanggal_meninggal'],
            'tempat_pemakaman'   => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $kematian) {
            $umatLama = $kematian->umat_id;

            $kematian->update($validated);

            // Jika umat_id diganti, kembalikan status umat lama dan update umat baru
            if ($umatLama !== (int) $validated['umat_id']) {
                Umat::where('id', $umatLama)->update(['status_almarhum' => false]);
                Umat::where('id', $validated['umat_id'])->update(['status_almarhum' => true]);

                // Hapus akun login (User) untuk umat baru
                User::where('umat_id', $validated['umat_id'])->delete();

                // Pulihkan akun login (User) untuk umat lama jika sebelumnya di-softdelete
                User::withTrashed()->where('umat_id', $umatLama)->restore();
            }
        });

        return redirect()
            ->route('sekretariat.kematian.show', $kematian)
            ->with('success', 'Data kematian berhasil diperbarui.');
    }

    public function destroy(Kematian $kematian)
    {
        DB::transaction(function () use ($kematian) {
            // Kembalikan status almarhum ke false saat data dihapus
            Umat::where('id', $kematian->umat_id)
                ->update(['status_almarhum' => false]);

            // Pulihkan akun login (User) almarhum jika data kematian dihapus
            User::withTrashed()->where('umat_id', $kematian->umat_id)->restore();

            $kematian->delete();
        });

        return redirect()
            ->route('sekretariat.kematian.index')
            ->with('success', 'Data kematian berhasil dihapus.');
    }
}
