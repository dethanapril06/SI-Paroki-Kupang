<?php

namespace App\Http\Controllers\Sekretariat;
use App\Http\Controllers\Controller;
use App\Models\Mutasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiController extends Controller
{
    /**
     * Daftar semua mutasi (semua jenis), dengan filter status opsional.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'semua');

        $query = Mutasi::with([
                'mutasiUmat.umat',
                'mutasiKeluarga.keluarga',
                'mutasiAgama.umat',
                'pemohon',
                'diprosesOleh',
            ])
            ->latest();

        if (in_array($status, ['pending', 'disetujui', 'ditolak'])) {
            $query->where('status', $status);
        }

        $mutasiList     = $query->paginate(20)->withQueryString();
        $pendingCount   = Mutasi::pending()->count();

        return view('sekretariat.mutasi.index', compact('mutasiList', 'status', 'pendingCount'));
    }

    /**
     * Setujui request mutasi dari umat dan eksekusi perubahan data.
     */
    public function approve(Request $request, Mutasi $mutasi): RedirectResponse
    {
        if (!$mutasi->isPending()) {
            return back()->with('error', 'Mutasi ini sudah diproses sebelumnya.');
        }

        // Eksekusi perubahan data sesuai jenis mutasi
        match ($mutasi->jenis) {
            'umat'     => app(MutasiUmatController::class)
                              ->executeApproval(
                                  $mutasi->mutasiUmat,
                                  $mutasi->mutasiUmat->toArray()
                              ),
            'keluarga' => app(MutasiKeluargaController::class)
                              ->executeApproval($mutasi->mutasiKeluarga),
            'agama'    => app(MutasiAgamaController::class)
                              ->executeApproval($mutasi->mutasiAgama),
        };

        $mutasi->update([
            'status'                => 'disetujui',
            'diproses_oleh_user_id' => auth()->id(),
            'catatan_admin'         => $request->input('catatan_admin'),
            'diproses_pada'         => now(),
        ]);

        return back()->with('success', 'Request mutasi berhasil disetujui dan data telah diperbarui.');
    }

    /**
     * Tolak request mutasi dari umat (data tidak berubah).
     */
    public function reject(Request $request, Mutasi $mutasi): RedirectResponse
    {
        $request->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ]);

        if (!$mutasi->isPending()) {
            return back()->with('error', 'Mutasi ini sudah diproses sebelumnya.');
        }

        $mutasi->update([
            'status'                => 'ditolak',
            'diproses_oleh_user_id' => auth()->id(),
            'catatan_admin'         => $request->input('catatan_admin'),
            'diproses_pada'         => now(),
        ]);

        return back()->with('success', 'Request mutasi telah ditolak.');
    }

    /**
     * Hapus mutasi beserta child-nya (cascade by DB).
     */
    public function destroy(Mutasi $mutasi): RedirectResponse
    {
        $mutasi->delete();

        return redirect()->route('sekretariat.mutasi.index')
            ->with('success', 'Data mutasi berhasil dihapus.');
    }
}