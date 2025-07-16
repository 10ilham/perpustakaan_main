<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeminjamanModel;
use App\Models\BukuModel;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\PeminjamanBukuAdminNotification;
use App\Notifications\PeminjamanManualNotification;

class PeminjamanController extends Controller
{
    /**
     * ELOQUENT - Helper method untuk menerapkan filter tanggal pada query peminjaman
     */
    private function applyDateFilter($query, $startDate, $endDate, $status = null)
    {
        // Fungsi ini hanya melihat tanggal_pinjam, terlepas dari status
        if ($startDate && $endDate) {
            // Gunakan Carbon untuk parsing tanggal
            $startDateFormatted = Carbon::parse($startDate)->startOfDay()->format('Y-m-d');
            $endDateFormatted = Carbon::parse($endDate)->endOfDay()->format('Y-m-d');

            // Filter tanggal_pinjam antara start dan end date
            return $query->whereDate('tanggal_pinjam', '>=', $startDateFormatted)
                ->whereDate('tanggal_pinjam', '<=', $endDateFormatted);
        } elseif ($startDate) {
            // Jika hanya ada startDate
            $startDateFormatted = Carbon::parse($startDate)->startOfDay()->format('Y-m-d');
            return $query->whereDate('tanggal_pinjam', '>=', $startDateFormatted);
        } elseif ($endDate) {
            // Jika hanya ada endDate
            $endDateFormatted = Carbon::parse($endDate)->endOfDay()->format('Y-m-d');
            return $query->whereDate('tanggal_pinjam', '<=', $endDateFormatted);
        }

        return $query;
    }

