<?php

namespace App\Http\Controllers\Sekretariat;
use App\Http\Controllers\Controller;
use App\Models\Mutasi;
use App\Models\MutasiAgama;
use App\Models\Umat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiAgamaController extends Controller
{
    /**
     * Daftar mutasi agama.
     */
    public function index(): View
    {
        $mutasiAgamaList = MutasiAgama::with(['mutasi', 'umat'])
            ->join('mutasi', 'mutasi.id', '=', 'mutasi_agama.mutasi_id')
            ->orderByDesc('mutasi.tanggal')
            ->select('mutasi_agama.*')
            ->paginate(20);

        return view('sekretariat.mutasi.agama.index', compact('mutasiAgamaList'));
    }

    /**
     * Form tambah mutasi agama.
     */
    public function create(): View
    {
        $umatList = Umat::aktif()
            ->where('status_almarhum', false)
            ->orderBy('nama')
            ->get();

        return view('sekretariat.mutasi.agama.create', compact('umatList'));
    }

    /**
     * Simpan mutasi agama baru (dibuat langsung oleh sekretariat → langsung disetujui).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'umat_id'      => ['required', 'exists:umat,id'],
            'agama_tujuan' => ['required', 'in:protestan,hindu,budha,khonghucu,islam'],
            'tanggal'      => ['required', 'date'],
            'keterangan'   => ['nullable', 'string'],
        ]);

        // Buat parent mutasi — langsung disetujui karena dibuat oleh sekretariat
        $mutasi = Mutasi::create([
            'jenis'                 => 'agama',
            'tanggal'               => $validated['tanggal'],
            'keterangan'            => $validated['keterangan'] ?? null,
            'status'                => 'disetujui',
            'pemohon_umat_id'       => $validated['umat_id'],
            'diproses_oleh_user_id' => auth()->id(),
            'diproses_pada'         => now(),
        ]);

        // Buat child mutasi_agama
        // agama_asal tidak perlu diinput — default 'katolik' sudah di DB
        $mutasiAgama = MutasiAgama::create([
            'mutasi_id'    => $mutasi->id,
            'umat_id'      => $validated['umat_id'],
            'agama_tujuan' => $validated['agama_tujuan'],
        ]);

        // Langsung eksekusi karena dibuat oleh sekretariat
        $this->executeApproval($mutasiAgama);

        return redirect()->route('sekretariat.mutasi.agama.index')
            ->with('success', 'Mutasi agama berhasil dicatat.');
    }

    /**
     * Eksekusi perubahan data setelah mutasi agama disetujui.
     * Dipanggil dari store() (sekretariat langsung) dan approve() di MutasiController.
     */
    public function executeApproval(MutasiAgama $mutasiAgama): void
    {
        // Mutasi agama: non-aktifkan + soft delete umat (keluar dari komunitas Katolik)
        $umat = Umat::find($mutasiAgama->umat_id);
        if ($umat) {
            $umat->update(['status_keaktifan' => 'non-aktif']);

            // Hapus akun login yang terhubung dengan umat ini
            if ($umat->user) {
                $umat->user->delete();
            }

            $umat->delete();
        }
    }

    /**
     * Detail satu mutasi agama.
     */
    public function show(MutasiAgama $mutasiAgama): View
    {
        $mutasiAgama->load([
            'mutasi.pemohon',
            'mutasi.diprosesOleh',
            'umat',
        ]);

        return view('sekretariat.mutasi.agama.show', compact('mutasiAgama'));
    }

    /**
     * Form edit mutasi agama.
     */
    public function edit(MutasiAgama $mutasiAgama): View
    {
        $mutasiAgama->load('mutasi');
        $umatList = Umat::aktif()->orderBy('nama')->get();

        return view('sekretariat.mutasi.agama.edit', compact('mutasiAgama', 'umatList'));
    }

    /**
     * Update mutasi agama.
     */
    public function update(Request $request, MutasiAgama $mutasiAgama): RedirectResponse
    {
        $validated = $request->validate([
            'umat_id'      => ['required', 'exists:umat,id'],
            'agama_tujuan' => ['required', 'in:protestan,hindu,budha,khonghucu,islam'],
            'tanggal'      => ['required', 'date'],
            'keterangan'   => ['nullable', 'string'],
        ]);

        // Update parent mutasi
        $mutasiAgama->mutasi->update([
            'tanggal'    => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Update child
        $mutasiAgama->update([
            'umat_id'      => $validated['umat_id'],
            'agama_tujuan' => $validated['agama_tujuan'],
        ]);

        return redirect()->route('sekretariat.mutasi.agama.index')
            ->with('success', 'Mutasi agama berhasil diperbarui.');
    }

    /**
     * Hapus mutasi agama — restore umat & akun login jika sebelumnya sudah di-soft delete.
     */
    public function destroy(MutasiAgama $mutasiAgama): RedirectResponse
    {
        $umat = Umat::withTrashed()->find($mutasiAgama->umat_id);

        if ($umat?->trashed()) {
            // Restore akun login yang ikut dihapus saat executeApproval
            if ($umat->user()->withTrashed()->exists()) {
                $umat->user()->withTrashed()->first()->restore();
            }

            $umat->restore();
            $umat->update(['status_keaktifan' => 'aktif']);
        }

        $mutasiAgama->mutasi->delete();

        return redirect()->route('sekretariat.mutasi.agama.index')
            ->with('success', 'Mutasi agama berhasil dihapus.');
    }
}
