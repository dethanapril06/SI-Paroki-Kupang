<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Kub;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Ambil KUB milik ketua yang sedang login.
     */
    private function getMyKub(): Kub
    {
        return Kub::where('ketua_umat_id', Auth::user()->umat_id)->firstOrFail();
    }

    /**
     * Query akun pendaftar yang berada di KUB milik ketua.
     */
    private function pendaftarQuery(Kub $myKub)
    {
        return User::with(['umat.keluarga.kub.wilayah', 'roles'])
            ->whereNotNull('umat_id')
            ->whereHas('umat.keluarga', function ($query) use ($myKub) {
                $query->where('kub_id', $myKub->id);
            });
    }

    /**
     * Pastikan akun pendaftar berada di KUB milik ketua yang sedang login.
     */
    private function authorizePendaftar(User $user, Kub $myKub): void
    {
        $user->loadMissing('umat.keluarga');

        if ((int) $user->umat?->keluarga?->kub_id !== (int) $myKub->id) {
            abort(403, 'Anda tidak memiliki akses ke pendaftaran ini.');
        }
    }

    /**
     * Daftar pendaftaran mandiri umat sesuai KUB ketua.
     */
    public function index(Request $request): View
    {
        $myKub = $this->getMyKub();
        $status = $request->get('status', 'pending');

        $pendaftar = $this->pendaftarQuery($myKub)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => (clone $this->pendaftarQuery($myKub))->where('status', 'pending')->count(),
            'active'   => (clone $this->pendaftarQuery($myKub))->where('status', 'active')->count(),
            'rejected' => (clone $this->pendaftarQuery($myKub))->where('status', 'rejected')->count(),
        ];

        return view('portal.pendaftaran.index', compact('pendaftar', 'status', 'counts', 'myKub'));
    }

    /**
     * Setujui pendaftaran umat dalam KUB ketua.
     */
    public function approve(User $user): RedirectResponse
    {
        $myKub = $this->getMyKub();
        $this->authorizePendaftar($user, $myKub);

        if (!$user->isPending()) {
            return back()->with('error', 'Akun ini tidak dalam status pending.');
        }

        DB::transaction(function () use ($user) {
            $user->update(['status' => 'active']);
            $user->umat?->update(['status_keaktifan' => 'aktif']);
        });

        return back()->with('success', "Pendaftaran atas nama {$user->name} telah disetujui. Akun dan data umat kini aktif.");
    }

    /**
     * Tolak pendaftaran umat dalam KUB ketua.
     */
    public function reject(User $user): RedirectResponse
    {
        $myKub = $this->getMyKub();
        $this->authorizePendaftar($user, $myKub);

        if (!$user->isPending()) {
            return back()->with('error', 'Akun ini tidak dalam status pending.');
        }

        DB::transaction(function () use ($user) {
            $user->update(['status' => 'rejected']);
            $user->umat?->update(['status_keaktifan' => 'non-aktif']);
        });

        return back()->with('success', "Pendaftaran atas nama {$user->name} telah ditolak.");
    }
}