    // ELOQUENT - Menampilkan daftar peminjaman dengan filter
    public function index(Request $request)
    {
        // Perbarui status terlambat terlebih dahulu
        $this->updateLateStatus();

        // Ambil role user
        $userLevel = Auth::user()->level;

        // Filter berdasarkan user_type jika ada
        $userType = $request->input('user_type');

        // Filter status peminjaman
        $status = $request->input('status');

        // Filter berdasarkan rentang tanggal
        $startDate = $request->filled('start_date') ? $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? $request->input('end_date') : null;

        // Validasi format tanggal
        if ($startDate && !$this->validateDate($startDate)) {
            $startDate = null;
        }

        if ($endDate && !$this->validateDate($endDate)) {
            $endDate = null;
        }

        // ELOQUENT - Base query untuk statistik dan daftar peminjaman
        $baseQuery = PeminjamanModel::with(['user', 'buku']);

        if ($userLevel == 'admin') {
            if ($userType) {
                // ELOQUENT - Jika ada filter, tambahkan where clause
                $peminjaman = $baseQuery->whereHas('user', function ($query) use ($userType) {
                    $query->where('level', $userType);
                });

                // Tambahkan filter status jika ada
                if ($status) {
                    if ($status == 'Dipinjam') {
                        $peminjaman = $peminjaman->whereIn('status', ['Dipinjam', 'Terlambat']);
                    } elseif ($status == 'Terlambat') {
                        $peminjaman = $peminjaman->where(function ($query) {
                            $query->where('status', 'Terlambat')
                                ->orWhere(function ($q) {
                                    $q->where('status', 'Dikembalikan')
                                        ->where('is_terlambat', true);
                                });
                        });
                    } elseif ($status == 'Diproses') {
                        // ELOQUENT - Untuk status Diproses
                        $peminjaman = PeminjamanModel::whereHas('user', function ($query) use ($userType) {
                            $query->where('level', $userType);
                        })->where('status', 'Diproses');
                    } elseif ($status == 'Dibatalkan') {
                        // ELOQUENT - Untuk status Dibatalkan
                        $peminjaman = PeminjamanModel::whereHas('user', function ($query) use ($userType) {
                            $query->where('level', $userType);
                        })->where('status', 'Dibatalkan');
                    } else {
                        // Untuk status Dikembalikan
                        $peminjaman = $peminjaman->where('status', $status);
                    }
                }

                // Terapkan filter tanggal
                $peminjaman = $this->applyDateFilter($peminjaman, $startDate, $endDate, $status);
                $peminjaman = $peminjaman->orderBy('created_at', 'desc')->get();

                // ELOQUENT - Query untuk statistik berdasarkan filter
                $totalQuery = PeminjamanModel::whereHas('user', function ($query) use ($userType) {
                    $query->where('level', $userType);
                });

                // Exclude Diproses dan Dibatalkan dari total kecuali jika filter status adalah salah satu dari keduanya
                // if ($status != 'Diproses' && $status != 'Dibatalkan') {
                //     $totalQuery = $totalQuery->whereNotIn('status', ['Diproses', 'Dibatalkan']);
                // }

                $dipinjamQuery = PeminjamanModel::whereHas('user', function ($query) use ($userType) {
                    $query->where('level', $userType);
                })->whereIn('status', ['Dipinjam', 'Terlambat']);

                $dikembalikanQuery = PeminjamanModel::whereHas('user', function ($query) use ($userType) {
                    $query->where('level', $userType);
                })->where('status', 'Dikembalikan');

                $terlambatQuery = PeminjamanModel::whereHas('user', function ($query) use ($userType) {
                    $query->where('level', $userType);
                })->where(function ($query) {
                    $query->where('status', 'Terlambat')
                        ->orWhere(function ($q) {
                            $q->where('status', 'Dikembalikan')
                                ->where('is_terlambat', true);
                        });
                });

                // Tambahkan filter status untuk statistik jika ada
                if ($status) {
                    if ($status == 'Dipinjam') {
                        $totalQuery = $totalQuery->whereIn('status', ['Dipinjam', 'Terlambat']);
                    } elseif ($status == 'Terlambat') {
                        $totalQuery = $totalQuery->where(function ($query) {
                            $query->where('status', 'Terlambat')
                                ->orWhere(function ($q) {
                                    $q->where('status', 'Dikembalikan')
                                        ->where('is_terlambat', true);
                                });
                        });
                    } elseif ($status == 'Diproses') {
                        // Untuk status Diproses, khusus menghitung peminjaman dengan status Diproses
                        $totalQuery = PeminjamanModel::where('user_id', Auth::id())->where('status', 'Diproses');
                    } elseif ($status == 'Dibatalkan') {
                        // Untuk status Dibatalkan, khusus menghitung peminjaman yang dibatalkan
                        $totalQuery = PeminjamanModel::where('user_id', Auth::id())->where('status', 'Dibatalkan');
                    } else {
                        // Untuk status Dikembalikan
                        $totalQuery = $totalQuery->where('status', $status);
                    }
                }

                // Terapkan filter tanggal di semua query statistik
                $totalQuery = $this->applyDateFilter($totalQuery, $startDate, $endDate, $status);
                $dipinjamQuery = $this->applyDateFilter($dipinjamQuery, $startDate, $endDate, 'Dipinjam');
                $dikembalikanQuery = $this->applyDateFilter($dikembalikanQuery, $startDate, $endDate, 'Dikembalikan');
                $terlambatQuery = $this->applyDateFilter($terlambatQuery, $startDate, $endDate, 'Terlambat');

                $totalPeminjaman = $totalQuery->count();
                $dipinjam = $dipinjamQuery->count();
                $dikembalikan = $dikembalikanQuery->count();
                $terlambat = $terlambatQuery->count();
            } else {
                // Admin dapat melihat semua peminjaman jika tidak ada filter user type
                $peminjaman = $baseQuery;

                // Tambahkan filter status jika ada
                if ($status) {
                    if ($status == 'Dipinjam') {
                        $peminjaman = $peminjaman->whereIn('status', ['Dipinjam', 'Terlambat']);
                    } elseif ($status == 'Terlambat') {
                        $peminjaman = $peminjaman->where(function ($query) {
                            $query->where('status', 'Terlambat')
                                ->orWhere(function ($q) {
                                    $q->where('status', 'Dikembalikan')
                                        ->where('is_terlambat', true);
                                });
                        });
                    } else {
                        // Untuk status Dikembalikan
                        $peminjaman = $peminjaman->where('status', $status);
                    }
                }

                // Terapkan filter tanggal
                $peminjaman = $this->applyDateFilter($peminjaman, $startDate, $endDate, $status);
                $peminjaman = $peminjaman->orderBy('created_at', 'desc')->get();

                // Statistik untuk semua peminjaman
                $totalQuery = PeminjamanModel::query();

                // Exclude Diproses dari total kecuali jika filter status adalah Diproses
                // if ($status != 'Diproses' && $status != 'Dibatalkan') {
                //     $totalQuery = $totalQuery->whereNotIn('status', ['Diproses', 'Dibatalkan']);
                // }
                $dipinjamQuery = PeminjamanModel::whereIn('status', ['Dipinjam', 'Terlambat']);
                $dikembalikanQuery = PeminjamanModel::where('status', 'Dikembalikan');
                $terlambatQuery = PeminjamanModel::where(function ($query) {
                    $query->where('status', 'Terlambat')
                        ->orWhere(function ($q) {
                            $q->where('status', 'Dikembalikan')
                                ->where('is_terlambat', true);
                        });
                });

                // Tambahkan filter status untuk statistik jika ada
                if ($status) {
                    if ($status == 'Dipinjam') {
                        $totalQuery = $totalQuery->whereIn('status', ['Dipinjam', 'Terlambat']);
                    } elseif ($status == 'Terlambat') {
                        $totalQuery = $totalQuery->where(function ($query) {
                            $query->where('status', 'Terlambat')
                                ->orWhere(function ($q) {
                                    $q->where('status', 'Dikembalikan')
                                        ->where('is_terlambat', true);
                                });
                        });
                    } elseif ($status == 'Diproses') {
                        // Untuk status Diproses, khusus menghitung peminjaman dengan status Diproses
                        $totalQuery = PeminjamanModel::where('status', 'Diproses');
                    } elseif ($status == 'Dibatalkan') {
                        // Untuk status Dibatalkan, khusus menghitung peminjaman yang dibatalkan
                        $totalQuery = PeminjamanModel::where('status', 'Dibatalkan');
                    } else {
                        // Untuk status Dikembalikan
                        $totalQuery = $totalQuery->where('status', $status);
                    }
                }

                // Terapkan filter tanggal di semua query statistik
                $totalQuery = $this->applyDateFilter($totalQuery, $startDate, $endDate, $status);
                $dipinjamQuery = $this->applyDateFilter($dipinjamQuery, $startDate, $endDate, 'Dipinjam');
                $dikembalikanQuery = $this->applyDateFilter($dikembalikanQuery, $startDate, $endDate, 'Dikembalikan');
                $terlambatQuery = $this->applyDateFilter($terlambatQuery, $startDate, $endDate, 'Terlambat');

                $totalPeminjaman = $totalQuery->count();
                $dipinjam = $dipinjamQuery->count();
                $dikembalikan = $dikembalikanQuery->count();
                $terlambat = $terlambatQuery->count();
            }
        } else {
            // Siswa, staff dan guru hanya melihat peminjaman mereka sendiri
            $peminjaman = $baseQuery->where('user_id', Auth::id());

            // Tambahkan filter status jika ada
            if ($status) {
                if ($status == 'Dipinjam') {
                    $peminjaman = $peminjaman->whereIn('status', ['Dipinjam', 'Terlambat']);
                } elseif ($status == 'Terlambat') {
                    $peminjaman = $peminjaman->where(function ($query) {
                        $query->where('status', 'Terlambat')
                            ->orWhere(function ($q) {
                                $q->where('status', 'Dikembalikan')
                                    ->where('is_terlambat', true);
                            });
                    });
                } else {
                    // Untuk status Dikembalikan
                    $peminjaman = $peminjaman->where('status', $status);
                }
            }

            // Terapkan filter tanggal
            $peminjaman = $this->applyDateFilter($peminjaman, $startDate, $endDate, $status);
            $peminjaman = $peminjaman->orderBy('created_at', 'desc')->get();

            // Statistik untuk peminjaman pengguna sendiri
            $totalQuery = PeminjamanModel::where('user_id', Auth::id());

            // Exclude Diproses dari total kecuali jika filter status adalah Diproses
            // if ($status != 'Diproses' && $status != 'Dibatalkan') {
            //     $totalQuery = $totalQuery->whereNotIn('status', ['Diproses', 'Dibatalkan']);
            // }
            $dipinjamQuery = PeminjamanModel::where('user_id', Auth::id())->whereIn('status', ['Dipinjam', 'Terlambat']);
            $dikembalikanQuery = PeminjamanModel::where('user_id', Auth::id())->where('status', 'Dikembalikan');
            $terlambatQuery = PeminjamanModel::where('user_id', Auth::id())->where(function ($query) {
                $query->where('status', 'Terlambat')
                    ->orWhere(function ($q) {
                        $q->where('status', 'Dikembalikan')
                            ->where('is_terlambat', true);
                    });
            });

            // Tambahkan filter status untuk statistik jika ada
            if ($status) {
                if ($status == 'Dipinjam') {
                    $totalQuery = $totalQuery->whereIn('status', ['Dipinjam', 'Terlambat']);
                } elseif ($status == 'Terlambat') {
                    $totalQuery = $totalQuery->where(function ($query) {
                        $query->where('status', 'Terlambat')
                            ->orWhere(function ($q) {
                                $q->where('status', 'Dikembalikan')
                                    ->where('is_terlambat', true);
                            });
                    });
                } else {
                    // Untuk status Dikembalikan
                    $totalQuery = $totalQuery->where('status', $status);
                }
            }

            // Terapkan filter tanggal
            $totalQuery = $this->applyDateFilter($totalQuery, $startDate, $endDate, $status);
            $dipinjamQuery = $this->applyDateFilter($dipinjamQuery, $startDate, $endDate, 'Dipinjam');
            $dikembalikanQuery = $this->applyDateFilter($dikembalikanQuery, $startDate, $endDate, 'Dikembalikan');
            $terlambatQuery = $this->applyDateFilter($terlambatQuery, $startDate, $endDate, 'Terlambat');

            $totalPeminjaman = $totalQuery->count();
            $dipinjam = $dipinjamQuery->count();
            $dikembalikan = $dikembalikanQuery->count();
            $terlambat = $terlambatQuery->count();
        }

        // Tambahkan informasi keterlambatan untuk setiap peminjaman di index
        foreach ($peminjaman as $item) {
            $tanggalBatasKembali = Carbon::parse($item->tanggal_kembali)->endOfDay();
            $sekarang = Carbon::now();

            // Logika sederhana: hanya terlambat jika sudah lewat akhir hari batas DAN belum dikembalikan
            $isTerlambat = ($item->status === 'Dipinjam' && $sekarang->greaterThan($tanggalBatasKembali)) ||
                ($item->status === 'Terlambat' && $sekarang->greaterThan($tanggalBatasKembali)) ||
                $item->is_terlambat; // Untuk buku yang sudah dikembalikan

            $item->is_late = $isTerlambat;

            // Hitung hari terlambat dengan konsisten
            if ($isTerlambat && ($item->status === 'Dipinjam' || $item->status === 'Terlambat')) {
                // Untuk buku yang belum dikembalikan
                $hariTerlambat = $this->hitungHariTerlambat($item);
                $item->late_days = $hariTerlambat > 0 ? $hariTerlambat : 0;
            } elseif ($item->is_terlambat && $item->jumlah_hari_terlambat) {
                // Untuk buku yang sudah dikembalikan dengan keterlambatan
                $item->late_days = $item->jumlah_hari_terlambat;
            } else {
                $item->late_days = 0;
            }
        }

        return view('peminjaman.index', compact('peminjaman', 'totalPeminjaman', 'dipinjam', 'dikembalikan', 'terlambat', 'startDate', 'endDate', 'status'));
    }

