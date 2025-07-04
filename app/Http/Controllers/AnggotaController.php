<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminModel;
use App\Models\SiswaModel;
use App\Models\GuruModel;
use App\Models\StaffModel;
use App\Models\PeminjamanModel;
use App\Models\BukuModel;
use Illuminate\Support\Facades\Auth;

class AnggotaController extends Controller
{
    //
    public function index(Request $request)
    {
        // Filter level
        $level = $request->query('level', 'all');

        // Mengambil data user dengan filter level jika diperlukan
        $query = User::query();

        if ($level !== 'all') {
            $query->where('level', $level);
        }

        $users = $query->get();

        // Data untuk dropdown filter
        $levels = ['all' => 'Semua Level', 'admin' => 'Admin', 'siswa' => 'Siswa', 'guru' => 'Guru', 'staff' => 'Staff'];

        return view('anggota.index', compact('users', 'level', 'levels'));
    }

    // Menampilkan detail anggota
    public function detail($id)
    {
        $user = User::findOrFail($id);
        $profileData = null;

        // Ambil parameter referensi jika ada
        $ref = request('ref');

        // Ambil data profil sesuai level
        if ($user->level === 'admin') {
            $profileData = AdminModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'siswa') {
            $profileData = SiswaModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'guru') {
            $profileData = GuruModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'staff') {
            $profileData = StaffModel::where('user_id', $user->id)->first();
        }

        // Ambil riwayat peminjaman anggota
        $peminjaman = PeminjamanModel::where('user_id', $id)
            ->with(['buku'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('anggota.detail', compact('user', 'profileData', 'peminjaman', 'ref'));
    }

    // Tambah anggota
    public function tambah()
    {
        return view('anggota.tambah');
    }

    // Simpan anggota
    public function simpan(Request $request)
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

            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal :min karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',

            'level.required' => 'Level wajib dipilih',
            'level.in' => 'Level tidak valid',

            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'foto.max' => 'Ukuran gambar tidak boleh lebih dari 3MB',

            'nip.required_if' => 'NIP wajib diisi',
            'nip.numeric' => 'NIP harus berupa angka',
            'nip.digits' => 'NIP harus terdiri dari 18 digit',
            'nip.unique' => 'NIP sudah digunakan',

            'nisn.required_if' => 'NISN wajib diisi',
            'nisn.numeric' => 'NISN harus berupa angka',
            'nisn.digits' => 'NISN harus terdiri dari 10 digit',
            'nisn.unique' => 'NISN sudah digunakan',

            'kelas.required_if' => 'Kelas wajib diisi',
            'kelas.string' => 'Kelas harus berupa teks',
            'kelas.max' => 'Kelas tidak boleh lebih dari :max karakter',

            'mata_pelajaran.required_if' => 'Mata pelajaran wajib diisi',
            'mata_pelajaran.string' => 'Mata pelajaran harus berupa teks',
            'mata_pelajaran.max' => 'Mata pelajaran tidak boleh lebih dari :max karakter',

            'bagian.required_if' => 'Bagian wajib diisi',
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
        ];

        // Validasi input
        $request->validate([
            'nama' => 'required|regex:/^[a-zA-Z\s]+$/|max:80',
            'email' => 'required|email|max:70|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'level' => 'required|in:admin,siswa,guru,staff',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048',
            // Validasi sesuai level
            'nip' => 'required_if:level,admin,guru,staff|numeric|digits:18|unique:admin,nip|unique:guru,nip|unique:staff,nip',
            'nisn' => 'required_if:level,siswa|numeric|digits:10|unique:siswa,nisn',
            'kelas' => 'required_if:level,siswa|string|max:6',
            'mata_pelajaran' => 'required_if:level,guru|string|max:40',
            'bagian' => 'required_if:level,staff|string|max:30',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_telepon' => 'required|numeric|digits_between:10,13|unique:admin,no_telepon|unique:siswa,no_telepon|unique:guru,no_telepon|unique:staff,no_telepon',
        ], $messages);

        $user = User::create($request->only('nama', 'email', 'level', 'password'));
        $user->password = bcrypt($request->password);
        $user->save();
        // Simpan data anggota sesuai level
        if ($user->level === 'admin') {
            $profileData = new AdminModel($request->only('nip', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $folder = 'admin_foto';
        } elseif ($user->level === 'siswa') {
            $profileData = new SiswaModel($request->only('nisn', 'kelas', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $folder = 'siswa_foto';
        } elseif ($user->level === 'guru') {
            $profileData = new GuruModel($request->only('nip', 'mata_pelajaran', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $folder = 'guru_foto';
        } elseif ($user->level === 'staff') {
            $profileData = new StaffModel($request->only('nip', 'bagian', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $folder = 'staff_foto';
        }
        $profileData->user_id = $user->id;
        $profileData->save();
        // Handle foto upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nama_file = $user->id . '_' . $foto->getClientOriginalName(); // Menggunakan ID user untuk menghindari duplikasi
            $foto->move(public_path('assets/img/' . $folder), $nama_file);

            $profileData->foto = $nama_file;
            $profileData->save();
        }
        return redirect()->route('anggota.index')->with('success', 'Anggota baru berhasil ditambahkan.');
    }

    // Edit anggota
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $profileData = null;

        // Ambil parameter referensi jika ada
        $ref = request('ref');

        // Ambil data profil sesuai level
        if ($user->level === 'admin') {
            $profileData = AdminModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'siswa') {
            $profileData = SiswaModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'guru') {
            $profileData = GuruModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'staff') {
            $profileData = StaffModel::where('user_id', $user->id)->first();
        }

        return view('anggota.edit', compact('user', 'profileData', 'ref'));
    }

    // Update anggota
    public function update(Request $request, $id)
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

            'password.min' => 'Password minimal :min karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',

            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'foto.max' => 'Ukuran gambar tidak boleh lebih dari 3MB',

            'nip.required_if' => 'NIP wajib diisi',
            'nip.numeric' => 'NIP harus berupa angka',
            'nip.digits' => 'NIP harus terdiri dari 18 digit',
            'nip.unique' => 'NIP sudah digunakan',

            'nisn.required_if' => 'NISN wajib diisi',
            'nisn.numeric' => 'NISN harus berupa angka',
            'nisn.digits' => 'NISN harus terdiri dari 10 digit',
            'nisn.unique' => 'NISN sudah digunakan',

            'kelas.required_if' => 'Kelas wajib diisi',
            'kelas.string' => 'Kelas harus berupa teks',
            'kelas.max' => 'Kelas tidak boleh lebih dari :max karakter',

            'mata_pelajaran.required_if' => 'Mata pelajaran wajib diisi',
            'mata_pelajaran.string' => 'Mata pelajaran harus berupa teks',
            'mata_pelajaran.max' => 'Mata pelajaran tidak boleh lebih dari :max karakter',

            'bagian.required_if' => 'Bagian wajib diisi',
            'bagian.string' => 'Bagian harus berupa teks',
            'bagian.max' => 'Bagian tidak boleh lebih dari :max karakter',

            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',

            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat tidak boleh lebih dari :max karakter',

            'no_telepon.required' => 'Nomor telepon wajib diisi',
            'no_telepon.numeric' => 'Nomor telepon hanya boleh berisi angka',
            'no_telepon.digits_between' => 'Nomor telepon harus terdiri dari 10 hingga 15 digit',
            'no_telepon.unique' => 'Nomor telepon sudah digunakan',
        ];
        // Validasi input
        $request->validate([
            'nama' => 'required|regex:/^[a-zA-Z\s]+$/|max:80',
            'email' => 'required|email|max:70|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048',
            // Validasi sesuai level
            'nip' => 'required_if:level,admin,guru,staff|numeric|digits:18|unique:admin,nip,' . $id . ',user_id|unique:guru,nip,' . $id . ',user_id|unique:staff,nip,' . $id . ',user_id',
            'nisn' => 'required_if:level,siswa|numeric|digits:10|unique:siswa,nisn,' . $id . ',user_id',
            'kelas' => 'required_if:level,siswa|string|max:6',
            'mata_pelajaran' => 'required_if:level,guru|string|max:50',
            'bagian' => 'required_if:level,staff|string|max:50',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|numeric|digits_between:10,15|unique:admin,no_telepon,' . $id . ',user_id|unique:siswa,no_telepon,' . $id . ',user_id|unique:guru,no_telepon,' . $id . ',user_id|unique:staff,no_telepon,' . $id . ',user_id',
        ], $messages);
        $user = User::findOrFail($id);
        $user->update($request->only('nama', 'email'));

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        // Update atau buat data anggota sesuai level (buat disini maksudnya ketika tabel anggota tersebut belum ada datanya karena awalnya anggota tersebut misalnya ditambahkan lewat seeder yang datanya tidak lengkap)
        if ($user->level === 'admin') {
            $profileData = AdminModel::where('user_id', $user->id)->first();
            if (!$profileData) {
                $profileData = new AdminModel();
                $profileData->user_id = $user->id;
            }
            $profileData->fill($request->only('nip', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $profileData->save();
            $folder = 'admin_foto';
        } elseif ($user->level === 'siswa') {
            $profileData = SiswaModel::where('user_id', $user->id)->first();
            if (!$profileData) {
                $profileData = new SiswaModel();
                $profileData->user_id = $user->id;
            }
            $profileData->fill($request->only('nisn', 'kelas', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $profileData->save();
            $folder = 'siswa_foto';
        } elseif ($user->level === 'guru') {
            $profileData = GuruModel::where('user_id', $user->id)->first();
            if (!$profileData) {
                $profileData = new GuruModel();
                $profileData->user_id = $user->id;
            }
            $profileData->fill($request->only('nip', 'mata_pelajaran', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $profileData->save();
            $folder = 'guru_foto';
        } elseif ($user->level === 'staff') {
            $profileData = StaffModel::where('user_id', $user->id)->first();
            if (!$profileData) {
                $profileData = new StaffModel();
                $profileData->user_id = $user->id;
            }
            $profileData->fill($request->only('nip', 'bagian', 'tanggal_lahir', 'alamat', 'no_telepon'));
            $profileData->save();
            $folder = 'staff_foto';
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($profileData->foto && file_exists(public_path('assets/img/' . $folder . '/' . $profileData->foto))) {
                unlink(public_path('assets/img/' . $folder . '/' . $profileData->foto));
            }

            $foto = $request->file('foto');
            $nama_file = $user->id . '_' . $foto->getClientOriginalName(); // Menggunakan ID user untuk menghindari duplikasi
            $foto->move(public_path('assets/img/' . $folder), $nama_file);

            $profileData->foto = $nama_file;
            $profileData->save();
        }

        // Cek apakah ada referensi ke halaman detail
        if ($request->has('ref') && $request->ref == 'detail') {
            return redirect()->route('anggota.detail', $id)->with('success', 'Anggota berhasil diperbarui.');
        } else {
            return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diperbarui.');
        }
    }

    // Hapus anggota
    public function hapus($id)
    {
        $user = User::findOrFail($id);

        // Hapus data profil sesuai level
        if ($user->level === 'admin') {
            $profile = AdminModel::where('user_id', $user->id)->first();
            if ($profile) {
                // Hapus foto jika ada
                if ($profile->foto && file_exists(public_path('assets/img/admin_foto/' . $profile->foto))) {
                    unlink(public_path('assets/img/admin_foto/' . $profile->foto));
                }
                $profile->delete();
            }
        } elseif ($user->level === 'siswa') {
            $profile = SiswaModel::where('user_id', $user->id)->first();
            if ($profile) {
                // Hapus foto jika ada
                if ($profile->foto && file_exists(public_path('assets/img/siswa_foto/' . $profile->foto))) {
                    unlink(public_path('assets/img/siswa_foto/' . $profile->foto));
                }
                $profile->delete();
            }
        } elseif ($user->level === 'guru') {
            $profile = GuruModel::where('user_id', $user->id)->first();
            if ($profile) {
                // Hapus foto jika ada
                if ($profile->foto && file_exists(public_path('assets/img/guru_foto/' . $profile->foto))) {
                    unlink(public_path('assets/img/guru_foto/' . $profile->foto));
                }
                $profile->delete();
            }
        } elseif ($user->level === 'staff') {
            $profile = StaffModel::where('user_id', $user->id)->first();
            if ($profile) {
                // Hapus foto jika ada
                if ($profile->foto && file_exists(public_path('assets/img/staff_foto/' . $profile->foto))) {
                    unlink(public_path('assets/img/staff_foto/' . $profile->foto));
                }
                $profile->delete();
            }
        }

        // Hapus user
        $user->delete();
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus');
    }

    // Dashboard Anggota (untuk level: siswa, guru, staff)
    public function showAnggotaData()
    {
        $userLevel = Auth::user()->level;
        $userId = Auth::id();
        $profileData = null;

        // Ambil data profil sesuai level
        if ($userLevel === 'siswa') {
            $profileData = SiswaModel::where('user_id', $userId)->first();
        } elseif ($userLevel === 'guru') {
            $profileData = GuruModel::where('user_id', $userId)->first();
        } elseif ($userLevel === 'staff') {
            $profileData = StaffModel::where('user_id', $userId)->first();
        }

        // Menghitung total buku
        $totalBuku = BukuModel::count();

        // Menghitung peminjaman berdasarkan user yang sedang login
        // Peminjaman yang sedang dipinjam (status Dipinjam dan Terlambat)
        $dipinjam = PeminjamanModel::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'Dipinjam')
                    ->orWhere('status', 'Terlambat');
            })->count();

        // Peminjaman yang terlambat
        $terlambat = PeminjamanModel::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'Terlambat')
                    ->orWhere('is_terlambat', true);
            })->count();

        // Peminjaman yang sudah dikembalikan
        $dikembalikan = PeminjamanModel::where('user_id', $userId)
            ->where('status', 'Dikembalikan')
            ->count();

        // Mendapatkan 10 buku terpopuler
        $bukuPopuler = PeminjamanController::getBukuPopuler(10);

        return view('layouts.AnggotaDashboard', compact('profileData', 'userLevel', 'totalBuku', 'dipinjam', 'terlambat', 'dikembalikan', 'bukuPopuler'));
    }

    /**
     * Mengambil data untuk grafik peminjaman anggota
     *
     * Fungsi ini menghasilkan data untuk ditampilkan dalam bentuk grafik statistik
     * berdasarkan periode waktu yang dipilih: hari ini, minggu ini, atau bulan ini.
     * Format data disesuaikan dengan kebutuhan chart di dashboard anggota.
     *
     * Catatan penting:
     * - Hanya peminjaman dengan status selain 'Diproses' yang dihitung
     * - Data untuk periode hari ditampilkan per jam tanpa menit (00:00, 01:00, dst)
     * - Format konsisten dengan AdminController dan LaporanController
     */
    public function getChartData(Request $request)
    {
        $period = $request->query('period', 'day');
        $userId = Auth::id();

        // Tentukan rentang waktu berdasarkan periode
        if ($period == 'day') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $format = 'H:00'; // Format untuk jam lengkap (tanpa menit), contoh: 09:00
            $interval = 'hour';
            $intervalValue = 1; // Interval 1 jam
        } elseif ($period == 'week') {
            // Implementasi saat ini: rentang 7 hari terakhir
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();
            $format = 'd/m';
            $interval = 'day';
            $intervalValue = 1;


            //  * ALTERNATIF: Kode untuk minggu dari Senin sampai Minggu
            //  * Kode ini mengganti rentang waktu dari 7 hari terakhir menjadi
            //  * minggu kalender (Senin sampai Minggu)
            // // Ambil tanggal awal minggu (Senin)
            // $startDate = now()->startOfWeek();  // Carbon default startOfWeek() ke Senin
            // // Ambil tanggal akhir minggu (Minggu)
            // $endDate = now()->endOfWeek();      // Carbon default endOfWeek() ke Minggu
            // $format = 'd/m';
            // $interval = 'day';
            // $intervalValue = 1;

        } elseif ($period == 'month') {
            // Implementasi saat ini: rentang 30 hari terakhir
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
            $format = 'd/m';
            $interval = 'day';
            $intervalValue = 1;

            // //  * ALTERNATIF: Kode untuk bulan dari tanggal 1 sampai akhir bulan
            // //  * Kode ini mengganti rentang waktu dari 30 hari terakhir menjadi
            // //  * bulan kalender (tanggal 1 sampai akhir bulan)
            // // Tanggal saat ini
            // $currentMonth = now();
            // // Tanggal 1 bulan ini
            // $startDate = $currentMonth->copy()->startOfMonth();
            // // Tanggal terakhir bulan ini (28/30/31)
            // $endDate = $currentMonth->copy()->endOfMonth();
            // // Jumlah hari dalam bulan ini
            // $daysInMonth = $endDate->day;
            // $format = 'd/m';
            // $interval = 'day';
            // $intervalValue = 1;
        }

        // Ambil data peminjaman aktual dari database untuk periode yang dipilih
        // Tidak termasuk peminjaman dengan status 'Diproses'
        // Menggunakan tanggal_pinjam untuk timestamp yang lebih akurat,
        $peminjamanFromDB = PeminjamanModel::where('user_id', $userId)
            ->where('status', '!=', 'Diproses') // Mengecualikan peminjaman yang masih berstatus 'Diproses'
            ->where('status', '!=', 'Dibatalkan') // Mengecualikan peminjaman yang dibatalkan
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate]) // Gunakan tanggal_pinjam
            ->select('tanggal_pinjam') // Gunakan tanggal_pinjam
            ->get();

        // Dapatkan total peminjaman user (tidak termasuk status 'Diproses' dan 'Dibatalkan')
        $totalPeminjaman = PeminjamanModel::where('user_id', $userId)
            ->where('status', '!=', 'Diproses')
            ->where('status', '!=', 'Dibatalkan')
            ->count();

        $labels = [];
        $peminjamanData = [];

        if ($period == 'day') {
            // === STATISTIK UNTUK PERIODE HARI INI (24 JAM) ===
            // Inisialisasi array untuk 24 jam dengan nilai 0
            $peminjamanData = array_fill(0, 24, 0);

            // Buat label untuk 24 jam (00:00, 01:00, ..., 23:00)
            for ($hour = 0; $hour < 24; $hour++) {
                $labels[] = sprintf('%02d:00', $hour); // Format: 00:00, 01:00, dst.
            }

            // Hitung peminjaman untuk setiap jam (hanya yang statusnya bukan 'Diproses')
            // Data sudah difilter pada query di atas
            // PERBAIKAN: Menggunakan tanggal_pinjam sebagai timestamp akurat, bukan created_at
            foreach ($peminjamanFromDB as $peminjaman) {
                $tanggalPinjam = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam);
                $hour = (int) $tanggalPinjam->format('H'); // Ambil jam dari timestamp, tanpa menit

                // Tambahkan ke penghitung jam yang sesuai
                $peminjamanData[$hour]++;
            }
        } else {
            // === STATISTIK UNTUK PERIODE MINGGU ATAU BULAN ===
            $current = clone $startDate;
            $dateMap = [];

            // Inisialisasi map tanggal (semua hari dalam rentang)
            while ($current <= $endDate) {
                $dateKey = $current->format('Y-m-d');
                $dateMap[$dateKey] = 0; // Nilai awal 0 untuk setiap tanggal
                $current->addDay();
            }

            // Hitung peminjaman per hari
            // PERBAIKAN: Menggunakan tanggal_pinjam untuk timestamp yang lebih akurat
            foreach ($peminjamanFromDB as $peminjaman) {
                $dateKey = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('Y-m-d');
                if (isset($dateMap[$dateKey])) {
                    $dateMap[$dateKey]++; // Tambah penghitung untuk tanggal tersebut
                }
            }            // Generate labels dan data untuk chart
            $current = clone $startDate;
            while ($current <= $endDate) {
                // Format label berdasarkan periode (d/m untuk minggu dan bulan)
                $labels[] = $current->format($format);

                // Ambil data untuk tanggal ini
                $dateKey = $current->format('Y-m-d');
                $peminjamanData[] = isset($dateMap[$dateKey]) ? $dateMap[$dateKey] : 0;

                $current->addDay(); // Pindah ke hari berikutnya
            }
        }

        // Format label jam dibuat dengan konsisten untuk periode harian (00:00, 01:00, dst)
        // sehingga tidak perlu pemrosesan tambahan untuk menampilkan menit
        // Ini agar data chart anggota konsisten dengan chart pada dashboard admin

        // Hitung total data dalam chart (untuk verifikasi)
        $totalInChart = array_sum($peminjamanData);

        // Kembalikan data dalam format JSON yang mudah digunakan oleh frontend
        return response()->json([
            'labels' => $labels,                            // Label untuk sumbu X chart (jam atau tanggal)
            'peminjaman' => $peminjamanData,                // Data jumlah peminjaman untuk ditampilkan
            'total' => $totalPeminjaman,                    // Total semua peminjaman user (tidak termasuk 'Diproses')
            'totalInChart' => $totalInChart,                // Total peminjaman dalam periode chart
        ]);
    }
}
