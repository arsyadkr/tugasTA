<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dijalankan SETELAH auth dan must.change.password.
 * Pada titik ini kita sudah yakin:
 *   - User sudah login (auth)
 *   - Password sudah diganti dari default (must.change.password)
 * Tinggal verifikasi role-nya sesuai atau tidak.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            $redirectRoute = $user->role === 'admin'
                ? 'admin.dashboard'
                : 'siswa.dashboard';

            return redirect()->route($redirectRoute)
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