    // Method untuk memperbarui status peminjaman yang terlambat
    private function updateLateStatus()
    {
        // Update status menjadi 'Terlambat' untuk peminjaman yang sudah melewati akhir hari batas
        // Hanya terlambat jika sudah melewati jam 23:59:59 dari tanggal_kembali
        $sekarang = Carbon::now();

        // Ambil semua peminjaman dengan status 'Dipinjam'
        $peminjamanDipinjam = PeminjamanModel::where('status', 'Dipinjam')->get();

        foreach ($peminjamanDipinjam as $peminjaman) {
            $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();

            // Hanya update status jika sudah melewati akhir hari batas
            if ($sekarang->greaterThan($tanggalBatasKembali)) {
                $peminjaman->status = 'Terlambat';
                $peminjaman->save();
            }
        }

        // Otomatis ubah status menjadi 'Dibatalkan' untuk peminjaman yang belum diambil (status Diproses) saat melewati Batas Waktu Pengembalian
        $peminjamanDiproses = PeminjamanModel::where('status', 'Diproses')->get();

        foreach ($peminjamanDiproses as $peminjaman) {
            $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();

            // Hanya update status jika sudah melewati akhir hari batas
            if ($sekarang->greaterThan($tanggalBatasKembali)) {
                $peminjaman->status = 'Dibatalkan';
                $peminjaman->save();
            }
        }

        // Kembalikan stok buku untuk peminjaman yang dibatalkan
        $dibatalkanBaru = PeminjamanModel::where('status', 'Dibatalkan')
            ->where('is_stok_returned', false)
            ->get();

        foreach ($dibatalkanBaru as $peminjaman) {
            $buku = BukuModel::find($peminjaman->buku_id);
            if ($buku) {
                // Tambah stok buku
                $buku->stok_buku += 1;

                // Update status buku jika stok tersedia
                if ($buku->stok_buku > 0) {
                    $buku->status = 'Tersedia';
                }

                $buku->save();

                // Tandai bahwa stok sudah dikembalikan
                $peminjaman->is_stok_returned = true;
                $peminjaman->save();
            }
        }
    }

