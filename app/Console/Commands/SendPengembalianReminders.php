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

        // Mencari peminjaman dengan batas waktu pengembalian hari ini dan belum dikembalikan
        $peminjamans = PeminjamanModel::with(['user', 'buku'])
            ->where('tanggal_kembali', $today)
            ->whereIn('status', ['Dipinjam', 'Terlambat'])  // Termasuk status Dipinjam dan Terlambat
            ->get();

        $this->info('Ditemukan ' . $peminjamans->count() . ' peminjaman dengan batas waktu pengembalian hari ini.');

        // Jika tidak ada peminjaman dengan batas waktu hari ini, selesai
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
                    $peminjaman->user->notify(new PengembalianBukuNotification($peminjaman));
                    $sentCount++;

                    $this->info("Notifikasi berhasil dikirim ke {$peminjaman->user->nama} ({$peminjaman->user->email}) untuk buku '{$peminjaman->buku->judul}'");
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
