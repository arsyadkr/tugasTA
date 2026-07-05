<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS  = 5;
    private const DECAY_MINUTES = 1;

    // -------------------------------------------------------------------------
    // Show Login Admin
    // -------------------------------------------------------------------------

    public function showLoginAdmin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login-admin');
    }

    // -------------------------------------------------------------------------
    // Show Login Siswa
    // -------------------------------------------------------------------------

    public function showLoginSiswa(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login-siswa');
    }

    // -------------------------------------------------------------------------
    // Handle Login — satu handler untuk keduanya
    // Field yang dikirim tetap 'login' dan 'password'
    // Deteksi email vs username tetap bekerja untuk kedua form
    // -------------------------------------------------------------------------

    public function login(LoginRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $credentials = [
            $field     => $request->login,
            'password' => $request->password,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request), self::DECAY_MINUTES * 60);

            // Redirect kembali ke halaman login yang tepat berdasarkan field
            // Admin gagal login → kembali ke /login/admin
            // Siswa gagal login → kembali ke /login/siswa
            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function logout(Request $request): RedirectResponse
    {
        $role = Auth::user()?->role;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login yang sesuai dengan role sebelum logout
        return $role === 'admin'
            ? redirect()->route('login.admin')->with('success', 'Anda berhasil keluar.')
            : redirect()->route('login.siswa')->with('success', 'Anda berhasil keluar.');
    }

    // -------------------------------------------------------------------------
    // Change Password
    // -------------------------------------------------------------------------

    public function showChangePassword(): View
    {
        return view('auth.change-password');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'password'             => $request->password,
            'must_change_password' => false,
        ]);

        return $this->redirectByRole()
            ->with('success', 'Password berhasil diubah. Selamat datang!');
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    private function ensureIsNotRateLimited(LoginRequest $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    private function throttleKey(LoginRequest $request): string
    {
        return Str::lower($request->login) . '|' . $request->ip();
    }

    private function redirectByRole(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return match ($user->role) {
            'admin'   => redirect()->intended(route('admin.dashboard')),
            'student' => redirect()->intended(route('siswa.dashboard')),
            default   => redirect()->route('login.siswa'),
        };
    }
}
