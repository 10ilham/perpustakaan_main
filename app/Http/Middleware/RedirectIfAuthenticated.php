<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Menangani permintaan masuk.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Memeriksa apakah pengguna sudah terotentikasi
        if (Auth::check()) {
            // Mengarahkan ke dashboard yang sesuai berdasarkan level pengguna
            $userLevel = Auth::user()->level;

            switch ($userLevel) {
                case 'admin':
                    return redirect('/admin/dashboard')->with('info', 'Anda sudah login sebagai admin.');
                case 'siswa':
                case 'guru':
                case 'staff':
                    return redirect('/anggota/dashboard')->with('info', 'Anda sudah login sebagai ' . $userLevel . '.');
                default:
                    // Logout untuk pengguna dengan level tidak valid
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect('/')->with('error', 'Level pengguna tidak valid.');
            }
        }

        return $next($request);
    }
}