    // Menampilkan form peminjaman buku
    public function formPinjam($id)
    {
        $buku = BukuModel::findOrFail($id);

        // Cek apakah stok buku tersedia
        if ($buku->stok_buku <= 0) {
            // Redirect ke halaman buku berdasarkan level user
            return $this->redirectToAppropriateView()->with('error', 'Stok buku tidak tersedia untuk dipinjam.');
        }

        // Cek apakah user sudah meminjam buku yang sama dan belum dikembalikan
        // Perubahan: Mencakup semua status yang belum dikembalikan (Dipinjam DAN Terlambat)
        $sudahPinjam = PeminjamanModel::where('user_id', Auth::id())
            ->where('buku_id', $id)
            ->whereIn('status', ['Dipinjam', 'Terlambat', 'Diproses'])
            ->first();

        if ($sudahPinjam) {
            return $this->redirectToAppropriateView()->with('error', 'Anda sudah meminjam buku ini dan belum mengembalikannya.');
        }

        // Cek jumlah judul buku yang berbeda yang sedang dipinjam oleh user
        $jumlahJudulPinjam = PeminjamanModel::where('user_id', Auth::id())
            ->whereIn('status', ['Dipinjam', 'Terlambat', 'Diproses'])
            ->distinct('buku_id')
            ->count();

        // Maksimal 2 judul buku yang berbeda yang boleh dipinjam dalam waktu bersamaan
        if ($jumlahJudulPinjam >= 2) {
            return $this->redirectToAppropriateView()->with('error', 'Anda sudah meminjam 2 judul buku yang berbeda. Silakan kembalikan salah satu buku terlebih dahulu untuk meminjam buku lain.');
        }

        return view('peminjaman.form', compact('buku'));
    }

    // Helper method untuk redirect yang tepat berdasarkan level user untuk anggota ketika peminjaman buku tidak bisa dilakukan
    private function redirectToAppropriateView()
    {
        $userLevel = Auth::user()->level;

        // Redirect ke index buku untuk user selain admin yaitu anggota (siswa, guru, staff)
        if ($userLevel !== 'admin') {
            return redirect()->route('buku.index');
        }
    }

