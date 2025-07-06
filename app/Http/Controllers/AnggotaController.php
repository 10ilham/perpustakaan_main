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
use Carbon\Carbon;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PeminjamanController;

class AnggotaController extends Controller
{
    // ELOQUENT - Menampilkan daftar anggota dengan filter level
    public function index(Request $request)
    {
        // Filter level
        $level = $request->query('level', 'all');

        // ELOQUENT - Mengambil data user dengan filter level jika diperlukan
        $query = User::query();

        if ($level !== 'all') {
            $query->where('level', $level);
        }

        $users = $query->get();

        // Data untuk dropdown filter
        $levels = ['all' => 'Semua Level', 'admin' => 'Admin', 'siswa' => 'Siswa', 'guru' => 'Guru', 'staff' => 'Staff'];

        return view('anggota.index', compact('users', 'level', 'levels'));
    }

    // ELOQUENT - Menampilkan detail anggota dengan riwayat peminjaman
    public function detail($id)
    {
        $user = User::findOrFail($id);
        $profileData = null;

        // Ambil parameter referensi jika ada
        $ref = request('ref');

        // ELOQUENT - Ambil data profil sesuai level
        if ($user->level === 'admin') {
            $profileData = AdminModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'siswa') {
            $profileData = SiswaModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'guru') {
            $profileData = GuruModel::where('user_id', $user->id)->first();
        } elseif ($user->level === 'staff') {
            $profileData = StaffModel::where('user_id', $user->id)->first();
        }

        // ELOQUENT - Ambil riwayat peminjaman anggota
        $peminjaman = PeminjamanModel::where('user_id', $id)
            ->with(['buku'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Tambahkan informasi keterlambatan untuk setiap peminjaman
        foreach ($peminjaman as $item) {
            $tanggalBatasKembali = Carbon::parse($item->tanggal_kembali)->endOfDay();
            $sekarang = Carbon::now();

            // Logika keterlambatan
            $isTerlambat = ($item->status === 'Dipinjam' && $sekarang->greaterThan($tanggalBatasKembali)) ||
                ($item->status === 'Terlambat' && $sekarang->greaterThan($tanggalBatasKembali)) ||
                $item->is_terlambat;

            $item->is_late = $isTerlambat;

            // Hitung hari terlambat
            if ($isTerlambat && ($item->status === 'Dipinjam' || $item->status === 'Terlambat')) {
                $hariTerlambat = $this->hitungHariTerlambat($item);
                $item->late_days = $hariTerlambat > 0 ? $hariTerlambat : 0;
            } elseif ($item->is_terlambat && $item->jumlah_hari_terlambat) {
                $item->late_days = $item->jumlah_hari_terlambat;
            } else {
                $item->late_days = 0;
            }
        }

        return view('anggota.detail', compact('user', 'profileData', 'peminjaman', 'ref'));
    }

    // Menampilkan form tambah anggota
    public function tambah()
    {
        return view('anggota.tambah');
    }

    // ELOQUENT - Menyimpan anggota baru
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

        // ELOQUENT - Buat user baru
        $user = User::create($request->only('nama', 'email', 'level', 'password'));
        $user->password = bcrypt($request->password);
        $user->save();

        // ELOQUENT - Simpan data anggota sesuai level
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
        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nama_file = $user->id . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('assets/img/' . $folder), $nama_file);

            $profileData->foto = $nama_file;
            $profileData->save();
        }
        return redirect()->route('anggota.index')->with('success', 'Anggota baru berhasil ditambahkan.');
    }

    // ELOQUENT - Menampilkan form edit anggota
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $profileData = null;

        // Ambil parameter referensi jika ada
        $ref = request('ref');

        // ELOQUENT - Ambil data profil sesuai level
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

    // ELOQUENT - Memperbarui data anggota
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

        // ELOQUENT - Update data user
        $user = User::findOrFail($id);
        $user->update($request->only('nama', 'email'));

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        // ELOQUENT - Update atau buat data anggota sesuai level
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

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($profileData->foto && file_exists(public_path('assets/img/' . $folder . '/' . $profileData->foto))) {
                unlink(public_path('assets/img/' . $folder . '/' . $profileData->foto));
            }

            $foto = $request->file('foto');
            $nama_file = $user->id . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('assets/img/' . $folder), $nama_file);

            $profileData->foto = $nama_file;
            $profileData->save();
        }

        // Redirect berdasarkan referensi
        if ($request->has('ref') && $request->ref == 'detail') {
            return redirect()->route('anggota.detail', $id)->with('success', 'Anggota berhasil diperbarui.');
        } else {
            return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diperbarui.');
        }
    }

    // ELOQUENT - Menghapus anggota dan data terkait
    public function hapus($id)
    {
        $user = User::findOrFail($id);

        // ELOQUENT - Hapus data profil sesuai level
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

        // ELOQUENT - Hapus user
        $user->delete();
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus');
    }

    // ELOQUENT - Dashboard untuk anggota (siswa, guru, staff)
    public function showAnggotaData()
    {
        $userLevel = Auth::user()->level;
        $userId = Auth::id();
        $profileData = null;

        // ELOQUENT - Ambil data profil sesuai level
        if ($userLevel === 'siswa') {
            $profileData = SiswaModel::where('user_id', $userId)->first();
        } elseif ($userLevel === 'guru') {
            $profileData = GuruModel::where('user_id', $userId)->first();
        } elseif ($userLevel === 'staff') {
            $profileData = StaffModel::where('user_id', $userId)->first();
        }

        // ELOQUENT - Menghitung total buku
        $totalBuku = BukuModel::count();

        // ELOQUENT - Menghitung peminjaman berdasarkan user yang sedang login
        $dipinjam = PeminjamanModel::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'Dipinjam')
                    ->orWhere('status', 'Terlambat');
            })->count();

        $terlambat = PeminjamanModel::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'Terlambat')
                    ->orWhere('is_terlambat', true);
            })->count();

        $dikembalikan = PeminjamanModel::where('user_id', $userId)
            ->where('status', 'Dikembalikan')
            ->count();

        // Mendapatkan buku terpopuler
        $bukuPopuler = PeminjamanController::getBukuPopuler(10);

        return view('layouts.AnggotaDashboard', compact('profileData', 'userLevel', 'totalBuku', 'dipinjam', 'terlambat', 'dikembalikan', 'bukuPopuler'));
    }

    /**
     * ELOQUENT - Mengambil data untuk grafik peminjaman anggota
     *
     * Fungsi ini menghasilkan data untuk ditampilkan dalam bentuk grafik statistik
     * berdasarkan periode waktu yang dipilih: hari ini, minggu ini, atau bulan ini.
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

        // ELOQUENT - Ambil data peminjaman untuk periode yang dipilih
        $peminjamanFromDB = PeminjamanModel::where('user_id', $userId)
            ->where('status', '!=', 'Diproses')
            ->where('status', '!=', 'Dibatalkan')
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->select('tanggal_pinjam')
            ->get();

        // ELOQUENT - Dapatkan total peminjaman user
        $totalPeminjaman = PeminjamanModel::where('user_id', $userId)
            ->where('status', '!=', 'Diproses')
            ->where('status', '!=', 'Dibatalkan')
            ->count();

        $labels = [];
        $peminjamanData = [];

        if ($period == 'day') {
            // Statistik untuk periode hari ini (24 jam)
            $peminjamanData = array_fill(0, 24, 0);

            // Buat label untuk 24 jam
            for ($hour = 0; $hour < 24; $hour++) {
                $labels[] = sprintf('%02d:00', $hour);
            }

            // Hitung peminjaman untuk setiap jam
            foreach ($peminjamanFromDB as $peminjaman) {
                $tanggalPinjam = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam);
                $hour = (int) $tanggalPinjam->format('H');
                $peminjamanData[$hour]++;
            }
        } else {
            // Statistik untuk periode minggu atau bulan
            $current = clone $startDate;
            $dateMap = [];

            // Inisialisasi map tanggal
            while ($current <= $endDate) {
                $dateKey = $current->format('Y-m-d');
                $dateMap[$dateKey] = 0;
                $current->addDay();
            }

            // Hitung peminjaman per hari
            foreach ($peminjamanFromDB as $peminjaman) {
                $dateKey = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('Y-m-d');
                if (isset($dateMap[$dateKey])) {
                    $dateMap[$dateKey]++;
                }
            }

            // Generate labels dan data untuk chart
            $current = clone $startDate;
            while ($current <= $endDate) {
                $labels[] = $current->format($format);
                $dateKey = $current->format('Y-m-d');
                $peminjamanData[] = isset($dateMap[$dateKey]) ? $dateMap[$dateKey] : 0;
                $current->addDay();
            }
        }

        // Hitung total data dalam chart
        $totalInChart = array_sum($peminjamanData);

        // Kembalikan data dalam format JSON
        return response()->json([
            'labels' => $labels,
            'peminjaman' => $peminjamanData,
            'total' => $totalPeminjaman,
            'totalInChart' => $totalInChart,
        ]);
    }

    // Menghitung jumlah hari keterlambatan
    private function hitungHariTerlambat($peminjaman)
    {
        $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();
        $sekarang = Carbon::now();

        // Hitung keterlambatan jika sudah melewati batas
        if ($sekarang->greaterThan($tanggalBatasKembali)) {
            $hariTerlambat = Carbon::parse($peminjaman->tanggal_kembali)->addDay()->startOfDay()
                ->diffInDays($sekarang->startOfDay()) + 1;
            return $hariTerlambat;
        }

        return 0;
    }
}
