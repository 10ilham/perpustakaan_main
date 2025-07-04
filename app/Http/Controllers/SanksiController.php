<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanksiModel;
use App\Models\PeminjamanModel;
use App\Models\BukuModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SanksiController extends Controller
{
    public function prosesPengembalian(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_buku' => 'required|in:baik,rusak_hilang',
            'denda_keterlambatan' => 'required|numeric|min:0',
            'denda_kerusakan' => 'required|numeric|min:0',
            'total_denda' => 'required|numeric|min:0',
            'harga_buku' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();

        // Ambil data peminjaman
        $peminjaman = PeminjamanModel::with('buku')->findOrFail($request->peminjaman_id);

        // Cek apakah sudah ada sanksi untuk peminjaman ini
        $existingSanksi = SanksiModel::where('peminjaman_id', $request->peminjaman_id)->first();
        if ($existingSanksi) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Sanksi untuk peminjaman ini sudah ada.'
            ]);
        }

        // Cek apakah peminjaman sudah dikembalikan
        if ($peminjaman->status == 'Dikembalikan') {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman ini sudah dikembalikan sebelumnya.'
            ]);
        }

        // HITUNG HARI TERLAMBAT YANG SEBENARNYA - konsisten dengan PeminjamanController
        $tanggalKembaliBatas = Carbon::parse($peminjaman->tanggal_kembali)->endOfDay(); // Sampai akhir hari batas
        $tanggalPengembalianSekarang = Carbon::now(); // Waktu pengembalian sekarang

        // Gunakan logika yang sama dengan PeminjamanController: hanya terlambat jika melewati endOfDay
        $hariTerlambatAktual = 0;
        if ($tanggalPengembalianSekarang->greaterThan($tanggalKembaliBatas)) {
            // Hitung selisih hari dengan cara yang sama seperti di PeminjamanController
            $hariTerlambatAktual = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay()
                ->diffInDays($tanggalPengembalianSekarang->copy()->startOfDay(), false);

            // Jika hasil negatif, ubah menjadi positif
            $hariTerlambatAktual = abs($hariTerlambatAktual);
        }

        // Hitung ulang denda berdasarkan kondisi dan hari terlambat yang benar
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

        // Buat sanksi jika ada denda
        if ($totalDenda > 0) {
            $jenisSanksi = [];

            // Logika sanksi berdasarkan ketentuan:
            // 1. Tidak terlambat + rusak/hilang = sanksi harga buku
            // 2. Terlambat + tidak rusak = sanksi keterlambatan
            // 3. Terlambat + rusak/hilang = sanksi harga buku (tanpa keterlambatan)

            if ($hariTerlambatAktual > 0) {
                if ($request->kondisi_buku === 'baik') {
                    // Terlambat + tidak rusak = hanya denda keterlambatan
                    $jenisSanksi[] = 'keterlambatan';
                } else if ($request->kondisi_buku === 'rusak_hilang') {
                    // Terlambat + rusak/hilang = hanya denda kerusakan (hapus keterlambatan)
                    $jenisSanksi[] = 'rusak_parah';
                }
            } else {
                if ($request->kondisi_buku === 'rusak_hilang') {
                    // Tidak terlambat + rusak/hilang = denda kerusakan
                    $jenisSanksi[] = 'rusak_parah';
                }
                // Tidak terlambat + tidak rusak = tidak ada sanksi
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

        // Update field is_terlambat dan jumlah_hari_terlambat di peminjaman
        $peminjaman->is_terlambat = $hariTerlambatAktual > 0;
        $peminjaman->jumlah_hari_terlambat = $hariTerlambatAktual;

        // Update status peminjaman menjadi Dikembalikan
        $peminjaman->status = 'Dikembalikan';
        $peminjaman->tanggal_pengembalian = Carbon::now();
        $peminjaman->save();

        // Update stok buku (kembalikan stok) hanya jika belum dikembalikan
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

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian berhasil diproses.' . (isset($totalDenda) && $totalDenda > 0 ? ' Denda: Rp ' . number_format($totalDenda) : ' Tidak ada denda.')
        ]);
    }

    public function index()
    {
        $query = SanksiModel::with(['peminjaman.buku', 'peminjaman.user'])
            ->orderBy('created_at', 'desc');

        // Jika bukan admin, hanya tampilkan sanksi user yang sedang login
        if (Auth::user()->level !== 'admin') {
            $query->whereHas('peminjaman', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $sanksi = $query->get();

        return view('sanksi.index', compact('sanksi'));
    }

    public function bayar(Request $request, $id)
    {
        $sanksi = SanksiModel::findOrFail($id);

        $sanksi->status_bayar = 'sudah_bayar';
        $sanksi->save();

        return redirect()->back()->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
    }
}
