<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SiswaAuthController extends Controller
{
    /**
     * Tampilkan formulir login portal calon siswa (FR-06)
     */
    public function login()
    {
        if (Auth::guard('siswa')->check()) {
            return redirect()->route('siswa.dashboard');
        }

        return view('pages.siswa.login');
    }

    /**
     * Proses autentikasi login calon siswa (FR-06 & NFR-08)
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'nomor_pendaftaran' => ['required', 'string'],
            'password' => ['required_without:tanggal_lahir', 'nullable', 'string'],
            'tanggal_lahir' => ['required_without:password', 'nullable', 'string'],
        ], [
            'nomor_pendaftaran.required' => 'Nomor Pendaftaran wajib diisi.',
            'password.required_without' => 'Kata Sandi atau Tanggal Lahir wajib diisi.',
            'tanggal_lahir.required_without' => 'Kata Sandi atau Tanggal Lahir wajib diisi.',
        ]);

        $nomorPendaftaran = trim($request->input('nomor_pendaftaran'));
        $rawCredential = trim((string) ($request->input('password') ?? $request->input('tanggal_lahir')));

        // Kunci pembatasan percobaan: 5 kali gagal -> kunci 5 menit (NFR-08)
        $throttleKey = Str::transliterate(Str::lower($nomorPendaftaran) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            return back()->withInput($request->only('nomor_pendaftaran'))->withErrors([
                'auth' => "Terlalu banyak percobaan login yang gagal. Akun Anda dikunci sementara demi keamanan selama {$minutes} menit.",
            ]);
        }

        // Coba autentikasi menggunakan guard 'siswa' dengan nilai mentah (password kustom / DDMMYYYY)
        $attemptSuccess = Auth::guard('siswa')->attempt([
            'nomor_pendaftaran' => $nomorPendaftaran,
            'password' => $rawCredential,
        ], $request->boolean('remember'));

        // Jika belum berhasil, coba dengan normalisasi tanggal lahir (misal YYYY-MM-DD atau input date picker)
        if (!$attemptSuccess) {
            $normalizedPassword = $this->normalisasiTanggalLahir($rawCredential);
            if ($normalizedPassword !== $rawCredential) {
                $attemptSuccess = Auth::guard('siswa')->attempt([
                    'nomor_pendaftaran' => $nomorPendaftaran,
                    'password' => $normalizedPassword,
                ], $request->boolean('remember'));
            }
        }

        if (!$attemptSuccess) {
            RateLimiter::hit($throttleKey, 300); // 300 detik = 5 menit kunci

            // Pesan kesalahan umum tanpa membocorkan eksistensi nomor pendaftaran (FR-06)
            return back()->withInput($request->only('nomor_pendaftaran'))->withErrors([
                'auth' => 'Nomor Pendaftaran atau Kata Sandi / Tanggal Lahir tidak cocok dengan data sistem PPDB.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('siswa.dashboard'));
    }

    /**
     * Logout dari Portal Siswa
     */
    public function logout(Request $request)
    {
        Auth::guard('siswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login')->with('sukses', 'Anda telah berhasil keluar dari Portal Calon Siswa.');
    }

    /**
     * Helper normalisasi berbagai input tanggal lahir ke string 'DDMMYYYY'
     */
    private function normalisasiTanggalLahir(string $input): string
    {
        $input = trim($input);

        // Jika hanya angka 8 digit, periksa apakah YYYYMMDD atau DDMMYYYY
        $digitsOnly = preg_replace('/[^0-9]/', '', $input);
        if (strlen($digitsOnly) === 8) {
            // Jika diawali 19 atau 20, kemungkinan besar format YYYYMMDD (misal dari input browser)
            if (str_starts_with($digitsOnly, '19') || str_starts_with($digitsOnly, '20')) {
                try {
                    return Carbon::createFromFormat('Ymd', $digitsOnly)->format('dmY');
                } catch (\Exception $e) {
                    // fallback
                }
            }
            return $digitsOnly;
        }

        // Coba parsing tanggal standar (YYYY-MM-DD dari <input type="date">)
        try {
            return Carbon::parse($input)->format('dmY');
        } catch (\Exception $e) {
            return $input;
        }
    }
}
