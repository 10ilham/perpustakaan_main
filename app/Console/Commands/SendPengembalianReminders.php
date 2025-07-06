<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanModel;
use Carbon\Carbon;
use App\Notifications\PengembalianBukuNotification;

class SendPengembalianReminders extends Command
{
    // Terhubung ke scheduler di routes/console.php (Laravel 12 sudah tidak menggunakan kernel untuk definisi schedule)
    // php artisan app:send-pengembalian-reminders (untuk mencoba menjalankan notifikasi di terminal)
    protected $signature = 'app:send-pengembalian-reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mengambil tanggal hari ini tanpa waktu
        $today = Carbon::today()->format('Y-m-d');

        // Mencari peminjaman yang:
        // 1. Batas pengembaliannya hari ini (tanggal_kembali = today)
        // 2. Sudah terlambat (tanggal_kembali < today)
        // Dan statusnya masih Dipinjam atau Terlambat
        $peminjamans = PeminjamanModel::with(['user', 'buku'])
            ->where('tanggal_kembali', '<=', $today) // Hari ini atau sudah lewat
            ->whereIn('status', ['Dipinjam', 'Terlambat'])
            ->get();

        // Pisahkan data untuk logging yang lebih detail
        $peminjamanHariIni = $peminjamans->where('tanggal_kembali', $today)->count();
        $peminjamanTerlambat = $peminjamans->where('tanggal_kembali', '<', $today)->count();

        $this->info("Ditemukan {$peminjamans->count()} peminjaman yang memerlukan notifikasi:");
        $this->info("- Batas pengembalian hari ini: {$peminjamanHariIni}");
        $this->info("- Sudah terlambat: {$peminjamanTerlambat}");

        // Jika tidak ada peminjaman yang memerlukan notifikasi, selesai
        if ($peminjamans->count() == 0) {
            $this->info('Tidak ada notifikasi yang dikirim.');
            return;
        }

        $sentCount = 0;

        // Kirim notifikasi ke setiap peminjam
        foreach ($peminjamans as $peminjaman) {
            try {
                // Pastikan user ada dan memiliki email
                if ($peminjaman->user && $peminjaman->user->email) {
                    // Tentukan jenis notifikasi berdasarkan tanggal
                    $hariTerlambat = Carbon::parse($peminjaman->tanggal_kembali)->diffInDays($today, false);
                    $jenisNotifikasi = $hariTerlambat > 0 ? 'terlambat' : 'reminder';

                    $peminjaman->user->notify(new PengembalianBukuNotification($peminjaman));
                    $sentCount++;

                    if ($hariTerlambat > 0) {
                        $this->info("Notifikasi TERLAMBAT ({$hariTerlambat} hari) dikirim ke {$peminjaman->user->nama} ({$peminjaman->user->email}) untuk buku '{$peminjaman->buku->judul}'");
                    } else {
                        $this->info("Notifikasi REMINDER dikirim ke {$peminjaman->user->nama} ({$peminjaman->user->email}) untuk buku '{$peminjaman->buku->judul}'");
                    }
                } else {
                    $this->error("Gagal mengirim notifikasi: User atau email tidak ditemukan untuk peminjaman ID {$peminjaman->id}");
                }
            } catch (\Exception $e) {
                $this->error("Gagal mengirim notifikasi untuk peminjaman ID {$peminjaman->id}: " . $e->getMessage());
            }
        }

        $this->info("Total notifikasi yang dikirim: {$sentCount}");
    }
}
