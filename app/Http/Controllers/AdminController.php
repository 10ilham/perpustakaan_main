<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminModel;
use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\PeminjamanModel;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showAdminData()
    {
        // Ambil data admin berdasarkan user yang sedang login
        $admin = AdminModel::where('user_id', Auth::id())->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan.');
        }

        // Data untuk dashboard
        $totalBuku = BukuModel::count();
        $totalKategori = KategoriModel::count();
        $totalPeminjaman = PeminjamanModel::where('status', '!=', 'Dibatalkan')->where('status', '!=', 'Diproses')->count();

        // Total anggota tidak termasuk admin
        // $totalAnggota = User::where('level', '!=', 'admin')->count();
        // Total semua anggota termasuk admin
        $totalAnggota = User::count();

        // Mendapatkan 10 buku terpopuler
        $bukuPopuler = PeminjamanController::getBukuPopuler(10);

        return view('layouts.AdminDashboard', compact('admin', 'totalBuku', 'totalKategori', 'totalPeminjaman', 'totalAnggota', 'bukuPopuler'));
    }

    public function showProfile()
    {
        // Ambil data admin berdasarkan user yang sedang login
        $admin = AdminModel::where('user_id', Auth::id())->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan.');
        }

        return view('admin.profile', compact('admin'));
    }

    // Fungsi edit profile
    public function editProfile()
    {
        // Ambil data admin berdasarkan user yang sedang login
        $admin = AdminModel::where('user_id', Auth::id())->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan.');
        }
        // Kirim data ke view untuk ditampilkan di form edit
        return view('admin.edit', compact('admin'));
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

        // Ambil data admin berdasarkan user yang sedang login
        $admin = AdminModel::where('user_id', Auth::id())->first();

        // Validasi input
        $request->validate([
            'nama' => 'required|regex:/^[a-zA-Z\s]+$/|max:80',
            'email' => 'required|email|max:70|unique:users,email,' . $admin->user->id,
            'nip' => 'required|numeric|digits:18|unique:admin,nip,' . $admin->id . '|unique:guru,nip|unique:staff,nip',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_telepon' => 'required|numeric|digits_between:10,13|unique:admin,no_telepon,' . $admin->id . '|unique:siswa,no_telepon|unique:guru,no_telepon|unique:staff,no_telepon',
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048'
        ], $messages);

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan.');
        }

        // Siapkan data untuk update (kecualikan foto untuk mencegah overwrite (fotonya nambah terus)
        $adminData = $request->except('foto');

        // Cek apakah email diubah
        $emailChanged = $admin->user->email != $request->email;
        $oldEmail = $admin->user->email;
        $newEmail = $request->email;

        // Update nama user
        $admin->user->update([
            'nama' => $request->nama,
        ]);

        // Jika email berubah, kirim email verifikasi menggunakan VerificationController
        if ($emailChanged) {
            $verificationController = new VerificationController();
            return $verificationController->sendVerificationEmail($admin->user, $newEmail);
        }

        // update password jika diisi
        if ($request->password) {
            $admin->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // Update data di tabel admin
        $admin->update($adminData);

        // Jika ada file foto yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($admin->foto && file_exists(public_path('assets/img/admin_foto/' . $admin->foto))) {
                unlink(public_path('assets/img/admin_foto/' . $admin->foto));
            }

            // Ambil nama file
            $nama_file = $admin->user->id . '_' . $request->file('foto')->getClientOriginalName(); // Menggunakan ID user untuk menghindari duplikasi

            // Simpan file ke folder public/assets/img/admin_foto
            $request->file('foto')->move(public_path('assets/img/admin_foto'), $nama_file);

            // Simpan HANYA nama file ke database, terpisah dari update data lainnya
            $admin->foto = $nama_file;
            $admin->save();
        }

        return redirect()->route('admin.profile')->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Generate chart data for admin dashboard
     */
    public function getChartData(Request $request)
    {
        $period = $request->query('period', 'day'); // menentukan periode default ke 'day' yang ditampilkan di admin dashboard

        // Tentukan rentang waktu berdasarkan periode
        $startDate = now();
        $endDate = now();

        if ($period == 'day') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $format = 'H:i';
            $interval = 'hour';
            $intervalValue = 1; // setiap 1 jam untuk detail lebih baik
        } elseif ($period == 'week') {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();
            $format = 'd/m';
            $interval = 'day';
            $intervalValue = 1; // setiap hari
        } elseif ($period == 'month') {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
            $format = 'd/m';
            $interval = 'day';
            $intervalValue = 1; // setiap 1 hari
        }

        // Ambil data total peminjaman untuk verifikasi
        $totalPeminjamanDB = PeminjamanModel::count();
        $totalPeminjamanSiswa = PeminjamanModel::whereHas('user', function ($query) {
            $query->where('level', 'siswa');
        })->count();
        $totalPeminjamanGuru = PeminjamanModel::whereHas('user', function ($query) {
            $query->where('level', 'guru');
        })->count();
        $totalPeminjamanStaff = PeminjamanModel::whereHas('user', function ($query) {
            $query->where('level', 'staff');
        })->count();

        // Generate labels untuk chart (tanggal)
        $labels = [];
        $current = clone $startDate;

        if ($interval == 'hour') {
            // Untuk periode hari, buat label per jam
            for ($hour = 0; $hour < 24; $hour += $intervalValue) {
                $labels[] = sprintf('%02d:00', $hour);
            }
        } else {
            // Format untuk week dan month seperti sebelumnya
            while ($current <= $endDate) {
                $labels[] = $current->format($format);
                $current->addDays($intervalValue);
            }
        }

        // Ambil data peminjaman untuk setiap level user
        $siswaData = $this->getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, 'siswa');
        $guruData = $this->getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, 'guru');
        $staffData = $this->getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, 'staff');

        // Hitung total dari semua data grafik untuk verifikasi
        $totalInChart = array_sum($siswaData) + array_sum($guruData) + array_sum($staffData);

        // Kembalikan data dalam format JSON yang lebih sederhana
        return response()->json([
            'labels' => $labels,
            'siswa' => $siswaData,
            'guru' => $guruData,
            'staff' => $staffData,
            'totalPeminjamanDB' => $totalPeminjamanDB,
            'totalSiswa' => $totalPeminjamanSiswa,
            'totalGuru' => $totalPeminjamanGuru,
            'totalStaff' => $totalPeminjamanStaff,
            'totalInChart' => $totalInChart,
            'chartMode' => $interval
        ]);
    }

    /**
     * Helper function to get loan data by period and user level
     * @return array Array of loan counts
     */
    /**
     * Helper function to get loan data by period and user level
     * Mengambil data peminjaman berdasarkan periode dan level user
     * Menggunakan tanggal_pinjam untuk memastikan data sesuai waktu aktual peminjaman
     * @return array Array of loan counts
     */
    private function getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, $userLevel)
    {
        // Ambil data peminjaman aktual dari database
        // Gunakan tanggal_pinjam (bukan created_at) untuk menentukan waktu peminjaman
        $peminjamanData = PeminjamanModel::whereHas('user', function ($query) use ($userLevel) {
            $query->where('level', $userLevel);
        })
            ->where('status', '!=', 'Diproses')
            ->where('status', '!=', 'Dibatalkan')
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->select('tanggal_pinjam') // Gunakan tanggal_pinjam untuk timestamp yang lebih akurat
            ->get();

        if ($interval == 'hour') {
            // Untuk tampilan per jam (24 jam), tanpa menit
            $hourData = array_fill(0, 24, 0); // Inisialisasi 24 jam dengan nilai 0

            // Hitung peminjaman untuk setiap jam
            foreach ($peminjamanData as $peminjaman) {
                $tanggalPinjam = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam);
                $hour = (int) $tanggalPinjam->format('H');

                // Tambahkan count untuk jam yang sesuai
                $hourData[$hour]++;
            }

            return $hourData;
        } else {
            // Format untuk week dan month
            $format = ($interval == 'day') ? 'd/m' : 'Y-m-d';
            $dateMap = [];

            // Inisialisasi semua tanggal dalam rentang dengan nilai 0
            $current = clone $startDate;
            while ($current <= $endDate) {
                $dateKey = $current->format($format);
                $dateMap[$dateKey] = 0;
                $current->addDays($intervalValue);
            }

            // Hitung peminjaman untuk setiap tanggal
            // PERBAIKAN: Menggunakan tanggal_pinjam, bukan created_at
            foreach ($peminjamanData as $peminjaman) {
                $dateKey = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format($format);
                if (isset($dateMap[$dateKey])) {
                    $dateMap[$dateKey]++;
                }
            }

            // Konversi ke array berurutan
            return array_values($dateMap);
        }
    }
}
