<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User; // Pastikan Anda mengimpor model User Anda

class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Jika user sudah login, arahkan mereka ke dashboard yang sesuai
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->roles === 'ADMIN') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }

        return view('auth.login'); // Pastikan view-nya ada di resources/views/auth/login.blade.php
    }

    /**
     * Mendapatkan nama kolom login yang akan digunakan oleh controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username'; // Memberitahu Laravel untuk menggunakan kolom 'username' untuk autentikasi
    }

    /**
     * Proses autentikasi user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Validasi input dari form login
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        // Coba autentikasi user dengan kredensial yang diberikan
        // $request->filled('remember') akan mengecek apakah checkbox "Ingat Saya" dicentang
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Jika autentikasi berhasil, regenerasi ID sesi untuk mencegah serangan fiksasi sesi
            $request->session()->regenerate();

            // Dapatkan objek user yang baru saja berhasil login
            $user = Auth::user();

            // Logika redirect berdasarkan peran (role) user
            // Penting: Pastikan nilai 'ADMIN' cocok persis dengan data di kolom 'role' di database Anda (case-sensitive)
            if ($user->roles === 'ADMIN') {
                // Redirect user admin ke dashboard admin
                return redirect()->intended(route('admin.dashboard'));
            }

            // Redirect user biasa (non-admin) ke dashboard user
            return redirect()->intended(route('user.dashboard'));
        }

        // Jika autentikasi gagal (username atau password salah)
        // Lemparkan pengecualian validasi dengan pesan error yang spesifik
        throw ValidationException::withMessages([
            'username' => __('Username atau password salah.'),
        ]);
    }

    /**
     * Logout user dari aplikasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Logout user yang sedang terautentikasi
        Auth::logout();

        // Invalidate sesi saat ini dan regenerasi token CSRF untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect user ke halaman depan ('/') setelah logout
        // Anda bisa mengubah ini menjadi redirect('/login') jika ingin selalu kembali ke form login
        return redirect('/');
    }
}