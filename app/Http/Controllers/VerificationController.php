<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /**
     * Kirim email verifikasi ketika pengguna mengubah email.
     *
     * @param  \App\Models\User  $user
     * @param  string  $newEmail
     * @return \Illuminate\Http\Response
     */
    public function sendVerificationEmail(User $user, $newEmail)
    {
        // Membuat token verifikasi acak sepanjang 64 karakter
        // Token panjang (64 karakter) digunakan agar sangat sulit ditebak oleh orang lain dan unik
        // Contoh token: "a7f9d2e3b1c8..."
        $verificationToken = Str::random(64);

        // Simpan token verifikasi dan email baru dalam format "token|email baru"
        // Format ini memungkinkan sistem menyimpan email baru yang akan diverifikasi
        // bersama dengan token, sehingga saat verifikasi, sistem tahu email mana yang diaktifkan
        $user->update([
            'email_verification_token' => $verificationToken . '|' . $newEmail,
        ]);

        // Kirim email verifikasi
        $verificationUrl = route('email.verify', ['token' => $verificationToken]);

        try {
            Mail::to($newEmail)->send(new EmailVerificationMail([
                'nama' => $user->nama, //dikirim ke view (verify_email.blade.php) untuk menampilkan nama pengguna di email
                'verificationUrl' => $verificationUrl,
            ]));

            // Logout pengguna
            Auth::logout();

            // Invalidasi session
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login')->with('info', 'Profile berhasil diperbarui. Silakan verifikasi email baru Anda melalui link yang telah dikirim ke ' . $newEmail);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email verifikasi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Verifikasi email pengguna.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    /**
     * Memaksa logout semua sesi aktif untuk pengguna tertentu
     * Digunakan saat email diubah untuk memastikan keamanan
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    private function logoutAllSessions(User $user)
    {
        // Jika pengguna masih login di beberapa perangkat atau browser
        // maka kita perlu invalidasi semua sesi aktif
        // Cara ini akan memaksa pengguna untuk login ulang dengan email baru

        // Logout pengguna yang sedang aktif
        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        // Untuk Laravel dengan database session driver
        // Hapus semua sesi yang terkait dengan pengguna ini dari database
        // Ini akan memaksa logout di semua perangkat/browser
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();
    }

    public function verify($token)
    {

        // Ambil user berdasarkan token verifikasi
        // Menggunakan LIKE untuk menghindari masalah dengan token yang lebih panjang
        // Misalnya, jika token yang disimpan adalah "abc123|email@example.com" maka kita hanya perlu mencocokkan "abc123" karena yang dicari "LIKE" adalah tokennya "%" sebagai pemisah karakter
        $user = User::where('email_verification_token', 'LIKE', $token . '%')->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token verifikasi tidak valid atau sudah kadaluarsa!');
        }

        // Memisahkan token dan email baru
        // Format yang disimpan adalah: "token|email baru"
        // (contoh: "abc123def456...|user@example.com")
        $tokenParts = explode('|', $user->email_verification_token);

        // Memeriksa apakah format token sesuai harapan
        // Token harus terdiri dari 2 bagian (token dan email)
        // Jika jumlah bagiannya tidak sama dengan 2, berarti format token tidak valid
        // !== 2 artinya "tidak sama dengan 2"
        if (count($tokenParts) !== 2) {
            return redirect()->route('login')->with('error', 'Format token verifikasi tidak valid!');
        }

        // Mengambil email baru dari bagian kedua token
        // $tokenParts[0] berisi token acak (bagian pertama)
        // $tokenParts[1] berisi email baru yang akan diverifikasi (bagian kedua)
        // Menggunakan indeks 1 karena dalam pemrograman penghitungan indeks dimulai dari 0
        $newEmail = $tokenParts[1];

        // Update user email dan status verifikasi
        $user->update([
            'email' => $newEmail,
            'email_verified_at' => now(),
            'email_verification_token' => null, // Menghapus token verifikasi setelah berhasil diverifikasi
            // 'email_verification_token' => $user->email_verification_token, // Mempertahankan token verifikasi yang ada didatabase (agar tidak hilang (null) setelah verifikasi)
        ]);

        // Memaksa logout semua sesi pengguna ini untuk keamanan
        // Ini berguna jika pengguna masih memiliki sesi aktif dengan email lama di perangkat lain
        $this->logoutAllSessions($user);

        return redirect()->route('login')->with('success', 'Email Anda berhasil diverifikasi! Silakan login kembali dengan email baru Anda.');
    }
}
