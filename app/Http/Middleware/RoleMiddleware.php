<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Penggunaan di routes:
     *   ->middleware('role:sekretariat')
     *   ->middleware('role:ketua_kub,ketua_kategorial')   // boleh salah satu
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth()->check()) {
            return redirect()->route('login');
        }

        // Load roles jika belum (hindari N+1)
        $user      = auth()->user()->loadMissing('roles');
        $userRoles = $user->roles->pluck('name')->toArray();

        if (empty(array_intersect($roles, $userRoles))) {
            abort(403);
        }

        return $next($request);
    }
}
