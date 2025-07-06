<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /**
     * Kirim email verifikasi ketika pengguna mengubah email
     *
     * @param User $user
     * @param string $newEmail
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendVerificationEmail(User $user, $newEmail)
    {
        // Buat token verifikasi acak 64 karakter - ELOQUENT
        // Contoh token: "a7f9d2e3b1c8..."
        $verificationToken = Str::random(64);

        // Simpan token dan email baru dengan format "token|email" - ELOQUENT
        $user->update([
            'email_verification_token' => $verificationToken . '|' . $newEmail,
        ]);

        // Buat URL verifikasi
        $verificationUrl = route('email.verify', ['token' => $verificationToken]);

        // Kirim email verifikasi
        Mail::to($newEmail)->send(new EmailVerificationMail([
            'nama' => $user->nama,
            'verificationUrl' => $verificationUrl,
        ]));

        // Logout pengguna untuk keamanan
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('info',
            'Profile berhasil diperbarui. Silakan verifikasi email baru Anda melalui link yang telah dikirim ke ' . $newEmail
        );
    }

    /**
     * Verifikasi email pengguna berdasarkan token
     *
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify($token)
    {
        // Cari user berdasarkan token verifikasi - ELOQUENT
        $user = User::where('email_verification_token', 'LIKE', $token . '%')->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token verifikasi tidak valid atau sudah kadaluarsa!');
        }

        // Pisahkan token dan email baru
        $tokenParts = explode('|', $user->email_verification_token);

        // Validasi format token
        if (count($tokenParts) !== 2) {
            return redirect()->route('login')->with('error', 'Format token verifikasi tidak valid!');
        }

        $newEmail = $tokenParts[1];
        $oldEmail = $user->email;

        // Update email dan hapus token verifikasi - ELOQUENT
        $user->update([
            'email' => $newEmail,
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        // Logout semua session yang menggunakan email lama untuk keamanan
        // Cek apakah ada user yang sedang login dengan email lama
        if (Auth::check() && Auth::user()->email === $oldEmail) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            
            return redirect()->route('login')->with('success',
                'Email Anda berhasil diverifikasi dan telah diubah! Silakan login kembali dengan email baru Anda: ' . $newEmail
            );
        }

        return redirect()->route('login')->with('success',
            'Email Anda berhasil diverifikasi! Silakan login dengan email baru Anda: ' . $newEmail
        );
    }
}
