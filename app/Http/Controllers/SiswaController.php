<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiswaModel;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VerificationController;

class SiswaController extends Controller
{
    // ELOQUENT - Menampilkan profil siswa
    public function showProfile()
    {
        // ELOQUENT - Ambil data siswa berdasarkan user yang sedang login
        $siswa = SiswaModel::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('siswa.profile', compact('siswa'));
    }

    // ELOQUENT - Menampilkan form edit profil siswa
    public function editProfile()
    {
        // ELOQUENT - Ambil data siswa berdasarkan user yang sedang login
        $siswa = SiswaModel::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('siswa.edit', compact('siswa'));
    }

    // ELOQUENT - Memperbarui profil siswa
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

            'nisn.required' => 'NISN wajib diisi',
            'nisn.numeric' => 'NISN hanya boleh berisi angka',
            'nisn.digits' => 'NISN harus terdiri dari 10 digit',
            'nisn.unique' => 'NISN sudah digunakan',
            'nisn.max' => 'NISN tidak boleh lebih dari :max karakter',

            'kelas.required' => 'Kelas wajib diisi',
            'kelas.string' => 'Kelas harus berupa teks',
            'kelas.max' => 'Kelas tidak boleh lebih dari :max karakter',

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

        // ELOQUENT - Ambil data siswa berdasarkan user yang sedang login
        $siswa = SiswaModel::where('user_id', Auth::id())->first();

        // Validasi input
        $request->validate([
            'nama' => 'required|regex:/^[a-zA-Z\s]+$/|max:80',
            'email' => 'required|email|max:70|unique:users,email,' . $siswa->user->id,
            'nisn' => 'required|numeric|digits:10|unique:siswa,nisn,' . $siswa->id,
            'kelas' => 'required|string|max:6',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_telepon' => 'required|numeric|digits_between:10,13|unique:siswa,no_telepon,' . $siswa->id . '|unique:guru,no_telepon|unique:staff,no_telepon|unique:admin,no_telepon',
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048'
        ], $messages);

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Siapkan data untuk update (kecuali foto dan password)
        $siswaData = $request->except(['foto', 'password', 'password_confirmation']);

        // Cek apakah email atau password diubah
        $emailChanged = $siswa->user->email != $request->email;
        $passwordChanged = $request->filled('password');
        $newEmail = $request->email;

        // ELOQUENT - Update nama user terlebih dahulu
        $siswa->user->update([
            'nama' => $request->nama,
        ]);

        // ELOQUENT - Update password jika diisi (sebelum email verification)
        if ($passwordChanged) {
            $siswa->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // ELOQUENT - Update data di tabel siswa (kecuali foto)
        $siswa->update($siswaData);

        // Upload foto baru jika ada (sebelum email verification)
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto && file_exists(public_path('assets/img/siswa_foto/' . $siswa->foto))) {
                unlink(public_path('assets/img/siswa_foto/' . $siswa->foto));
            }

            // Simpan foto baru
            $nama_file = $siswa->user->id . '_' . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move(public_path('assets/img/siswa_foto'), $nama_file);

            // ELOQUENT - Update foto di database
            $siswa->update(['foto' => $nama_file]);
        }

        // Jika email berubah, kirim email verifikasi dan logout
        if ($emailChanged) {
            $verificationController = new VerificationController();
            return $verificationController->sendVerificationEmail($siswa->user, $newEmail);
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

        return redirect()->route('siswa.profile')->with('success', 'Profile berhasil diperbarui.');
    }
}
