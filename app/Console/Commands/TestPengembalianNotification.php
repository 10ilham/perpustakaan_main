<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanModel;
use App\Models\User;
use App\Notifications\PengembalianBukuNotification;

class TestPengembalianNotification extends Command
{
    // Terhubung ke scheduler di routes/console.php (Laravel 12 sudah tidak menggunakan kernel untuk definisi schedule)
    // php artisan app:test-notification (tambahkan user_id jika ingin mengirim notifikasi ke user tertentu) (untuk mencoba menjalankan notifikasi di terminal)
    protected $signature = 'app:test-notification {user_id? : ID user yang akan dikirimkan notifikasi}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            // Jika user_id diberikan, cari user tersebut
            $user = User::find($userId);

            if (!$user) {
                $this->error("User dengan ID {$userId} tidak ditemukan!");
                return 1;
            }

            // Cari peminjaman aktif dari user
            $peminjaman = PeminjamanModel::with(['user', 'buku'])
                ->where('user_id', $userId)
                ->whereIn('status', ['Dipinjam', 'Terlambat'])
                ->first();

            if (!$peminjaman) {
                $this->error("Tidak ada peminjaman aktif untuk user {$user->nama}!");
                return 1;
            }

            // Kirim notifikasi
            $user->notify(new PengembalianBukuNotification($peminjaman));
            $this->info("Notifikasi berhasil dikirim ke {$user->nama} ({$user->email}) untuk buku '{$peminjaman->buku->judul}'");
        } else {
            // Jika tidak didefinisikan user_id pada terminal, kirim notifikasi ke semua user dengan peminjaman aktif
            $this->info("Daftar user dengan peminjaman aktif:");

            $peminjamans = PeminjamanModel::with(['user', 'buku'])
                ->whereIn('status', ['Dipinjam', 'Terlambat'])
                ->get();

            $tableData = [];
            $sentCount = 0;

            foreach ($peminjamans as $peminjaman) {
                if ($peminjaman->user) {
                    $tableData[] = [
                        'id' => $peminjaman->user_id,
                        'nama' => $peminjaman->user->nama,
                        'email' => $peminjaman->user->email,
                        'buku' => $peminjaman->buku->judul,
                        'status' => $peminjaman->status
                    ];

                    // Kirim notifikasi ke setiap user dengan peminjaman aktif
                    $peminjaman->user->notify(new PengembalianBukuNotification($peminjaman));
                    $this->line("✓ Notifikasi berhasil dikirim ke {$peminjaman->user->nama} ({$peminjaman->user->email}) untuk buku '{$peminjaman->buku->judul}'");
                    $sentCount++;
                }
            }

            if (count($tableData) > 0) {
                $this->table(
                    ['ID', 'Nama', 'Email', 'Buku', 'Status'],
                    $tableData
                );

                $this->info("Total notifikasi terkirim: {$sentCount}");
            } else {
                $this->info("Tidak ada peminjaman aktif saat ini.");
            }
        }

        return 0;
    }
}
