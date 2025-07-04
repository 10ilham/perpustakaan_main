<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffModel;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function showProfile()
    {
        // Ambil data staff berdasarkan user yang sedang login
        $staff = StaffModel::where('user_id', Auth::id())->first();

        if (!$staff) {
            return redirect()->back()->with('error', 'Data staff tidak ditemukan.');
        }

        return view('staff.profile', compact('staff'));
    }

    // Fungsi edit profile
    public function editProfile()
    {
        // Ambil data staff berdasarkan user yang sedang login
        $staff = StaffModel::where('user_id', Auth::id())->first();

        if (!$staff) {
            return redirect()->back()->with('error', 'Data staff tidak ditemukan.');
        }
        // Kirim data ke view untuk ditampilkan di form edit
        return view('staff.edit', compact('staff'));
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

            'bagian.required' => 'Bagian wajib diisi',
            'bagian.string' => 'Bagian harus berupa teks',
            'bagian.max' => 'Bagian tidak boleh lebih dari :max karakter',

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

        // Ambil data staff berdasarkan user yang sedang login
        $staff = StaffModel::where('user_id', Auth::id())->first();

        // Validasi input
        $request->validate([
            'nama' => 'required|regex:/^[a-zA-Z\s]+$/|max:80',
            'email' => 'required|email|max:70|unique:users,email,' . $staff->user->id,
            'nip' => 'required|numeric|digits:18|unique:staff,nip,' . $staff->id . '|unique:guru,nip|unique:admin,nip',
            'bagian' => 'required|string|max:30',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_telepon' => 'required|numeric|digits_between:10,13|unique:staff,no_telepon,' . $staff->id . '|unique:siswa,no_telepon|unique:admin,no_telepon|unique:guru,no_telepon',
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048'
        ], $messages);

        if (!$staff) {
            return redirect()->back()->with('error', 'Data staff tidak ditemukan.');
        }

        // Siapkan data untuk update (kecualikan foto untuk mencegah overwrite (fotonya nambah terus)
        $staffData = $request->except('foto');

        // Cek apakah email diubah
        $emailChanged = $staff->user->email != $request->email;
        $oldEmail = $staff->user->email;
        $newEmail = $request->email;

        // Update nama user
        $staff->user->update([
            'nama' => $request->nama,
        ]);

        // Jika email berubah, kirim email verifikasi menggunakan VerificationController
        if ($emailChanged) {
            $verificationController = new VerificationController();
            return $verificationController->sendVerificationEmail($staff->user, $newEmail);
        }

        // update password jika diisi
        if ($request->password) {
            $staff->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // Update data di tabel staff
        $staff->update($staffData);

        // Jika ada file foto yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($staff->foto && file_exists(public_path('assets/img/staff_foto/' . $staff->foto))) {
                unlink(public_path('assets/img/staff_foto/' . $staff->foto));
            }

            // Ambil nama file
            $nama_file = $staff->user->id . '_' . $request->file('foto')->getClientOriginalName(); // Menggunakan ID user untuk menghindari duplikasi

            // Simpan file ke folder public/assets/img/staff_foto
            $request->file('foto')->move(public_path('assets/img/staff_foto'), $nama_file);

            // Simpan HANYA nama file ke database, terpisah dari update data lainnya
            $staff->foto = $nama_file;
            $staff->save();
        }

        return redirect()->route('staff.profile')->with('success', 'Profile berhasil diperbarui.');
    }
}
