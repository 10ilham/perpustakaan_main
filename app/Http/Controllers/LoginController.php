<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Tampilkan halaman login yang baru
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek apakah email terdaftar (untuk data error sweetalert)
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Email tidak terdaftar (untuk data error sweetalert)
            return redirect()->route('login')
                ->with('error', 'Email tidak terdaftar!')
                ->withInput($request->only('email'));
        }

        // Coba login
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // Redirect berdasarkan level pengguna
            $user = Auth::user();
            if ($user->level === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($user->level === 'siswa') {
                return redirect('/anggota/dashboard');
            } elseif ($user->level === 'guru') {
                return redirect('/anggota/dashboard');
            } elseif ($user->level === 'staff') {
                return redirect('/anggota/dashboard');
            }

            return redirect('/');
        }

        // Jika password salah (email sudah benar tapi auth gagal)
        return redirect()->route('login')
            ->with('error', 'Password yang Anda masukkan salah!')
            ->withInput($request->only('email'));
    }
}
