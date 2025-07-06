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
    /**
     * Tampilkan dashboard admin dengan statistik
     */
    public function showAdminData()
    {
        // Ambil data admin yang sedang login - ELOQUENT
        $admin = AdminModel::where('user_id', Auth::id())->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan.');
        }

        // Hitung statistik dashboard - ELOQUENT
        $totalBuku = BukuModel::count();
        $totalKategori = KategoriModel::count();
        $totalPeminjaman = PeminjamanModel::whereNotIn('status', ['Dibatalkan', 'Diproses'])->count();
        $totalAnggota = User::count();

        // Ambil buku populer - ELOQUENT
        $bukuPopuler = PeminjamanController::getBukuPopuler(10);

        return view('layouts.AdminDashboard', compact(
            'admin',
            'totalBuku',
            'totalKategori',
            'totalPeminjaman',
            'totalAnggota',
            'bukuPopuler'
        ));
    }

    /**
     * Tampilkan halaman profile admin
     */
    public function showProfile()
    {
        // Ambil data admin yang sedang login - ELOQUENT
        $admin = AdminModel::where('user_id', Auth::id())->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan.');
        }

        return view('admin.profile', compact('admin'));
    }

    /**
     * Tampilkan form edit profile admin
     */
    public function editProfile()
    {
        // Ambil data admin yang sedang login - ELOQUENT
        $admin = AdminModel::where('user_id', Auth::id())->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan.');
        }

        return view('admin.edit', compact('admin'));
    }

    /**
     * Update profile admin
     */
    public function updateProfile(Request $request)
    {
        // Ambil data admin yang sedang login - ELOQUENT
        $admin = AdminModel::where('user_id', Auth::id())->first();

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

        // Cek apakah email atau password berubah
        $emailChanged = $admin->user->email != $request->email;
        $passwordChanged = $request->filled('password');

        // Update data user - ELOQUENT
        $admin->user->update(['nama' => $request->nama]);

        // Update password jika ada - ELOQUENT (sebelum email verification)
        if ($passwordChanged) {
            $admin->user->update(['password' => bcrypt($request->password)]);
        }

        // Update data admin - ELOQUENT (kecualikan foto untuk mencegah overwrite)
        $admin->update($request->except(['foto', 'password', 'password_confirmation']));

        // Handle upload foto (sebelum email verification)
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($admin->foto && file_exists(public_path('assets/img/admin_foto/' . $admin->foto))) {
                unlink(public_path('assets/img/admin_foto/' . $admin->foto));
            }

            // Simpan foto baru
            $nama_file = $admin->user->id . '_' . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move(public_path('assets/img/admin_foto'), $nama_file);

            // Update nama foto di database - ELOQUENT
            $admin->update(['foto' => $nama_file]);
        }

        // Jika email berubah, kirim verifikasi email dan logout
        if ($emailChanged) {
            $verificationController = new VerificationController();
            return $verificationController->sendVerificationEmail($admin->user, $request->email);
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

        return redirect()->route('admin.profile')->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Generate data chart untuk dashboard admin
     */
    public function getChartData(Request $request)
    {
        $period = $request->query('period', 'day');

        // Tentukan rentang waktu berdasarkan periode
        [$startDate, $endDate, $format, $interval, $intervalValue] = $this->getDateRange($period);

        // Ambil data total peminjaman per level - ELOQUENT
        $totalPeminjamanSiswa = PeminjamanModel::whereHas('user', fn($q) => $q->where('level', 'siswa'))->count();
        $totalPeminjamanGuru = PeminjamanModel::whereHas('user', fn($q) => $q->where('level', 'guru'))->count();
        $totalPeminjamanStaff = PeminjamanModel::whereHas('user', fn($q) => $q->where('level', 'staff'))->count();

        // Generate labels untuk chart
        $labels = $this->generateLabels($startDate, $endDate, $interval, $intervalValue, $format);

        // Ambil data peminjaman per level - ELOQUENT
        $siswaData = $this->getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, 'siswa');
        $guruData = $this->getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, 'guru');
        $staffData = $this->getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, 'staff');

        return response()->json([
            'labels' => $labels,
            'siswa' => $siswaData,
            'guru' => $guruData,
            'staff' => $staffData,
            'totalSiswa' => $totalPeminjamanSiswa,
            'totalGuru' => $totalPeminjamanGuru,
            'totalStaff' => $totalPeminjamanStaff,
        ]);
    }

    /**
     * Tentukan rentang tanggal berdasarkan periode
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'day':
                return [now()->startOfDay(), now()->endOfDay(), 'H:i', 'hour', 1];
            case 'week':
                return [now()->subDays(6)->startOfDay(), now()->endOfDay(), 'd/m', 'day', 1];
            case 'month':
                return [now()->subDays(29)->startOfDay(), now()->endOfDay(), 'd/m', 'day', 1];
            default:
                return [now()->startOfDay(), now()->endOfDay(), 'H:i', 'hour', 1];
        }
    }

    /**
     * Generate labels untuk chart
     */
    private function generateLabels($startDate, $endDate, $interval, $intervalValue, $format)
    {
        $labels = [];

        if ($interval == 'hour') {
            for ($hour = 0; $hour < 24; $hour += $intervalValue) {
                $labels[] = sprintf('%02d:00', $hour);
            }
        } else {
            $current = clone $startDate;
            while ($current <= $endDate) {
                $labels[] = $current->format($format);
                $current->addDays($intervalValue);
            }
        }

        return $labels;
    }

    /**
     * Ambil data peminjaman berdasarkan periode dan level user
     */
    private function getPeminjamanByPeriodAndLevel($startDate, $endDate, $interval, $intervalValue, $userLevel)
    {
        // Ambil data peminjaman dari database - ELOQUENT
        $peminjamanData = PeminjamanModel::whereHas('user', fn($q) => $q->where('level', $userLevel))
            ->whereNotIn('status', ['Diproses', 'Dibatalkan'])
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->select('tanggal_pinjam')
            ->get();

        if ($interval == 'hour') {
            // Data per jam (24 jam)
            $hourData = array_fill(0, 24, 0);

            foreach ($peminjamanData as $peminjaman) {
                $hour = (int) \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('H');
                $hourData[$hour]++;
            }

            return $hourData;
        } else {
            // Data per hari
            $format = 'd/m';
            $dateMap = [];

            // Inisialisasi semua tanggal dengan nilai 0
            $current = clone $startDate;
            while ($current <= $endDate) {
                $dateMap[$current->format($format)] = 0;
                $current->addDays($intervalValue);
            }

            // Hitung peminjaman per tanggal
            foreach ($peminjamanData as $peminjaman) {
                $dateKey = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format($format);
                if (isset($dateMap[$dateKey])) {
                    $dateMap[$dateKey]++;
                }
            }

            return array_values($dateMap);
        }
    }
}
