<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ini TIDAK didaftarkan ke web group secara global.
 * Dipasang eksplisit hanya di route group yang butuh:
 *
 *   Route::middleware(['auth', 'must.change.password', 'role:admin'])
 *
 * Urutan middleware yang benar (sesuai rekomendasi arsitektur):
 *   1. auth               → pastikan sudah login
 *   2. must.change.password → paksa ganti password dulu jika flagnya true
 *   3. role               → baru cek role setelah password aman
 *
 * Kenapa urutan ini lebih baik?
 *   User yang belum ganti password tidak perlu sampai ke cek role.
 *   Redirect terjadi lebih awal, flow lebih clean, tidak ada state ambigu.
 */
class MustChangePassword
{
    // Named route yang dikecualikan dari pengecekan ini
    protected array $except = [
        'password.change.show',
        'password.change.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        if (Auth::user()->must_change_password && ! $this->isExcepted($request)) {
            return redirect()->route('password.change.show')
                ->with('warning', 'Demi keamanan, silakan ganti password default Anda terlebih dahulu.');
        }

        return $next($request);
    }

    private function isExcepted(Request $request): bool
    {
        foreach ($this->except as $routeName) {
            if ($request->routeIs($routeName)) {
                return true;
            }
        }

        return false;
    }
}