    // Proses peminjaman buku
    public function pinjamBuku(Request $request)
    {
        $request->validate(
            [
                'buku_id' => 'required|exists:buku,id',
                'nama' => 'required|string',
                'tanggal_pinjam' => 'required|date|after_or_equal:today',
                'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
                'catatan' => 'nullable|string'
            ],
            [
                'buku_id.required' => 'ID buku diperlukan.',
                'buku_id.exists' => 'Buku tidak ditemukan.',

                'nama.required' => 'Nama peminjam harus diisi.',
                'nama.string' => 'Format nama peminjam tidak valid.',

                'tanggal_pinjam.required' => 'Tanggal pinjam harus diisi.',
                'tanggal_pinjam.date' => 'Format tanggal pinjam tidak valid.',
                'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam minimal hari ini.',

                'tanggal_kembali.required' => 'Tanggal kembali harus diisi.',
                'tanggal_kembali.date' => 'Format tanggal kembali tidak valid.',
                'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam.'
            ]
        );

        if ($request->nama != Auth::user()->nama) {
            return redirect()->back()->with('error', 'Nama peminjam harus sesuai dengan nama akun yang digunakan.')->withInput();
        }

        $buku = BukuModel::findOrFail($request->buku_id);

        // Cek stok buku lagi (double-check)
        if ($buku->stok_buku <= 0) {
            return redirect()->back()->with('error', 'Stok buku tidak tersedia untuk dipinjam.');
        }

        // Cek apakah user sudah meminjam buku yang sama dan belum dikembalikan (termasuk status Terlambat)
        $sudahPinjam = PeminjamanModel::where('user_id', Auth::id())
            ->where('buku_id', $request->buku_id)
            ->whereIn('status', ['Dipinjam', 'Terlambat', 'Diproses'])
            ->first();

        if ($sudahPinjam) {
            return redirect()->back()->with('error', 'Anda sudah meminjam buku ini dan belum mengembalikanannya.')->withInput();
        }

        // Cek jumlah judul buku yang berbeda yang sedang dipinjam oleh user
        $jumlahJudulPinjam = PeminjamanModel::where('user_id', Auth::id())
            ->whereIn('status', ['Dipinjam', 'Terlambat', 'Diproses'])
            ->distinct('buku_id')
            ->count();

        // Maksimal 2 judul buku yang berbeda yang boleh dipinjam dalam waktu bersamaan
        if ($jumlahJudulPinjam >= 2) {
            return redirect()->back()->with('error', 'Anda sudah meminjam 2 judul buku yang berbeda. Silakan kembalikan salah satu buku terlebih dahulu untuk meminjam buku lain.')->withInput();
        }

        // Generate nomor peminjaman
        $no_peminjaman = 'PJM-' . date('YmdHis') . '-' . Str::random(2);

        // Buat record peminjaman
        $peminjaman = new PeminjamanModel();
        $peminjaman->user_id = Auth::id();
        $peminjaman->buku_id = $request->buku_id;
        $peminjaman->no_peminjaman = $no_peminjaman;
        $peminjaman->tanggal_pinjam = $request->tanggal_pinjam;
        $peminjaman->tanggal_kembali = $request->tanggal_kembali;
        $peminjaman->status = 'Diproses';
        $peminjaman->diproses_by = null; // Set diproses_by ke null untuk peminjaman self-service
        $peminjaman->catatan = $request->catatan;
        $peminjaman->save();

        // Kurangi stok buku
        $buku->stok_buku -= 1;

        // Update status buku jika stok habis
        if ($buku->stok_buku <= 0) {
            $buku->status = 'Habis';
        }

        $buku->save();

        // Kirim notifikasi ke admin tentang peminjaman baru
        $this->kirimNotifikasiPeminjamanBaru($peminjaman);

        return redirect()->route('peminjaman.index')->with('success', 'Buku berhasil dipinjam. Silahkan ambil buku di perpustakaan.');
    }

    // Menampilkan detail peminjaman
    public function detail($id)
    {
        // Perbarui status terlambat terlebih dahulu
        $this->updateLateStatus();

        // Ambil parameter referensi jika ada
        $ref = request('ref');
        $anggota_id = request('anggota_id');

        $peminjaman = PeminjamanModel::with(['user', 'buku', 'sanksi'])->findOrFail($id);

        // Tambahkan informasi keterlambatan ke data peminjaman
        // Cek apakah benar-benar terlambat (sudah lewat akhir hari batas)
        $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();
        $sekarang = Carbon::now();

        // Logika sederhana: hanya terlambat jika sudah lewat akhir hari batas DAN belum dikembalikan
        $isTerlambat = ($peminjaman->status === 'Dipinjam' && $sekarang->greaterThan($tanggalBatasKembali)) ||
            ($peminjaman->status === 'Terlambat' && $sekarang->greaterThan($tanggalBatasKembali)) ||
            $peminjaman->is_terlambat; // Untuk buku yang sudah dikembalikan

        $peminjaman->is_late = $isTerlambat;

        // Hitung hari terlambat dengan konsisten
        if ($isTerlambat && ($peminjaman->status === 'Dipinjam' || $peminjaman->status === 'Terlambat')) {
            // Untuk buku yang belum dikembalikan
            $hariTerlambat = $this->hitungHariTerlambat($peminjaman);
            $peminjaman->late_days = $hariTerlambat > 0 ? $hariTerlambat : 0;
        } elseif ($peminjaman->is_terlambat && $peminjaman->jumlah_hari_terlambat) {
            // Untuk buku yang sudah dikembalikan dengan keterlambatan
            $peminjaman->late_days = $peminjaman->jumlah_hari_terlambat;
        } else {
            $peminjaman->late_days = 0;
        }

        // Menentukan apakah menampilkan tombol konfirmasi pengembalian
        $showReturnButton = false;
        if ($peminjaman->status == 'Dipinjam' || $peminjaman->status == 'Terlambat') {
            $showReturnButton = true;
        }

        return view('peminjaman.detail', compact('peminjaman', 'showReturnButton', 'ref', 'anggota_id'));
    }

    // Memeriksa apakah peminjaman terlambat dikembalikan
    public function cekKeterlambatan($peminjaman)
    {
        // Hanya terlambat jika sudah melewati akhir hari tanggal batas kembali
        // Jika hari ini adalah tanggal batas kembali, masih belum terlambat sampai jam 23:59:59
        $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();
        $sekarang = Carbon::now();

        return $peminjaman->status == 'Dipinjam' && $sekarang->greaterThan($tanggalBatasKembali);
    }

