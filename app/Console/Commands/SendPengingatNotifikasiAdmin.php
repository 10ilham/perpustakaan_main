<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanModel;
use App\Models\User;
use App\Notifications\PeminjamanBukuAdminNotification;
use Carbon\Carbon;

class SendPengingatNotifikasiAdmin extends Command
{
    // Terhubung ke scheduler di routes/console.php (Laravel 12 sudah tidak menggunakan kernel untuk definisi schedule)
    // php artisan app:send-pengingat-notifikasi-admin (untuk mencoba menjalankan notifikasi di terminal)
    protected $signature = 'app:send-pengingat-notifikasi-admin {minutes=1 : Jangka waktu (dalam menit) untuk memperoleh peminjaman terbaru}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim notifikasi ke admin secara otomatis saat ada peminjaman buku baru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = (int) $this->argument('minutes');

        // Tentukan waktu untuk mencari peminjaman baru (default: 1 menit terakhir)
        $startTime = Carbon::now()->subMinutes($minutes);

        // Cari peminjaman baru yang dibuat dalam jangka waktu tertentu
        $peminjamanBaru = PeminjamanModel::with(['user', 'buku'])
            ->where('created_at', '>=', $startTime)
            ->where('status', 'Dipinjam')
            ->get();

        if ($peminjamanBaru->isEmpty()) {
            $this->info("Tidak ada peminjaman baru dalam {$minutes} menit terakhir.");
            return 0;
        }

        $this->info("Ditemukan " . $peminjamanBaru->count() . " peminjaman baru untuk dikirimkan notifikasi:");

        // Dapatkan semua admin
        $admin = User::whereIn('level', ['admin'])->get();

        if ($admin->isEmpty()) {
            $this->error("Tidak ada admin yang ditemukan dalam sistem!");
            return 1;
        }

        $totalSent = 0;

        // Untuk setiap peminjaman baru, kirim notifikasi ke semua admin
        foreach ($peminjamanBaru as $peminjaman) {
            $this->line("Mengirim notifikasi untuk peminjaman ID: " . $peminjaman->id);
            $this->line("Buku: " . $peminjaman->buku->judul);
            $this->line("Peminjam: " . $peminjaman->user->nama);

            foreach ($admin as $user) {
                // Kirim notifikasi ke masing-masing admin
                $user->notify(new PeminjamanBukuAdminNotification($peminjaman));
                $this->line("✓ Notifikasi berhasil dikirim ke {$user->nama} ({$user->email})");
                $totalSent++;
            }
        }

        $this->info("Total {$totalSent} notifikasi berhasil dikirim ke admin.");
        return 0;
    }
}
