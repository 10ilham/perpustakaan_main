<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GuruModel;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VerificationController;

class GuruController extends Controller
{
    // ELOQUENT - Menampilkan profil guru
    public function showProfile()
    {
        // ELOQUENT - Ambil data guru berdasarkan user yang sedang login
        $guru = GuruModel::where('user_id', Auth::id())->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        return view('guru.profile', compact('guru'));
    }

    // ELOQUENT - Menampilkan form edit profil guru
    public function editProfile()
    {
        // ELOQUENT - Ambil data guru berdasarkan user yang sedang login
        $guru = GuruModel::where('user_id', Auth::id())->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        return view('guru.edit', compact('guru'));
    }

    // ELOQUENT - Memperbarui profil guru
    public function updateProfile(Request $request)
    {
        // Pesan validasi kustom dalam bahasa Indonesia
        $messages = [
            'nama.required' => 'Nama lengkap wajib diisi',
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi',
            'nama.max' => 'Nama tidak boleh lebih dari :max karakter',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email tidak boleh lebih dari :max karakter',
            'email.unique' => 'Email sudah digunakan',

            'nip.required' => 'NIP wajib diisi',
            'nip.numeric' => 'NIP harus berupa angka',
            'nip.digits' => 'NIP harus terdiri dari 18 digit',
            'nip.unique' => 'NIP sudah digunakan',

            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi',
            'mata_pelajaran.string' => 'Mata pelajaran harus berupa teks',
            'mata_pelajaran.max' => 'Mata pelajaran tidak boleh lebih dari :max karakter',

            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',

            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',

            'no_telepon.required' => 'Nomor telepon wajib diisi',
            'no_telepon.numeric' => 'Nomor telepon hanya boleh berisi angka',
            'no_telepon.digits_between' => 'Nomor telepon harus terdiri dari 10 hingga 13 digit',
            'no_telepon.unique' => 'Nomor telepon sudah digunakan',

            'password.min' => 'Password minimal :min karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',

            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'foto.max' => 'Ukuran gambar tidak boleh lebih dari 3MB',
        ];

        // ELOQUENT - Ambil data guru berdasarkan user yang sedang login
        $guru = GuruModel::where('user_id', Auth::id())->first();

        // Validasi input
        $request->validate([
            'nama' => 'required|regex:/^[a-zA-Z\s]+$/|max:80',
            'email' => 'required|email|max:70|unique:users,email,' . $guru->user->id,
            'nip' => 'required|numeric|digits:18|unique:guru,nip,' . $guru->id . '|unique:staff,nip|unique:admin,nip',
            'mata_pelajaran' => 'required|string|max:40',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_telepon' => 'required|numeric|digits_between:10,13|unique:guru,no_telepon,' . $guru->id . '|unique:siswa,no_telepon|unique:admin,no_telepon|unique:staff,no_telepon',
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048'
        ], $messages);

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Siapkan data untuk update
        $guruData = $request->except(['foto', 'password', 'password_confirmation']);

        // Cek apakah email atau password diubah
        $emailChanged = $guru->user->email != $request->email;
        $passwordChanged = $request->filled('password');
        $newEmail = $request->email;

        // ELOQUENT - Update nama user terlebih dahulu
        $guru->user->update([
            'nama' => $request->nama,
        ]);

        // ELOQUENT - Update password jika diisi (sebelum email verification)
        if ($passwordChanged) {
            $guru->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // ELOQUENT - Update data di tabel guru (kecuali foto)
        $guru->update($guruData);

        // Upload foto baru jika ada (sebelum email verification)
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($guru->foto && file_exists(public_path('assets/img/guru_foto/' . $guru->foto))) {
                unlink(public_path('assets/img/guru_foto/' . $guru->foto));
            }

            // Simpan foto baru
            $nama_file = $guru->user->id . '_' . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move(public_path('assets/img/guru_foto'), $nama_file);

            // ELOQUENT - Update foto di database
            $guru->update(['foto' => $nama_file]);
        }

        // Jika email berubah, kirim email verifikasi dan logout
        if ($emailChanged) {
            $verificationController = new VerificationController();
            return $verificationController->sendVerificationEmail($guru->user, $newEmail);
        }

        // Jika password berubah, logout untuk keamanan
        if ($passwordChanged) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with(
                'success',
                'Profile berhasil diperbarui dan password telah diubah. Silakan login kembali dengan password baru.'
            );
        }

        return redirect()->route('guru.profile')->with('success', 'Profile berhasil diperbarui.');
    }
}
