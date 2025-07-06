<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login pengguna
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek apakah email terdaftar - ELOQUENT
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Email tidak terdaftar!')
                ->withInput($request->only('email'));
        }

        // Cek apakah user sedang dalam proses verifikasi email (ada token)
        if ($user->email_verification_token) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda sedang dalam proses verifikasi email. Silakan periksa email Anda dan klik link verifikasi terlebih dahulu!')
                ->withInput($request->only('email'));
        }

        // Proses login
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // Redirect berdasarkan level pengguna
            $user = Auth::user();

            switch ($user->level) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'siswa':
                case 'guru':
                case 'staff':
                    return redirect('/anggota/dashboard');
                default:
                    return redirect('/');
            }
        }

        // Jika password salah
        return redirect()->route('login')
            ->with('error', 'Password yang Anda masukkan salah!')
            ->withInput($request->only('email'));
    }
}
