<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // Blokir akun pending — belum disetujui sekretariat
        if ($user->isPending()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('account.pending');
        }

        // Blokir akun rejected
        if ($user->isRejected()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Pendaftaran Anda telah ditolak. Silakan hubungi sekretariat paroki untuk informasi lebih lanjut.']);
        }

        return redirect()->to($this->redirectTo($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Tentukan halaman redirect setelah login berdasarkan role yang dimiliki user.
     *
     * Prioritas untuk dedicated dashboard: sekretariat > pastor > dewan_pastoral
     * Semua role portal (umat / ketua_kub / ketua_kategorial) → 1 dashboard bersama
     */
    private function redirectTo(User $user): string
    {
        $user->loadMissing('roles');

        // Dedicated dashboards
        if ($user->isSekretariat())   return route('sekretariat.dashboard');
        if ($user->isPastor())        return route('pastor.dashboard');
        if ($user->isDewanPastoral()) return route('dewan_pastoral.dashboard');

        // Semua role portal → 1 dashboard bersama
        if ($user->isUmat()) return route('portal.dashboard');

        abort(403, 'Akun ini belum memiliki role yang valid. Hubungi sekretariat.');
    }
}