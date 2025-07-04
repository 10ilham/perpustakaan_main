<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeminjamanModel;
use App\Models\SanksiModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index()
    {
        // === STATISTIK UMUM ===
        // Hitung total peminjaman (tidak termasuk buku yang masih diproses atau dibatalkan)
        $totalPeminjaman = PeminjamanModel::where('status', '!=', 'Diproses')
            ->where('status', '!=', 'Dibatalkan')
            ->count();

        // Hitung buku yang saat ini masih dipinjam atau terlambat dikembalikan
        $belumKembali = PeminjamanModel::whereIn('status', ['Dipinjam', 'Terlambat'])->count();

        // Hitung total buku yang sudah dikembalikan
        $sudahKembali = PeminjamanModel::where('status', 'Dikembalikan')->count();

        // === HITUNG KETERLAMBATAN ===
        // Bagian 1: Buku yang masih dipinjam tapi sudah lewat tanggal kembali
        // (tanggal kembali lebih kecil dari hari ini = harusnya sudah dikembalikan)
        $terlambatMasihPinjam = PeminjamanModel::whereIn('status', ['Dipinjam', 'Terlambat'])
            ->where('tanggal_kembali', '<', now()->toDateString())
            ->count();

        // Bagian 2: Buku yang sudah dikembalikan tapi terlambat
        // (tanggal pengembalian lebih besar dari tanggal kembali yang seharusnya)
        $terlambatSudahKembali = PeminjamanModel::where('status', 'Dikembalikan')
            ->whereRaw('DATE(tanggal_pengembalian) > DATE(tanggal_kembali)')
            ->count();

        $terlambat = $terlambatMasihPinjam + $terlambatSudahKembali;

        // === STATISTIK BERDASARKAN JENIS PENGGUNA ===
        // Ambil data level pengguna (siswa, guru, staff) beserta jumlahnya - exclude admin
        $statistikLevel = User::select('level', DB::raw('count(*) as total_user'))
            ->where('level', '!=', 'admin') // Exclude admin from statistics
            ->groupBy('level')
            ->get()
            ->map(function ($user) {
                // Untuk setiap jenis pengguna, hitung total buku yang pernah dipinjam
                $user->total_peminjaman = PeminjamanModel::whereHas('user', function ($q) use ($user) {
                    $q->where('level', $user->level);
                })->where('status', '!=', 'Diproses')
                    ->where('status', '!=', 'Dibatalkan')
                    ->count();

                // Untuk setiap jenis pengguna, hitung buku yang sedang dipinjam saat ini
                $user->sedang_pinjam = PeminjamanModel::whereHas('user', function ($q) use ($user) {
                    $q->where('level', $user->level);
                })->whereIn('status', ['Dipinjam', 'Terlambat'])->count();

                return $user;
            });

        return view('laporan.index', compact(
            'totalPeminjaman',
            'belumKembali',
            'sudahKembali',
            'terlambat',
            'statistikLevel'
        ));
    }

    /**
     * Menampilkan daftar buku yang belum dikembalikan
     *
     * Fungsi ini menampilkan daftar buku yang masih dipinjam atau terlambat
     * dengan berbagai filter berdasarkan tanggal dan jenis pengguna
     */
    public function belumKembali(Request $request)
    {
        // Dapatkan semua peminjaman dengan status 'Dipinjam' atau 'Terlambat'
        $query = PeminjamanModel::with(['user', 'buku'])  // Load relasi user dan buku
            ->whereIn('status', ['Dipinjam', 'Terlambat']);

        // Filter data untuk non-admin: hanya tampilkan data peminjaman mereka sendiri
        if (Auth::user()->level !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_selesai') && $request->tanggal_selesai) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_selesai);
        }

        // Filter berdasarkan level user jika ada (hanya untuk admin)
        if (Auth::user()->level === 'admin' && $request->has('level') && $request->level) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        // Filter berdasarkan status sedang dipinjam (tetapi masih belum terlambat) atau terlambat jika ada
        if ($request->has('status') && $request->status) {
            if ($request->status === 'belum_terlambat') {
                // Ambil peminjaman yang masih aktif (Dipinjam dan belum terlambat)
                $query->where('status', 'Dipinjam')->where('tanggal_kembali', '>=', now());
            } elseif ($request->status === 'terlambat') {
                // Ambil peminjaman yang terlambat
                $query->where('status', 'Terlambat');
            }
        }

        $peminjamanBelumKembali = $query->orderBy('tanggal_kembali', 'asc')
            ->get()
            ->map(function ($peminjaman) {
                $tanggalKembali = \Carbon\Carbon::parse($peminjaman->tanggal_kembali);
                $hariIni = \Carbon\Carbon::now();

                $peminjaman->hari_terlambat = 0;
                $peminjaman->status_keterlambatan = 'normal';

                if ($tanggalKembali->isPast()) {
                    $peminjaman->hari_terlambat = $hariIni->diffInDays($tanggalKembali);
                    $peminjaman->status_keterlambatan = 'terlambat';
                } elseif ($tanggalKembali->isToday()) {
                    $peminjaman->status_keterlambatan = 'hari_ini';
                } elseif ($tanggalKembali->isTomorrow()) {
                    $peminjaman->status_keterlambatan = 'besok';
                }

                return $peminjaman;
            });

        // Data untuk filter (hanya untuk admin)
        $levels = ['siswa', 'guru', 'staff'];

        return view('laporan.belum_kembali', compact('peminjamanBelumKembali', 'levels'));
    }

    /**
     * Menampilkan daftar buku yang sudah dikembalikan
     *
     * Fungsi ini menampilkan daftar buku yang sudah dikembalikan
     * dengan berbagai filter berdasarkan tanggal dan jenis pengguna
     */
    public function sudahKembali(Request $request)
    {
        // Dapatkan semua peminjaman dengan status 'Dikembalikan'
        $query = PeminjamanModel::with(['user', 'buku'])  // Load relasi user dan buku
            ->where('status', 'Dikembalikan');

        // Filter data untuk non-admin: hanya tampilkan data peminjaman mereka sendiri
        if (Auth::user()->level !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->whereDate('tanggal_pengembalian', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_selesai') && $request->tanggal_selesai) {
            $query->whereDate('tanggal_pengembalian', '<=', $request->tanggal_selesai);
        }

        // Filter berdasarkan level user jika ada (hanya untuk admin)
        if (Auth::user()->level === 'admin' && $request->has('level') && $request->level) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        // Filter berdasarkan status pengembalian (tepat waktu atau terlambat) jika ada
        if ($request->has('status') && $request->status) {
            if ($request->status === 'tepat_waktu') {
                // Ambil peminjaman yang dikembalikan tepat waktu atau lebih awal
                // (tanggal_pengembalian <= tanggal_kembali)
                $query->whereRaw('tanggal_pengembalian <= tanggal_kembali');
            } elseif ($request->status === 'terlambat') {
                // Ambil peminjaman yang dikembalikan terlambat
                // (tanggal_pengembalian > tanggal_kembali)
                $query->whereRaw('tanggal_pengembalian > tanggal_kembali');
            }
        }

        $peminjamanSudahKembali = $query->orderBy('tanggal_pengembalian', 'desc')->get();

        // Data untuk filter (hanya untuk admin)
        $levels = ['siswa', 'guru', 'staff'];

        return view('laporan.sudah_kembali', compact('peminjamanSudahKembali', 'levels'));
    }

    /**
     * Fungsi untuk menghasilkan data grafik peminjaman dan pengembalian
     *
     * Fungsi ini mengambil data untuk ditampilkan dalam bentuk grafik statistik
     * berdasarkan periode waktu yang dipilih: hari ini, minggu ini, bulan ini,
     * 6 bulan terakhir, atau tahun ini.
     */
    public function getChartData(Request $request)
    {
        // Ambil periode dari request, default-nya 6 bulan terakhir
        $period = $request->get('period', '6months');
        $data = []; // Array untuk menyimpan data grafik

        // Array untuk menerjemahkan nama bulan dan hari dalam bahasa Indonesia
        // Key: nomor bulan (1-12), Value: nama bulan singkat dalam bahasa Indonesia
        $bulanIndonesia = [
            1 => 'Jan',  // Januari
            2 => 'Feb',  // Februari
            3 => 'Mar',  // Maret
            4 => 'Apr',  // April
            5 => 'Mei',  // Mei
            6 => 'Jun',  // Juni
            7 => 'Jul',  // Juli
            8 => 'Agu',  // Agustus
            9 => 'Sep',  // September
            10 => 'Okt', // Oktober
            11 => 'Nov', // November
            12 => 'Des'  // Desember
        ];

        // Array untuk menerjemahkan nama hari dari bahasa Inggris ke Indonesia
        // Key: nama hari dalam bahasa Inggris (dari format('l')), Value: nama hari singkat dalam bahasa Indonesia
        $hariIndonesia = [
            'Sunday' => 'Min',    // Minggu
            'Monday' => 'Sen',    // Senin
            'Tuesday' => 'Sel',   // Selasa
            'Wednesday' => 'Rab', // Rabu
            'Thursday' => 'Kam',  // Kamis
            'Friday' => 'Jum',    // Jumat
            'Saturday' => 'Sab'   // Sabtu
        ];

        switch ($period) {
            case 'day':
                // === STATISTIK UNTUK PERIODE HARI INI (24 JAM) ===

                // Dapatkan tanggal hari ini (mulai dari jam 00:00)
                $today = now()->startOfDay();

                // Ambil semua data peminjaman hari ini dengan waktu yang tepat
                // Status 'Diproses' tidak termasuk dalam perhitungan
                // PERBAIKAN: Gunakan tanggal_pinjam, bukan created_at untuk statistik yang lebih akurat
                $todayLoans = PeminjamanModel::where('status', '!=', 'Diproses')
                    ->where('status', '!=', 'Dibatalkan')
                    ->whereDate('tanggal_pinjam', $today->toDateString())
                    ->get(['id', 'tanggal_pinjam']);

                // Ambil data pengembalian hari ini
                // Catatan: tanggal_pengembalian hanya berisi tanggal (tidak ada jam),
                // jadi kita gunakan updated_at yang memiliki informasi jam
                $todayReturns = PeminjamanModel::where('status', 'Dikembalikan')
                    ->whereDate('tanggal_pengembalian', $today->toDateString())
                    ->get(['id', 'tanggal_pengembalian', 'updated_at']);

                // Buat array untuk menyimpan jumlah peminjaman dan pengembalian per jam
                // Inisialisasi dengan nilai 0 untuk semua 24 jam
                $loansByHour = array_fill(0, 24, 0);     // Peminjaman per jam
                $returnsByHour = array_fill(0, 24, 0);   // Pengembalian per jam

                // Hitung peminjaman per jam
                // PERBAIKAN: Gunakan tanggal_pinjam untuk data peminjaman yang lebih akurat
                foreach ($todayLoans as $loan) {
                    $timestamp = $loan->tanggal_pinjam;
                    $hour = \Carbon\Carbon::parse($timestamp)->hour; // Ambil jam dari timestamp
                    $loansByHour[$hour]++; // Tambahkan penghitung untuk jam tersebut
                }

                // Hitung pengembalian per jam menggunakan updated_at yang berisi info jam
                foreach ($todayReturns as $return) {
                    $timestamp = $return->updated_at; // Gunakan updated_at, bukan tanggal_pengembalian
                    $hour = \Carbon\Carbon::parse($timestamp)->hour;
                    $returnsByHour[$hour]++;
                }

                // Buat data untuk grafik dengan format 24 jam (00:00 hingga 23:00)
                for ($hour = 0; $hour < 24; $hour++) {
                    $hourLabel = sprintf("%02d:00", $hour); // Format label jam: 00:00, 01:00, dst
                    $data[] = [
                        'label' => $hourLabel,
                        'dipinjam' => $loansByHour[$hour],
                        'dikembalikan' => $returnsByHour[$hour]
                    ];
                }
                break;

            case 'week':
                // === STATISTIK UNTUK PERIODE 7 HARI TERAKHIR ===

                // Kode di bawah ini tidak digunakan (menggunakan 1 minggu dari Senin-Minggu)
                // $startOfWeek = now()->startOfWeek();
                // $endOfWeek = now()->endOfWeek();
                // $daysInWeek = 7;
                // for ($day = 0; $day < $daysInWeek; $day++) {
                //     $date = $startOfWeek->copy()->addDays($day);
                //     $dayName = $hariIndonesia[$date->format('l')];
                //     $data[] = [
                //         'label' => $dayName . ', ' . $date->format('j') . ' ' . $bulanIndonesia[$date->month],
                //         'dipinjam' => PeminjamanModel::whereDate('tanggal_pinjam', $date->toDateString())->count(),
                //         'dikembalikan' => PeminjamanModel::whereDate('tanggal_pengembalian', $date->toDateString())
                //             ->where('status', 'Dikembalikan')
                //             ->count()
                //     ];
                // }
                // break;

                // Ambil data 7 hari terakhir secara berurutan mundur
                for ($i = 6; $i >= 0; $i--) {
                    $day = now()->subDays($i);  // Hitung tanggal: hari ini sampai 6 hari
                    // format('l') - 'l' kecil digunakan untuk mendapatkan nama hari lengkap dalam bahasa Inggris
                    // contoh: 'Monday', 'Tuesday', dst. Lalu dikonversi ke nama hari dalam bahasa Indonesia
                    $dayName = $hariIndonesia[$day->format('l')];  // Mendapatkan nama hari dalam bahasa Indonesia

                    $data[] = [
                        // Format label: Nama Hari, Tanggal Bulan (contoh: Sen, 20 Jun)
                        'label' => $dayName . ', ' . $day->format('j') . ' ' . $bulanIndonesia[$day->month],

                        // Jumlah peminjaman pada tanggal tersebut (tidak termasuk status 'Diproses')
                        'dipinjam' => PeminjamanModel::where('status', '!=', 'Diproses')
                            ->where('status', '!=', 'Dibatalkan')
                            ->whereDate('tanggal_pinjam', $day->toDateString())->count(),

                        // Jumlah pengembalian pada tanggal tersebut
                        'dikembalikan' => PeminjamanModel::whereDate('tanggal_pengembalian', $day->toDateString())
                            ->where('status', 'Dikembalikan')
                            ->count()
                    ];
                }
                break;

            case 'month':
                // === STATISTIK UNTUK BULAN INI (DARI TANGGAL 1 SAMPAI AKHIR BULAN) ===

                $currentMonth = now();  // Tanggal dan waktu saat ini
                $startOfMonth = $currentMonth->copy()->startOfMonth();  // Tanggal 1 pada bulan ini
                $endOfMonth = $currentMonth->copy()->endOfMonth();  // Tanggal terakhir pada bulan ini
                $daysInMonth = $endOfMonth->day;  // Jumlah hari dalam bulan ini (28/30/31)

                // Buat data untuk setiap hari dalam bulan ini
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = $currentMonth->copy()->day($day);  // Set tanggal ke-n
                    $data[] = [
                        // Format label: Tanggal Bulan (contoh: 15 Jun)
                        'label' => $day . ' ' . $bulanIndonesia[$date->month],

                        // Jumlah peminjaman pada tanggal tersebut (tidak termasuk status 'Diproses')
                        'dipinjam' => PeminjamanModel::where('status', '!=', 'Diproses')
                            ->where('status', '!=', 'Dibatalkan')
                            ->whereDate('tanggal_pinjam', $date->toDateString())->count(),

                        // Jumlah pengembalian pada tanggal tersebut
                        'dikembalikan' => PeminjamanModel::whereDate('tanggal_pengembalian', $date->toDateString())
                            ->where('status', 'Dikembalikan')
                            ->count()
                    ];
                }
                break;

            // Alternatif: Data 30 hari terakhir (tidak digunakan)
            // Kode ini tidak digunakan tapi disimpan sebagai referensi
            // for ($i = 29; $i >= 0; $i--) {
            //     $day = now()->subDays($i);
            //     $data[] = [
            //         'label' => $day->format('j') . ' ' . $bulanIndonesia[$day->month],
            //         'dipinjam' => PeminjamanModel::whereDate('tanggal_pinjam', $day->toDateString())->count(),
            //         'dikembalikan' => PeminjamanModel::whereDate('tanggal_pengembalian', $day->toDateString())
            //             ->where('status', 'Dikembalikan')
            //             ->count()
            //     ];
            // }
            // break;

            case '6months':
                // === STATISTIK UNTUK 6 BULAN TERAKHIR ===

                // Ambil data bulanan untuk 6 bulan terakhir
                for ($i = 5; $i >= 0; $i--) {
                    $month = now()->subMonths($i);  // Bulan ke-i dari sekarang mundur ke belakang

                    $data[] = [
                        // Format label: Bulan Tahun (contoh: Jun 2025)
                        'label' => $bulanIndonesia[$month->month] . ' ' . $month->year,

                        // Jumlah peminjaman pada bulan dan tahun tersebut (tidak termasuk status 'Diproses')
                        'dipinjam' => PeminjamanModel::where('status', '!=', 'Diproses')
                            ->where('status', '!=', 'Dibatalkan')
                            ->whereYear('tanggal_pinjam', $month->year)    // Filter berdasarkan tahun
                            ->whereMonth('tanggal_pinjam', $month->month)  // Filter berdasarkan bulan
                            ->count(),

                        // Jumlah pengembalian pada bulan dan tahun tersebut
                        'dikembalikan' => PeminjamanModel::whereYear('tanggal_pengembalian', $month->year)
                            ->whereMonth('tanggal_pengembalian', $month->month)
                            ->where('status', 'Dikembalikan')
                            ->count()
                    ];
                }
                break;

            case 'year':
                // === STATISTIK UNTUK TAHUN INI (JANUARI SAMPAI DESEMBER) ===

                $currentYear = now()->year;  // Tahun saat ini

                // Ambil data untuk setiap bulan dalam tahun ini
                for ($month = 1; $month <= 12; $month++) {
                    $data[] = [
                        // Format label: Bulan Tahun (contoh: Jan 2025)
                        'label' => $bulanIndonesia[$month] . ' ' . $currentYear,

                        // Jumlah peminjaman pada bulan tersebut dalam tahun ini (tidak termasuk status 'Diproses')
                        'dipinjam' => PeminjamanModel::where('status', '!=', 'Diproses')
                            ->where('status', '!=', 'Dibatalkan')
                            ->whereYear('tanggal_pinjam', $currentYear)   // Filter berdasarkan tahun ini
                            ->whereMonth('tanggal_pinjam', $month)        // Filter berdasarkan bulan 1-12
                            ->count(),

                        // Jumlah pengembalian pada bulan tersebut dalam tahun ini
                        'dikembalikan' => PeminjamanModel::whereYear('tanggal_pengembalian', $currentYear)
                            ->whereMonth('tanggal_pengembalian', $month)
                            ->where('status', 'Dikembalikan')
                            ->count()
                    ];
                }
                break;
        }

        return response()->json($data);
    }

    /**
     * Fungsi untuk menghasilkan data diagram lingkaran (pie chart)
     *
     * Fungsi ini menghasilkan data untuk:
     * 1. Diagram lingkaran berdasarkan level pengguna (siswa/guru/staff)
     * 2. Diagram status buku (sedang dipinjam/sudah dikembalikan)
     */
    public function getPieChartData(Request $request)
    {
        // Ambil periode dari request, default-nya 6 bulan terakhir
        $period = $request->get('period', '6months');

        // Dapatkan rentang tanggal berdasarkan periode
        $dateRange = $this->getDateRange($period);

        // === DATA DIAGRAM BERDASARKAN JENIS PENGGUNA ===
        // Ambil data peminjaman berdasarkan level pengguna (kecuali admin karena admin tidak meminjam buku)
        $levelData = User::select('level', DB::raw('count(*) as total_user'))
            ->where('level', '!=', 'admin')  // Kecualikan level admin
            ->groupBy('level')               // Kelompokkan berdasarkan level
            ->get()
            ->map(function ($user) use ($dateRange, $period) {
                // Untuk konsistensi antar grafik, kita perlu menangani periode yang berbeda secara berbeda
                if ($period === 'day') {
                    // Untuk tampilan harian: tampilkan status saat ini (buku yang sedang dipinjam)
                    $user->total_peminjaman = PeminjamanModel::whereHas('user', function ($q) use ($user) {
                        $q->where('level', $user->level);  // Filter berdasarkan level pengguna
                    })
                        ->whereIn('status', ['Dipinjam', 'Terlambat'])  // Hanya status "sedang dipinjam"
                        ->count();
                } else {
                    // Untuk periode lain: tampilkan peminjaman dalam periode (tidak termasuk "Diproses")
                    $query = PeminjamanModel::whereHas('user', function ($q) use ($user) {
                        $q->where('level', $user->level);  // Filter berdasarkan level pengguna
                    })->where('status', '!=', 'Diproses') // Kecualikan status "Diproses"
                        ->where('status', '!=', 'Dibatalkan'); // Kecualikan status "Dibatalkan"

                    // Terapkan filter rentang tanggal jika ada
                    if ($dateRange) {
                        $query->whereBetween('tanggal_pinjam', $dateRange);
                    }

                    $user->total_peminjaman = $query->count();
                }

                // Hitung buku yang sedang dipinjam saat ini (untuk diagram status)
                $user->sedang_pinjam = PeminjamanModel::whereHas('user', function ($q) use ($user) {
                    $q->where('level', $user->level);
                })->whereIn('status', ['Dipinjam', 'Terlambat'])->count();

                return $user;
            });

        // === DATA DIAGRAM STATUS BUKU ===
        // Hitung total buku yang sedang dipinjam (tidak termasuk peminjaman oleh admin)
        $totalSedangPinjam = $levelData->sum('sedang_pinjam');

        // Untuk diagram status, hitung buku yang sudah dikembalikan berdasarkan logika periode
        $totalDikembalikanPeriod = 0;

        if ($period === 'day') {
            // Untuk tampilan harian: tampilkan buku yang dikembalikan hari ini
            $today = now()->toDateString();
            $totalDikembalikanPeriod = PeminjamanModel::where('status', 'Dikembalikan')
                ->whereHas('user', function ($q) {
                    $q->where('level', '!=', 'admin');  // Kecualikan admin
                })
                ->whereDate('tanggal_pengembalian', $today)  // Filter berdasarkan tanggal hari ini
                ->count();
        } else {
            // Untuk periode lain: tampilkan buku yang dikembalikan dalam periode
            if ($dateRange) {
                // Jika ada rentang tanggal yang ditentukan
                $totalDikembalikanPeriod = PeminjamanModel::where('status', 'Dikembalikan')
                    ->whereHas('user', function ($q) {
                        $q->where('level', '!=', 'admin');  // Kecualikan admin
                    })
                    ->whereBetween('tanggal_pengembalian', $dateRange)  // Filter berdasarkan rentang tanggal
                    ->count();
            } else {
                // Jika tidak ada rentang tanggal (semua waktu)
                $totalDikembalikanPeriod = PeminjamanModel::where('status', 'Dikembalikan')
                    ->whereHas('user', function ($q) {
                        $q->where('level', '!=', 'admin');  // Kecualikan admin
                    })
                    ->count();
            }
        }

        return response()->json([
            'levelData' => $levelData,
            'statusData' => [
                'sedang_pinjam' => $totalSedangPinjam,
                'sudah_kembali' => $totalDikembalikanPeriod
            ]
        ]);
    }

    /**
     * Fungsi helper untuk menentukan rentang tanggal berdasarkan periode
     *
     * @param string $period Periode waktu: day, week, month, 6months, year
     * @return array|null Array berisi [tanggal_awal, tanggal_akhir] atau null jika tidak ada filter
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'day':
                // Hari ini: dari jam 00:00 sampai 23:59:59
                return [now()->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString()];

            case 'week':
                // 7 hari terakhir: dari 6 hari yang lalu jam 00:00 sampai hari ini jam 23:59:59
                return [now()->subDays(6)->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString()];

            case 'month':
                // Bulan ini: dari tanggal 1 jam 00:00 sampai tanggal terakhir bulan ini jam 23:59:59
                return [now()->startOfMonth()->toDateTimeString(), now()->endOfMonth()->toDateTimeString()];

            case '6months':
                // 6 bulan terakhir: dari awal bulan 5 bulan yang lalu sampai akhir bulan ini
                return [now()->subMonths(5)->startOfMonth()->toDateTimeString(), now()->endOfMonth()->toDateTimeString()];

            case 'year':
                // Tahun ini: dari 1 Januari jam 00:00 sampai 31 Desember jam 23:59:59
                return [now()->startOfYear()->toDateTimeString(), now()->endOfYear()->toDateTimeString()];

            default:
                return null; // Tidak ada filter waktu (semua data)
        }
    }

    public function sanksi(Request $request)
    {
        $query = SanksiModel::with(['peminjaman.buku', 'peminjaman.user'])
            ->whereHas('peminjaman.user', function($q) {
                $q->where('level', '!=', 'admin'); // Exclude admin from sanksi reports
            });

        // Filter berdasarkan parameter
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        if ($request->filled('jenis_sanksi')) {
            $query->where('jenis_sanksi', 'like', '%' . $request->jenis_sanksi . '%');
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        $sanksi = $query->orderBy('created_at', 'desc')->get();

        // Statistik sanksi (exclude admin)
        $totalSanksi = SanksiModel::whereHas('peminjaman.user', function($q) {
            $q->where('level', '!=', 'admin');
        })->count();
        $belumBayar = SanksiModel::where('status_bayar', 'belum_bayar')
            ->whereHas('peminjaman.user', function($q) {
                $q->where('level', '!=', 'admin');
            })->count();
        $sudahBayar = SanksiModel::where('status_bayar', 'sudah_bayar')
            ->whereHas('peminjaman.user', function($q) {
                $q->where('level', '!=', 'admin');
            })->count();
        $totalDenda = SanksiModel::whereHas('peminjaman.user', function($q) {
            $q->where('level', '!=', 'admin');
        })->sum('total_denda');

        // Statistik berdasarkan level (exclude admin since they don't make loans)
        $statistikLevel = User::select('level',
            DB::raw('COUNT(DISTINCT sanksi.id) as total_sanksi'),
            DB::raw('COUNT(CASE WHEN sanksi.status_bayar = "belum_bayar" THEN 1 END) as belum_bayar'),
            DB::raw('COUNT(CASE WHEN sanksi.status_bayar = "sudah_bayar" THEN 1 END) as sudah_bayar')
        )
        ->leftJoin('peminjaman', 'users.id', '=', 'peminjaman.user_id')
        ->leftJoin('sanksi', 'peminjaman.id', '=', 'sanksi.peminjaman_id')
        ->where('level', '!=', 'admin') // Exclude admin from statistics
        ->groupBy('level')
        ->get();

        return view('laporan.sanksi', compact('sanksi', 'totalSanksi', 'belumBayar', 'sudahBayar', 'totalDenda', 'statistikLevel'));
    }

    public function sanksiBelumBayar(Request $request)
    {
        $query = SanksiModel::with(['peminjaman.buku', 'peminjaman.user'])
            ->where('status_bayar', 'belum_bayar')
            ->whereHas('peminjaman.user', function($q) {
                $q->where('level', '!=', 'admin'); // Exclude admin from sanksi reports
            });

        // Filter data untuk non-admin: hanya tampilkan sanksi mereka sendiri
        if (Auth::user()->level !== 'admin') {
            $query->whereHas('peminjaman', function($q) {
                $q->where('user_id', Auth::id());
            });
        }

        // Filter berdasarkan parameter
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('level')) {
            $query->whereHas('peminjaman.user', function($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        if ($request->filled('jenis_sanksi')) {
            $query->where('jenis_sanksi', 'like', '%' . $request->jenis_sanksi . '%');
        }

        $sanksi = $query->orderBy('created_at', 'desc')->get();

        // Statistik
        $totalSanksi = $sanksi->count();
        $totalDenda = $sanksi->sum('total_denda');
        $siswaCount = $sanksi->filter(function($item) {
            return $item->peminjaman->user->level === 'siswa';
        })->count();
        $guruCount = $sanksi->filter(function($item) {
            return $item->peminjaman->user->level === 'guru';
        })->count();
        $staffCount = $sanksi->filter(function($item) {
            return $item->peminjaman->user->level === 'staff';
        })->count();

        return view('laporan.sanksi-belum-bayar', compact('sanksi', 'totalSanksi', 'totalDenda', 'siswaCount', 'guruCount', 'staffCount'));
    }

    public function sanksiSudahBayar(Request $request)
    {
        $query = SanksiModel::with(['peminjaman.buku', 'peminjaman.user'])
            ->where('status_bayar', 'sudah_bayar')
            ->whereHas('peminjaman.user', function($q) {
                $q->where('level', '!=', 'admin');
            });

        // Filter data untuk non-admin: hanya tampilkan sanksi mereka sendiri
        if (Auth::user()->level !== 'admin') {
            $query->whereHas('peminjaman', function($q) {
                $q->where('user_id', Auth::id());
            });
        }

        // Apply filters
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('level')) {
            $query->whereHas('peminjaman.user', function($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        if ($request->filled('jenis_sanksi')) {
            $query->where('jenis_sanksi', 'like', '%' . $request->jenis_sanksi . '%');
        }

        $sanksi = $query->orderBy('created_at', 'desc')->get();

        $totalSanksi = $sanksi->count();
        $totalDenda = $sanksi->sum('total_denda');
        $siswaCount = $sanksi->filter(function($item) {
            return $item->peminjaman->user->level === 'siswa';
        })->count();
        $guruCount = $sanksi->filter(function($item) {
            return $item->peminjaman->user->level === 'guru';
        })->count();
        $staffCount = $sanksi->filter(function($item) {
            return $item->peminjaman->user->level === 'staff';
        })->count();

        return view('laporan.sanksi-sudah-bayar', compact('sanksi', 'totalSanksi', 'totalDenda', 'siswaCount', 'guruCount', 'staffCount'));
    }
}