    // Menghitung jumlah hari keterlambatan
    public function hitungHariTerlambat($peminjaman)
    {
        $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();
        $sekarang = Carbon::now();

        // Hanya hitung keterlambatan jika sudah melewati akhir hari batas
        if ($sekarang->greaterThan($tanggalBatasKembali)) {
            // Hitung selisih hari dari hari berikutnya setelah batas kembali
            $hariTerlambat = Carbon::parse($peminjaman->tanggal_kembali)->addDay()->startOfDay()
                ->diffInDays($sekarang->startOfDay()) + 1;
            return $hariTerlambat;
        }

        return 0;
    }

    // Proses pengembalian buku (untuk Admin)
    public function kembalikanBuku($id)
    {
        $peminjaman = PeminjamanModel::findOrFail($id);

        // Hanya admin yang dapat mengembalikan buku
        if (Auth::user()->level !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengembalikan buku.');
        }

        // Ambil tanggal batas kembali dan jadikan jam 23:59:59 (akhir hari)
        $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();
        $tanggalPengembalian = Carbon::now();

        // Periksa status terlambat, hanya terlambat jika pengembalian melewati akhir hari tanggal batas kembali
        $isTerlambat = false;
        if ($peminjaman->status == 'Terlambat' || $tanggalPengembalian->greaterThan($tanggalBatasKembali)) {
            $isTerlambat = true;
        }

        // Update status peminjaman ke 'Dikembalikan'
        // Namun tetap simpan informasi keterlambatan dengan field terpisah
        $peminjaman->is_terlambat = $isTerlambat; // Kolom(Field) untuk melacak keterlambatan
        $peminjaman->status = 'Dikembalikan';
        $peminjaman->tanggal_pengembalian = $tanggalPengembalian;

        // Hitung jumlah hari terlambat jika terlambat
        if ($isTerlambat) {
            // Gunakan startOfDay() untuk kedua tanggal agar perhitungan konsisten
            $hariTerlambat = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay()
                ->diffInDays($tanggalPengembalian->copy()->startOfDay(), false);

            // Jika hasil negatif, ubah menjadi positif
            $hariTerlambat = abs($hariTerlambat);
            $peminjaman->jumlah_hari_terlambat = $hariTerlambat;
        } else {
            // Jika tidak terlambat, pastikan jumlah hari terlambat adalah 0
            $peminjaman->jumlah_hari_terlambat = 0;
        }

        $peminjaman->save();

        // Tambah stok buku
        $buku = BukuModel::findOrFail($peminjaman->buku_id);
        $buku->stok_buku += 1;

        // Update status buku jika stok tersedia
        if ($buku->stok_buku > 0) {
            $buku->status = 'Tersedia';
        }

        $buku->save();

        // Cek parameter referensi dari halaman anggota
        $ref = request('ref');
        $anggota_id = request('anggota_id');

        // Pesan sukses berdasarkan status keterlambatan
        $successMessage = $isTerlambat ?
            'Buku berhasil dikembalikan dengan status terlambat ' . $peminjaman->jumlah_hari_terlambat . ' hari.' :
            'Buku berhasil dikembalikan tepat waktu.';

        // Redirect berdasarkan parameter referensi
        if ($ref == 'anggota' && $anggota_id) {
            return redirect()->route('anggota.detail', $anggota_id)->with('success', $successMessage);
        } else {
            return redirect()->route('peminjaman.index')->with('success', $successMessage);
        }
    }

