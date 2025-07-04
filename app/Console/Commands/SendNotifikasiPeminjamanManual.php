<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanModel;
use App\Models\User;
use App\Notifications\PeminjamanManualNotification;
use Carbon\Carbon;

// untuk keperluan test notifikasi email peminjaman manual
class SendNotifikasiPeminjamanManual extends Command
{
    // Terhubung ke scheduler di routes/console.php (Laravel 12 sudah tidak menggunakan kernel untuk definisi schedule)
    // php artisan app:send-notifikasi-peminjaman-manual (untuk mencoba menjalankan notifikasi di terminal)
    protected $signature = 'app:send-notifikasi-peminjaman-manual {minutes=10 : Jangka waktu (dalam menit) untuk memperoleh peminjaman manual terbaru}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim notifikasi ke anggota secara otomatis saat ada peminjaman buku manual oleh admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = (int) $this->argument('minutes');

        // Tentukan waktu untuk mencari peminjaman baru (default: 10 menit terakhir)
        $startTime = Carbon::now()->subMinutes($minutes);

        // Cari peminjaman baru manual yang dibuat dalam jangka waktu tertentu
        // Kita mengasumsikan peminjaman manual adalah yang dibuat oleh admin dan tersimpan dalam database
        $peminjamanManual = PeminjamanModel::with(['user', 'buku'])
            ->where('created_at', '>=', $startTime)
            ->where('status', 'Dipinjam')
            ->get();

        if ($peminjamanManual->isEmpty()) {
            $this->info("Tidak ada peminjaman manual baru dalam {$minutes} menit terakhir.");
            return 0;
        }

        $this->info("Ditemukan " . $peminjamanManual->count() . " peminjaman manual baru untuk dikirimkan notifikasi:");

        $totalSent = 0;

        // Untuk setiap peminjaman manual baru, kirim notifikasi ke anggota yang terkait
        foreach ($peminjamanManual as $peminjaman) {
            // Dapatkan user (anggota) yang terkait dengan peminjaman
            $anggota = User::find($peminjaman->user_id);

            if (!$anggota) {
                $this->error("Anggota dengan ID {$peminjaman->user_id} tidak ditemukan untuk peminjaman ID: {$peminjaman->id}");
                continue;
            }

            $this->line("Mengirim notifikasi untuk peminjaman ID: " . $peminjaman->id);
            $this->line("Buku: " . $peminjaman->buku->judul);
            $this->line("Peminjam: " . $anggota->nama);

            // Kirim notifikasi ke anggota
            $anggota->notify(new PeminjamanManualNotification($peminjaman));
            $this->line("✓ Notifikasi berhasil dikirim ke {$anggota->nama} ({$anggota->email})");
            $totalSent++;
        }

        $this->info("Total {$totalSent} notifikasi peminjaman manual berhasil dikirim ke anggota.");
        return 0;
    }
}
