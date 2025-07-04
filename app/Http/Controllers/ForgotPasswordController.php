<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class ForgotPasswordController extends Controller
{
    /**
     * Memproses permintaan reset password
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validasi input email
        $request->validate(
            [
                'email' => 'required|email',
            ]
        );

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Email tidak terdaftar dalam sistem!');
        }

        // Generate token unik
        $token = Str::random(64);

        // Simpan token di database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Buat URL reset password
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Kirim email
        try {
            Mail::to($user->email)->send(new ResetPasswordMail([
                'email' => $user->email,
                'token' => $token,
                'name' => $user->nama,
                'resetUrl' => $resetUrl
            ]));

            return redirect()->route('login')->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek email Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email reset password: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form reset password dengan token
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Memproses reset password
     */
    public function reset(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|max:70|exists:users',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.max' => 'Email tidak boleh lebih dari :max karakter',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Cek token valid
        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Token tidak valid atau sudah kadaluarsa!');
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // Hapus token setelah berhasil reset password
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }
}
