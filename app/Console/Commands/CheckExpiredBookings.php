<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanModel;
use App\Models\UserBlacklistModel;
use App\Models\BukuModel;
use App\Notifications\BookingCancellationWarning;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and cancel expired bookings that are not picked up within school hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired bookings...');

        // Ambil semua peminjaman dengan status 'Diproses' yang sudah melewati batas waktu
        $expiredBookings = PeminjamanModel::where('status', 'Diproses')
            ->where('booking_expired_at', '<=', Carbon::now())
            ->with(['user', 'buku'])
            ->get();

        $cancelledCount = 0;

        foreach ($expiredBookings as $booking) {
            try {
                // Update status menjadi dibatalkan
                $booking->update([
                    'status' => 'Dibatalkan',
                    'is_auto_cancelled' => true
                ]);

                // Kembalikan stok buku
                $buku = $booking->buku;
                $buku->increment('stok_buku');

                // Update status buku jika stok > 0
                if ($buku->stok_buku > 0) {
                    $buku->update(['status' => 'Tersedia']);
                }

                // Tambah counter pembatalan untuk user dan cek blacklist
                $blacklistRecord = UserBlacklistModel::incrementCancelledBookings($booking->user_id);

                // Kirim email peringatan
                $this->sendWarningEmail($booking, $blacklistRecord);

                $cancelledCount++;

                $this->info("Cancelled booking #{$booking->no_peminjaman} for user {$booking->user->nama}");
            } catch (\Exception $e) {
                $this->error("Failed to cancel booking #{$booking->no_peminjaman}: " . $e->getMessage());
                Log::error('Failed to cancel expired booking', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Total expired bookings cancelled: {$cancelledCount}");

        return Command::SUCCESS;
    }

    private function sendWarningEmail($booking, $blacklistRecord)
    {
        try {
            $isBlacklisted = (bool) $blacklistRecord->is_active; // Cast to bool
            $cancelledCount = $blacklistRecord->cancelled_bookings_count;

            // Menggunakan Laravel Notifications
            $booking->user->notify(new BookingCancellationWarning($booking, $cancelledCount, $isBlacklisted));

            $this->info("Warning notification sent to {$booking->user->email}");
        } catch (\Exception $e) {
            $this->error("Failed to send notification to {$booking->user->email}: " . $e->getMessage());
            Log::error('Failed to send booking cancellation notification', [
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
