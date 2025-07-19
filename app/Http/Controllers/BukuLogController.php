<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuLogModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BukuLogController extends Controller
{
    /**
     * Display book logs for both incoming and outgoing books
     */
    public function index(Request $request)
    {
        // Set default filter to 'semua' if not provided
        $filter = $request->filter ?? 'semua';

        // Start query builder
        $query = BukuLogModel::with('admin');

        // Apply filter
        if ($filter == 'masuk') {
            $query->where('tipe', 'masuk');
        } elseif ($filter == 'keluar') {
            $query->where('tipe', 'keluar');
        }

        // Apply date range filter if provided
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        // Get results with pagination
        $logs = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('laporan.buku_log', compact('logs', 'filter'));
    }

    /**
     * Delete a book log entry
     */
    public function destroy($id)
    {
        try {
            $log = BukuLogModel::findOrFail($id);
            $log->delete();

            return redirect()->route('laporan.buku_log')
                ->with('success', 'Log buku berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('laporan.buku_log')
                ->with('error', 'Gagal menghapus log buku: ' . $e->getMessage());
        }
    }
}
