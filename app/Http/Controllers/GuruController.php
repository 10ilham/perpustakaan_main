<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GuruModel;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    public function showProfile()
    {
        // Ambil data guru berdasarkan user yang sedang login
        $guru = GuruModel::where('user_id', Auth::id())->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        return view('guru.profile', compact('guru'));
    }

    // Fungsi edit profile
    public function editProfile()
    {
        // Ambil data guru berdasarkan user yang sedang login
        $guru = GuruModel::where('user_id', Auth::id())->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }
        // Kirim data ke view untuk ditampilkan di form edit
        return view('guru.edit', compact('guru'));
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

        // Ambil data guru berdasarkan user yang sedang login
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

        // Siapkan data untuk update (kecualikan foto untuk mencegah overwrite (fotonya nambah terus)
        $guruData = $request->except('foto');

        // Cek apakah email diubah
        $emailChanged = $guru->user->email != $request->email;
        $oldEmail = $guru->user->email;
        $newEmail = $request->email;

        // Update nama user
        $guru->user->update([
            'nama' => $request->nama,
        ]);

        // Jika email berubah, kirim email verifikasi menggunakan VerificationController
        if ($emailChanged) {
            $verificationController = new VerificationController();
            return $verificationController->sendVerificationEmail($guru->user, $newEmail);
        }

        // update password jika diisi
        if ($request->password) {
            $guru->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // Update data di tabel guru
        $guru->update($guruData);

        // Jika ada file foto yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($guru->foto && file_exists(public_path('assets/img/guru_foto/' . $guru->foto))) {
                unlink(public_path('assets/img/guru_foto/' . $guru->foto));
            }

            // Ambil nama file
            $nama_file = $guru->user->id . '_' . $request->file('foto')->getClientOriginalName(); // Menggunakan ID user untuk menghindari duplikasi

            // Simpan file ke folder public/assets/img/guru_foto
            $request->file('foto')->move(public_path('assets/img/guru_foto'), $nama_file);

            // Simpan HANYA nama file ke database, terpisah dari update data lainnya
            $guru->foto = $nama_file;
            $guru->save();
        }

        return redirect()->route('guru.profile')->with('success', 'Profile berhasil diperbarui.');
    }
}
