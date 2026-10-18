<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses login Admin dan Super Admin.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Memeriksa username dan password.
        if (! Auth::attempt(
            [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            ],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $user = Auth::user();

        // Hanya akun aktif yang boleh login.
        if (! $user->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => 'Akun kamu tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        // Admin dan Super Admin diperbolehkan masuk ke web.
        if (! $user->isAdmin()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => 'Halaman ini khusus untuk Admin dan Super Admin. Petugas silakan login melalui aplikasi mobile.',
            ]);
        }

        // Memperbarui session setelah login berhasil.
        $request->session()->regenerate();

        // Mengarahkan pengguna ke dashboard admin.
        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