    // Hapus data peminjaman (khusus Admin)
    public function hapusPeminjaman($id)
    {
        $peminjaman = PeminjamanModel::findOrFail($id);

        // Hanya admin yang dapat menghapus data peminjaman
        if (Auth::user()->level !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data peminjaman.');
        }

        // Cek apakah peminjaman masih aktif
        if (in_array($peminjaman->status, ['Dipinjam', 'Terlambat', 'Diproses'])) {
            $statusText = '';
            switch ($peminjaman->status) {
                case 'Dipinjam':
                    $statusText = 'sedang dipinjam';
                    break;
                case 'Terlambat':
                    $statusText = 'terlambat dan belum dikembalikan';
                    break;
                case 'Diproses':
                    $statusText = 'sedang diproses';
                    break;
            }

            return redirect()->back()->with('error', "Data peminjaman tidak dapat dihapus karena status peminjaman masih aktif ($statusText). Silakan tunggu hingga buku dikembalikan atau dibatalkan.");
        }

        // Hanya peminjaman dengan status 'Dikembalikan' atau 'Dibatalkan' yang boleh dihapus
        if (!in_array($peminjaman->status, ['Dikembalikan', 'Dibatalkan'])) {
            return redirect()->back()->with('error', 'Data peminjaman hanya dapat dihapus jika status sudah Dikembalikan atau Dibatalkan.');
        }

        // Hapus data peminjaman (hanya untuk status non-aktif)
        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    // Mendapatkan buku populer (paling banyak dipinjam) untuk ditampilkan di dashboard
    public static function getBukuPopuler($limit = 10)
    {
        // Mengelompokkan peminjaman berdasarkan buku_id dan menghitung jumlahnya
        $bukuPopuler = PeminjamanModel::select('buku_id')
            ->selectRaw('COUNT(*) as total_peminjaman')
            ->whereNotIn('status', ['Diproses', 'Dibatalkan'])
            ->groupBy('buku_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->with('buku') // Load relasi buku
            ->get();

        return $bukuPopuler;
    }

    /**
     * Mengirim notifikasi ke semua admin tentang peminjaman buku baru
     * Terhubung ke Notifications/PeminjamanBukuAdminNotification
     * @param PeminjamanModel $peminjaman - Model peminjaman yang baru dibuat
     * @return void
     */
    private function kirimNotifikasiPeminjamanBaru(PeminjamanModel $peminjaman)
    {
        // Cari semua user dengan level admin
        $admin = User::whereIn('level', ['admin'])->get();

        foreach ($admin as $user) {
            // Kirim notifikasi ke masing-masing admin
            $user->notify(new PeminjamanBukuAdminNotification($peminjaman));
        }
    }

    /**
     * Mengirim notifikasi ke anggota tentang peminjaman manual yang dilakukan admin
     * Terhubung ke Notifications/PeminjamanManualNotification
     * @param PeminjamanModel $peminjaman - Model peminjaman yang baru dibuat
     * @return void
     */
    private function kirimNotifikasiPeminjamanManual(PeminjamanModel $peminjaman)
    {
        // Ambil user (anggota) yang terkait dengan peminjaman
        $anggota = User::find($peminjaman->user_id);

        if ($anggota) {
            // Kirim notifikasi ke anggota
            $anggota->notify(new PeminjamanManualNotification($peminjaman));
        }
    }

    /**
     * Menampilkan form peminjaman manual untuk admin
     * @return \Illuminate\Http\Response
     */
    public function formManual()
    {
        // Hanya admin yang bisa akses
        if (Auth::user()->level !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        }

        // Ambil semua buku yang tersedia
        $buku = BukuModel::where('status', 'Tersedia')->where('stok_buku', '>', 0)->get();

        return view('peminjaman.manual', compact('buku'));
    }

    /**
     * Mendapatkan daftar anggota berdasarkan level untuk peminjaman manual
     * Anggota yang sudah meminjam maksimal 2 judul buku yang berbeda tidak ditampilkan
     */
    public function getAnggotaByLevel($level)
    {
        // Hanya admin yang bisa akses
        if (Auth::user()->level !== 'admin') {
            return response()->json(['error' => 'Dilarang masuk! Selain admin tidak diperbolehkan'], 403);
        }

        // Dapatkan user_id yang sudah meminjam 2 judul buku yang berbeda (maksimal)
        $userIdDenganPeminjamanMaksimal = PeminjamanModel::whereIn('status', ['Diproses', 'Dipinjam', 'Terlambat'])
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT buku_id) >= 2')
            ->pluck('user_id')
            ->toArray();

        $anggota = [];

        if ($level === 'siswa') {
            $anggota = User::where('level', 'siswa')
                ->whereNotIn('id', $userIdDenganPeminjamanMaksimal) // Exclude anggota dengan peminjaman maksimal (2 judul buku)
                ->with('siswa')
                ->get()
                ->map(function ($user) {
                    // Hitung jumlah judul buku yang sedang dipinjam
                    $jumlahJudulPinjam = PeminjamanModel::where('user_id', $user->id)
                        ->whereIn('status', ['Diproses', 'Dipinjam', 'Terlambat'])
                        ->distinct('buku_id')
                        ->count();

                    return [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'info' => $user->siswa ? 'NISN: ' . $user->siswa->nisn . ' - Kelas: ' . $user->siswa->kelas . ' (Sedang pinjam: ' . $jumlahJudulPinjam . '/2 judul)' : 'Data tidak lengkap'
                    ];
                });
        } elseif ($level === 'guru') {
            $anggota = User::where('level', 'guru')
                ->whereNotIn('id', $userIdDenganPeminjamanMaksimal) // Exclude anggota dengan peminjaman maksimal (2 judul buku)
                ->with('guru')
                ->get()
                ->map(function ($user) {
                    // Hitung jumlah judul buku yang sedang dipinjam
                    $jumlahJudulPinjam = PeminjamanModel::where('user_id', $user->id)
                        ->whereIn('status', ['Diproses', 'Dipinjam', 'Terlambat'])
                        ->distinct('buku_id')
                        ->count();

                    return [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'info' => $user->guru ? 'NIP: ' . $user->guru->nip . ' - Mapel: ' . $user->guru->mata_pelajaran . ' (Sedang pinjam: ' . $jumlahJudulPinjam . '/2 judul)' : 'Data tidak lengkap'
                    ];
                });
        } elseif ($level === 'staff') {
            $anggota = User::where('level', 'staff')
                ->whereNotIn('id', $userIdDenganPeminjamanMaksimal) // Exclude anggota dengan peminjaman maksimal (2 judul buku)
                ->with('staff')
                ->get()
                ->map(function ($user) {
                    // Hitung jumlah judul buku yang sedang dipinjam
                    $jumlahJudulPinjam = PeminjamanModel::where('user_id', $user->id)
                        ->whereIn('status', ['Diproses', 'Dipinjam', 'Terlambat'])
                        ->distinct('buku_id')
                        ->count();

                    return [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'info' => $user->staff ? 'NIP: ' . $user->staff->nip . ' - Bagian: ' . $user->staff->bagian . ' (Sedang pinjam: ' . $jumlahJudulPinjam . '/2 judul)' : 'Data tidak lengkap'
                    ];
                });
        }

        return response()->json($anggota);
    }

    /**
     * Menyimpan peminjaman manual yang diinput oleh admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function simpanManual(Request $request)
    {
        // Hanya admin yang bisa akses
        if (Auth::user()->level !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk fitur ini.');
        }

        $messages = [
            'buku_id.required' => 'Buku harus dipilih.',
            'buku_id.exists' => 'Buku tidak ditemukan.',

            'user_level.required' => 'Level anggota harus dipilih.',
            'user_level.in' => 'Level anggota tidak valid.',

            'user_id.required' => 'Anggota harus dipilih.',
            'user_id.exists' => 'Anggota tidak ditemukan.',

            'tanggal_pinjam.required' => 'Tanggal pinjam harus diisi.',
            'tanggal_pinjam.date' => 'Format tanggal pinjam tidak valid.',
            'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam minimal hari ini.',

            'tanggal_kembali.required' => 'Tanggal kembali harus diisi.',
            'tanggal_kembali.date' => 'Format tanggal kembali tidak valid.',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam.',

            'catatan.max' => 'Catatan tidak boleh lebih dari 500 karakter.'
        ];

        $request->validate(
            [
                'buku_id' => 'required|exists:buku,id',
                'user_level' => 'required|in:siswa,guru,staff',
                'user_id' => 'required|exists:users,id',
                'tanggal_pinjam' => 'required|date|after_or_equal:today',
                'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
                'catatan' => 'nullable|string|max:500'
            ],
            $messages
        );

        // Validasi level user sesuai dengan user yang dipilih
        $user = User::findOrFail($request->user_id);
        if ($user->level !== $request->user_level) {
            return redirect()->back()->with('error', 'Level anggota tidak sesuai dengan anggota yang dipilih.')->withInput();
        }

        $buku = BukuModel::findOrFail($request->buku_id);

        // Cek stok buku
        if ($buku->stok_buku <= 0) {
            return redirect()->back()->with('error', 'Stok buku tidak tersedia untuk dipinjam.');
        }

        // Cek apakah user sudah meminjam buku yang sama dan belum dikembalikan
        $sudahPinjam = PeminjamanModel::where('user_id', $request->user_id)
            ->where('buku_id', $request->buku_id)
            ->whereIn('status', ['Dipinjam', 'Terlambat', 'Diproses'])
            ->first();

        if ($sudahPinjam) {
            return redirect()->back()->with('error', 'Anggota sudah meminjam buku ini dan belum mengembalikanannya.');
        }

        // Cek jumlah judul buku yang berbeda yang sedang dipinjam oleh user
        $jumlahJudulPinjam = PeminjamanModel::where('user_id', $request->user_id)
            ->whereIn('status', ['Dipinjam', 'Terlambat', 'Diproses'])
            ->distinct('buku_id')
            ->count();

        // Maksimal 2 judul buku yang berbeda yang boleh dipinjam dalam waktu bersamaan
        if ($jumlahJudulPinjam >= 2) {
            return redirect()->back()->with('error', 'Anggota sudah meminjam 2 judul buku yang berbeda. Silakan kembalikan salah satu buku terlebih dahulu.');
        }

        // Generate nomor peminjaman
        $no_peminjaman = 'PJM-' . date('YmdHis') . '-' . Str::random(2);

        // Buat record peminjaman
        $peminjaman = new PeminjamanModel();
        $peminjaman->user_id = $request->user_id;
        $peminjaman->buku_id = $request->buku_id;
        $peminjaman->no_peminjaman = $no_peminjaman;
        $peminjaman->tanggal_pinjam = $request->tanggal_pinjam;
        $peminjaman->tanggal_kembali = $request->tanggal_kembali;
        $peminjaman->status = 'Diproses';
        $peminjaman->diproses_by = 'admin'; // Set diproses_by untuk peminjaman manual
        $peminjaman->catatan = $request->catatan;
        $peminjaman->save();

        // Kurangi stok buku
        $buku->stok_buku -= 1;

        // Update status buku jika stok habis
        if ($buku->stok_buku <= 0) {
            $buku->status = 'Habis';
        }

        $buku->save();

        // Kirim notifikasi ke anggota tentang peminjaman manual
        $this->kirimNotifikasiPeminjamanManual($peminjaman);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman manual berhasil disimpan untuk anggota: ' . $user->nama);
    }

    // Proses konfirmasi pengambilan buku
    public function konfirmasiPengambilan($id)
    {
        $peminjaman = PeminjamanModel::findOrFail($id);

        // Cek status peminjaman harus dalam status Diproses
        if ($peminjaman->status !== 'Diproses') {
            return redirect()->back()->with('error', 'Status peminjaman tidak valid untuk pengambilan buku.');
        }

        // Update tanggal pinjam menjadi saat ini
        $peminjaman->tanggal_pinjam = now();
        $peminjaman->status = 'Dipinjam';
        $peminjaman->save();

        // Redirect berdasarkan level pengguna
        if (Auth::user()->level === 'admin') {
            return redirect()->route('peminjaman.index')->with('success', 'Konfirmasi pengambilan buku berhasil.');
        } else {
            return redirect()->route('peminjaman.index', $peminjaman->id)->with('success', 'Konfirmasi pengambilan buku berhasil.');
        }
    }

    /**
     * Helper method untuk memvalidasi format tanggal untuk filter data tanggal
     */
    private function validateDate($date)
    {
        return !empty($date) && is_string($date) && strtotime($date) !== false;
    }
}
