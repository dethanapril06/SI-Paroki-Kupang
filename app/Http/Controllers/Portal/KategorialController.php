<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKategorial;
use App\Models\Kategorial;
use App\Models\Umat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategorialController extends Controller
{
    private function ownedOrFail(Kategorial $kategorial): void
    {
        $umat = auth()->user()->umat;
        abort_if((int) $kategorial->ketua_umat_id !== (int) $umat?->id, 403, 'Anda bukan ketua kategorial ini.');
    }

    public function index(): View
    {
        $umat = auth()->user()->umat;
        $kategorialList = Kategorial::where('ketua_umat_id', $umat?->id)
            ->withCount(['anggota as anggota_aktif' => fn($q) => $q->where('anggota_kategorial.status', 'Aktif')])
            ->get();
        return view('portal.kategorial.index', compact('kategorialList'));
    }

    public function show(Kategorial $kategorial): View
    {
        $this->ownedOrFail($kategorial);
        $kategorial->load(['anggota' => fn($q) => $q->orderByPivot('jabatan')]);
        $umatTersedia = Umat::aktif()
            ->where('status_almarhum', false)
            ->whereNotIn('id', $kategorial->anggota->pluck('id'))
            ->orderBy('nama')->get();
        return view('portal.kategorial.show', compact('kategorial', 'umatTersedia'));
    }

    public function edit(Kategorial $kategorial): View
    {
        $this->ownedOrFail($kategorial);
        return view('portal.kategorial.edit', compact('kategorial'));
    }

    public function update(Request $request, Kategorial $kategorial): RedirectResponse
    {
        $this->ownedOrFail($kategorial);
        $validated = $request->validate(['nama' => ['required', 'string', 'max:150']]);
        $kategorial->update(['nama' => $validated['nama']]);
        return redirect()->route('portal.kategorial.show', $kategorial)
            ->with('success', 'Informasi kategorial berhasil diperbarui.');
    }

    public function storeAnggota(Request $request, Kategorial $kategorial): RedirectResponse
    {
        $this->ownedOrFail($kategorial);
        $validated = $request->validate([
            'umat_id'           => ['required', 'exists:umat,id'],
            'jabatan'           => ['required', 'in:Ketua,Wakil Ketua,Sekretaris,Bendahara,Anggota'],
            'bidang_tugas'      => ['nullable', 'string', 'max:150'],
            'tanggal_bergabung' => ['required', 'date'],
        ]);
        $sudahAda = AnggotaKategorial::where('kategorial_id', $kategorial->id)
            ->where('umat_id', $validated['umat_id'])->exists();
        if ($sudahAda) {
            return back()->with('error', 'Umat ini sudah terdaftar sebagai anggota kategorial.');
        }
        AnggotaKategorial::create([
            'kategorial_id'     => $kategorial->id,
            'umat_id'           => $validated['umat_id'],
            'jabatan'           => $validated['jabatan'],
            'bidang_tugas'      => $validated['bidang_tugas'] ?? null,
            'tanggal_bergabung' => $validated['tanggal_bergabung'],
            'status'            => 'Aktif',
        ]);
        return redirect()->route('portal.kategorial.show', $kategorial)
            ->with('success', 'Anggota baru berhasil ditambahkan.');
    }

    public function editAnggota(Kategorial $kategorial, AnggotaKategorial $anggota): View
    {
        $this->ownedOrFail($kategorial);
        abort_if((int) $anggota->kategorial_id !== (int) $kategorial->id, 404);
        $anggota->load('umat');
        return view('portal.kategorial.anggota-edit', compact('kategorial', 'anggota'));
    }

    public function updateAnggota(Request $request, Kategorial $kategorial, AnggotaKategorial $anggota): RedirectResponse
    {
        $this->ownedOrFail($kategorial);
        abort_if((int) $anggota->kategorial_id !== (int) $kategorial->id, 404);
        $validated = $request->validate([
            'jabatan'           => ['required', 'in:Ketua,Wakil Ketua,Sekretaris,Bendahara,Anggota'],
            'bidang_tugas'      => ['nullable', 'string', 'max:150'],
            'status'            => ['required', 'in:Aktif,Tidak Aktif'],
            'tanggal_bergabung' => ['required', 'date'],
        ]);
        $anggota->update($validated);
        return redirect()->route('portal.kategorial.show', $kategorial)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroyAnggota(Kategorial $kategorial, AnggotaKategorial $anggota): RedirectResponse
    {
        $this->ownedOrFail($kategorial);
        abort_if((int) $anggota->kategorial_id !== (int) $kategorial->id, 404);
        $anggota->delete();
        return redirect()->route('portal.kategorial.show', $kategorial)
            ->with('success', 'Anggota berhasil dikeluarkan dari kategorial.');
    }
}
