<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LevelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cek apakah pengguna memiliki level yang valid
            if (!in_array($user->level, ['admin', 'siswa', 'guru', 'staff'])) {
                return $this->logoutAndRedirect($request, 'Level pengguna tidak valid.');
            }

            // Cek apakah pengguna memiliki akses ke halaman yang diminta
            if ($request->is('admin/*') && $user->level !== 'admin') {
                return $this->redirectToUserDashboard($user->level, 'Anda tidak memiliki akses ke halaman admin.');
            }
            if ($request->is('siswa/*') && $user->level !== 'siswa') {
                $message = $this->getSpecificMessage($request, 'siswa');
                return $this->redirectToUserDashboard($user->level, $message);
            }
            if ($request->is('guru/*') && $user->level !== 'guru') {
                $message = $this->getSpecificMessage($request, 'guru');
                return $this->redirectToUserDashboard($user->level, $message);
            }
            if ($request->is('staff/*') && $user->level !== 'staff') {
                $message = $this->getSpecificMessage($request, 'staff');
                return $this->redirectToUserDashboard($user->level, $message);
            }

            // Cek akses khusus untuk anggota dashboard - hanya untuk siswa, guru, dan staff
            if ($request->is('anggota/dashboard') && $user->level === 'admin') {
                return redirect('/admin/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman anggota. Silakan gunakan dashboard admin.');
            }

            // Cek akses khusus untuk anggota chart data - hanya untuk siswa, guru, dan staff
            if ($request->is('anggota/chart-data') && $user->level === 'admin') {
                return redirect('/admin/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman anggota.');
            }

            // Mencegah admin mengakses fitur download QR Code
            if ($request->is('buku/*/qrcode-download') && $user->level === 'admin') {
                return redirect('/admin/dashboard')->with('error', 'Fitur download QR Code hanya tersedia untuk anggota perpustakaan.');
            }

            // Mencegah admin mengakses fitur peminjaman buku
            if ($request->is('peminjaman/form/*') && $user->level === 'admin') {
                return redirect('/admin/dashboard')->with('error', 'Fitur peminjaman buku hanya tersedia untuk anggota perpustakaan.');
            }

            // Mencegah anggota mengakses fitur pengelolaan anggota (siswa, guru, staff)
            if ($request->is('anggota') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur pengelolaan anggota hanya tersedia untuk admin.');
            }
            // Mencegah anggota mengakses detail anggota lain
            if ($request->is('anggota/detail/*') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur melihat detail anggota hanya tersedia untuk admin.');
            }
            // Mencegah anggota mengakses form tambah anggota
            if ($request->is('anggota/tambah') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur menambah anggota hanya tersedia untuk admin.');
            }
            // Mencegah anggota mengakses form edit anggota
            if ($request->is('anggota/edit/*') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur mengedit anggota hanya tersedia untuk admin.');
            }

            // Mencegah anggota mengakses fitur pengelolaan buku (siswa, guru, staff)
            if ($request->is('buku/tambah') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur tambah buku hanya tersedia untuk admin.');
            }
            if ($request->is('buku/edit/*') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur edit buku hanya tersedia untuk admin.');
            }

            // Mencegah anggota mengakses fitur pengelolaan kategori (siswa, guru, staff)
            if ($request->is('kategori/tambah') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur tambah kategori hanya tersedia untuk admin.');
            }
            if ($request->is('kategori/edit/*') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur edit kategori hanya tersedia untuk admin.');
            }

            // Mencegah anggota mengakses fitur laporan
            if ($request->is('laporan') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Fitur ini hanya tersedia untuk admin.');
            }
            // Mencegah anggota mengakses data chart laporan
            if ($request->is('laporan/chart-data') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Data ini hanya tersedia untuk admin.');
            }
            // Mencegah anggota mengakses data pie chart laporan
            if ($request->is('laporan/pie-chart-data') && $user->level !== 'admin') {
                return redirect('/anggota/dashboard')->with('error', 'Data ini hanya tersedia untuk admin.');
            }

            // Jika pengguna memiliki akses yang sesuai, lanjutkan ke permintaan berikutnya
            return $next($request);
        }
        return redirect('/login')->with('error', 'Anda harus login terlebih dahulu.');
    }

    /**
     * Redirect user to their appropriate dashboard
     */
    private function redirectToUserDashboard($userLevel, $message)
    {
        $redirectRoute = '/';

        switch ($userLevel) {
            case 'admin':
                $redirectRoute = '/admin/dashboard';
                break;
            case 'siswa':
            case 'guru':
            case 'staff':
                $redirectRoute = '/anggota/dashboard';
                break;
        }

        return redirect($redirectRoute)->with('error', $message);
    }

    /**
     * Logout user and redirect to home with proper session cleanup
     */
    private function logoutAndRedirect($request, $message)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('error', $message);
    }

    /**
     * Get specific message based on request type and target level
     */
    private function getSpecificMessage($request, $targetLevel)
    {
        $method = $request->method();
        $path = $request->path();

        // Handle profile-related requests
        if (str_contains($path, 'profile')) {
            if (str_contains($path, 'edit')) {
                return "Anda tidak memiliki akses untuk mengedit profil {$targetLevel}.";
            } elseif (str_contains($path, 'update') || $method === 'POST') {
                return "Anda tidak memiliki akses untuk memperbarui profil {$targetLevel}.";
            } else {
                return "Anda tidak memiliki akses untuk melihat profil {$targetLevel}.";
            }
        }

        // Default message for other requests
        return "Anda tidak memiliki akses ke halaman {$targetLevel}.";
    }
}
