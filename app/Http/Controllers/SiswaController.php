<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiswaModel;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function showProfile()
    {
        // Ambil data siswa berdasarkan user yang sedang login
        $siswa = SiswaModel::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('siswa.profile', compact('siswa'));
    }

    // Fungsi edit profile
    public function editProfile()
    {
        // Ambil data siswa berdasarkan user yang sedang login
        $siswa = SiswaModel::where('user_id', Auth::id())->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }
        // Kirim data ke view untuk ditampilkan di form edit
        return view('siswa.edit', compact('siswa'));
    }

    // Fungsi untuk update profile
    public function updateProfile(Request $request)
    {
        // Buat pesan validasi kustom dalam bahasa Indonesia
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

        // Ambil data siswa berdasarkan user yang sedang login
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

        // Siapkan data untuk update (kecualikan foto untuk mencegah overwrite (fotonya nambah terus)
        $siswaData = $request->except('foto');

        // Cek apakah email diubah
        $emailChanged = $siswa->user->email != $request->email;
        $oldEmail = $siswa->user->email;
        $newEmail = $request->email;

        // Update nama user
        $siswa->user->update([
            'nama' => $request->nama,
        ]);

        // Jika email berubah, kirim email verifikasi menggunakan VerificationController
        if ($emailChanged) {
            $verificationController = new VerificationController();
            return $verificationController->sendVerificationEmail($siswa->user, $newEmail);
        }

        // Update password jika diisi
        if ($request->password) {
            $siswa->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // Update data di tabel siswa
        $siswa->update($siswaData);

        // Jika ada foto baru, hapus foto lama dan simpan foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto && file_exists(public_path('assets/img/siswa_foto/' . $siswa->foto))) {
                unlink(public_path('assets/img/siswa_foto/' . $siswa->foto));
            }

            // Ambil nama file
            $nama_file = $siswa->user->id . '_' . $request->file('foto')->getClientOriginalName(); // Menggunakan ID user untuk menghindari duplikasi

            // Simpan file ke folder public/assets/img/siswa_foto
            $request->file('foto')->move(public_path('assets/img/siswa_foto'), $nama_file);

            // Simpan HANYA nama file ke database, terpisah dari update data lainnya
            $siswa->foto = $nama_file;
            $siswa->save();
        }

        return redirect()->route('siswa.profile')->with('success', 'Profile berhasil diperbarui.');
    }
}
