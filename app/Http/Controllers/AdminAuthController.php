<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan formulir login staf admin (FR-33)
     */
    public function login()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('pages.admin.login');
    }

    /**
     * Autentikasi login staf admin / piket (FR-33 & NFR-08)
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $username = trim($request->input('username'));
        $throttleKey = Str::transliterate(Str::lower($username) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            LogAktivitas::catat(null, 'login_gagal', 'Autentikasi Staf', "Percobaan login berulang terkunci untuk username: {$username}");

            return back()->withInput($request->only('username'))->withErrors([
                'auth' => "Terlalu banyak percobaan login gagal. Akses dikunci sementara selama {$minutes} menit.",
            ]);
        }

        $credentials = [
            'username' => $username,
            'password' => $request->input('password'),
        ];

        if (!Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 300);

            LogAktivitas::catat(null, 'login_gagal', 'Autentikasi Staf', "Login gagal untuk username: {$username}");

            return back()->withInput($request->only('username'))->withErrors([
                'auth' => 'Username atau password yang Anda masukkan salah.',
            ]);
        }

        $user = Auth::guard('web')->user();

        // Validasi status aktif akun staf
        if (!$user->is_aktif) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            LogAktivitas::catat($user, 'login_ditolak', 'Autentikasi Staf', 'Percobaan login dari akun staf yang berstatus nonaktif.');

            return back()->withInput($request->only('username'))->withErrors([
                'auth' => 'Akun Anda dinonaktifkan oleh Administrator. Silakan hubungi Super Admin.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        LogAktivitas::catat($user, 'login', 'Autentikasi Staf', "Login berhasil sebagai {$user->role}.");

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Logout staf admin
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            LogAktivitas::catat($user, 'logout', 'Autentikasi Staf', 'Staf keluar dari sistem.');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('sukses', 'Anda telah berhasil keluar dari Panel Administrasi.');
    }
}
