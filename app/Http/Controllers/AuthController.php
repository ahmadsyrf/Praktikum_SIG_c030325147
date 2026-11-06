<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Membuat identitas pembatas berdasarkan email dan IP
        $key = Str::lower($request->input('email'))
            . '|' . $request->ip();

        // Memeriksa batas percobaan login
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->status(429);
        }

        // Memeriksa email dan password
        if (Auth::attempt($credentials)) {

            // Memeriksa status aktif pengguna
            if (!Auth::user()->isActive()) {

                // Mengeluarkan pengguna yang berstatus nonaktif
                Auth::logout();

                // Menambah hitungan percobaan login
                RateLimiter::hit($key, 60);

                return back()->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            // Menghapus hitungan percobaan jika login berhasil
            RateLimiter::clear($key);

            // Memperbarui session
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        // Menambah hitungan percobaan jika login gagal
        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->onlyInput('email');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
