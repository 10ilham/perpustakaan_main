<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanksiModel;
use App\Models\PeminjamanModel;
use App\Models\BukuModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SanksiController extends Controller
{
    // ELOQUENT - Memproses pengembalian buku dan penghitungan sanksi
    public function prosesPengembalian(Request $request)
    {
        $messages = [
            'peminjaman_id.required' => 'ID peminjaman harus diisi.',
            'peminjaman_id.exists' => 'Peminjaman tidak ditemukan.',

            'kondisi_buku.required' => 'Kondisi buku harus diisi.',
            'kondisi_buku.in' => 'Kondisi buku harus baik atau rusak_hilang.',

            'denda_keterlambatan.required' => 'Denda keterlambatan harus diisi.',
            'denda_keterlambatan.numeric' => 'Denda keterlambatan harus berupa angka.',
            'denda_keterlambatan.min' => 'Denda keterlambatan tidak boleh kurang dari 0.',

            'denda_kerusakan.required' => 'Denda kerusakan harus diisi.',
            'denda_kerusakan.numeric' => 'Denda kerusakan harus berupa angka.',
            'denda_kerusakan.min' => 'Denda kerusakan tidak boleh kurang dari 0.',

            'total_denda.required' => 'Total denda harus diisi.',
            'total_denda.numeric' => 'Total denda harus berupa angka.',
            'total_denda.min' => 'Total denda tidak boleh kurang dari 0.',

            'harga_buku.required' => 'Harga buku harus diisi.',
            'harga_buku.numeric' => 'Harga buku harus berupa angka.',
            'harga_buku.min' => 'Harga buku tidak boleh kurang dari 0.',
            
            'keterangan.string' => 'Keterangan harus berupa string.'
        ];
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_buku' => 'required|in:baik,rusak_hilang',
            'denda_keterlambatan' => 'required|numeric|min:0',
            'denda_kerusakan' => 'required|numeric|min:0',
            'total_denda' => 'required|numeric|min:0',
            'harga_buku' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ], $messages);

        // ELOQUENT - Ambil data peminjaman
        $peminjaman = PeminjamanModel::with('buku')->findOrFail($request->peminjaman_id);

        // ELOQUENT - Cek apakah sudah ada sanksi untuk peminjaman ini
        $existingSanksi = SanksiModel::where('peminjaman_id', $request->peminjaman_id)->first();
        if ($existingSanksi) {
            return response()->json([
                'success' => false,
                'message' => 'Sanksi untuk peminjaman ini sudah ada.'
            ]);
        }

        // Cek apakah peminjaman sudah dikembalikan
        if ($peminjaman->status == 'Dikembalikan') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman ini sudah dikembalikan sebelumnya.'
            ]);
        }

        // Hitung hari terlambat yang sebenarnya - konsisten dengan PeminjamanController
        $tanggalKembaliBatas = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay();
        $tanggalPengembalianSekarang = Carbon::now();

        // Gunakan logika yang sama dengan PeminjamanController
        $hariTerlambatAktual = 0;
        if ($tanggalPengembalianSekarang->greaterThan($tanggalKembaliBatas)) {
            $hariTerlambatAktual = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay()
                ->diffInDays($tanggalPengembalianSekarang->copy()->startOfDay(), false);
            $hariTerlambatAktual = abs($hariTerlambatAktual);
        }

        // Hitung ulang denda berdasarkan kondisi dan hari terlambat
        $dendaKeterlambatan = 0;
        $dendaKerusakan = 0;
        $dendaPerHari = 1000;
        $hargaBuku = $peminjaman->buku->harga_buku ?? 0;

        if ($hariTerlambatAktual > 0 && $request->kondisi_buku === 'baik') {
            $dendaKeterlambatan = $hariTerlambatAktual * $dendaPerHari;
        } elseif ($request->kondisi_buku === 'rusak_hilang') {
            $dendaKerusakan = $hargaBuku;
        }

        $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

        // ELOQUENT - Buat sanksi jika ada denda
        if ($totalDenda > 0) {
            $jenisSanksi = [];

            if ($hariTerlambatAktual > 0) {
                if ($request->kondisi_buku === 'baik') {
                    $jenisSanksi[] = 'keterlambatan';
                } else if ($request->kondisi_buku === 'rusak_hilang') {
                    $jenisSanksi[] = 'rusak_hilang';
                }
            } else {
                if ($request->kondisi_buku === 'rusak_hilang') {
                    $jenisSanksi[] = 'rusak_hilang';
                }
            }

            SanksiModel::create([
                'peminjaman_id' => $request->peminjaman_id,
                'jenis_sanksi' => implode(',', $jenisSanksi),
                'hari_terlambat' => $hariTerlambatAktual,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'denda_kerusakan' => $dendaKerusakan,
                'total_denda' => $totalDenda,
                'status_bayar' => 'belum_bayar',
                'keterangan' => $request->keterangan
            ]);
        }

        // ELOQUENT - Update field is_terlambat dan jumlah_hari_terlambat di peminjaman
        $peminjaman->is_terlambat = $hariTerlambatAktual > 0;
        $peminjaman->jumlah_hari_terlambat = $hariTerlambatAktual;

        // Update status peminjaman menjadi Dikembalikan
        $peminjaman->status = 'Dikembalikan';
        $peminjaman->tanggal_pengembalian = Carbon::now();
        $peminjaman->save();

        // ELOQUENT - Update stok buku (kembalikan stok) hanya jika belum dikembalikan
        if (!$peminjaman->is_stok_returned) {
            $buku = $peminjaman->buku;
            $buku->stok_buku += 1;

            // Update status buku berdasarkan stok
            if ($buku->stok_buku > 0) {
                $buku->status = 'Tersedia';
            }
            $buku->save();

            // Tandai bahwa stok sudah dikembalikan
            $peminjaman->is_stok_returned = true;
            $peminjaman->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian berhasil diproses.' . (isset($totalDenda) && $totalDenda > 0 ? ' Denda: Rp ' . number_format($totalDenda) : ' Tidak ada denda.')
        ]);
    }

    // ELOQUENT - Menampilkan daftar sanksi
    public function index()
    {
        $query = SanksiModel::with(['peminjaman.buku', 'peminjaman.user'])
            ->orderBy('created_at', 'asc');

        // Jika bukan admin, hanya tampilkan sanksi user yang sedang login
        if (Auth::user()->level !== 'admin') {
            $query->whereHas('peminjaman', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $sanksi = $query->get();

        return view('sanksi.index', compact('sanksi'));
    }

    // ELOQUENT - Mengonfirmasi pembayaran denda
    public function bayar(Request $request, $id)
    {
        $sanksi = SanksiModel::findOrFail($id);

        $sanksi->status_bayar = 'sudah_bayar';
        $sanksi->save();

        return redirect()->back()->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
    }
}
