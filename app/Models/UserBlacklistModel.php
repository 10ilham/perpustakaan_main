<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserBlacklistModel extends Model
{
    use HasFactory;

    protected $table = 'user_blacklist';

    protected $fillable = [
        'user_id',
        'cancelled_bookings_count',
        'blacklisted_at',
        'blacklist_expires_at',
        'is_active'
    ];

    protected $casts = [
        'blacklisted_at' => 'datetime',
        'blacklist_expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk blacklist aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('blacklist_expires_at', '>', Carbon::now());
    }

    // Boot method untuk mengupdate status blacklist otomatis saat diakses
    protected static function boot()
    {
        parent::boot();

        // Event retrieved: dipanggil setiap kali model ini diambil dari database
        static::retrieved(function ($blacklist) {
            // Perbarui status aktif berdasarkan waktu saat ini
            if ($blacklist->is_active && $blacklist->blacklist_expires_at <= Carbon::now()) {
                $blacklist->is_active = false;
                $blacklist->save();
            }
        });
    }

    // Method untuk mengecek apakah user sedang di-blacklist
    public static function isUserBlacklisted($userId)
    {
        // Otomatis update status blacklist terkini
        self::updateBlacklistStatus($userId);

        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->where('blacklist_expires_at', '>', Carbon::now())
            ->exists();
    }

    // Method untuk menambah counter pembatalan booking
    public static function incrementCancelledBookings($userId)
    {
        return self::updateBlacklistStatus($userId);
    }

    // Method yang dipanggil otomatis ketika ada pembatalan baru
    public static function processNewCancellation($userId)
    {
        // Langsung update status blacklist - sistem akan otomatis mendeteksi
        // apakah perlu membuat periode blacklist baru
        $blacklist = self::updateBlacklistStatus($userId);

        if ($blacklist && $blacklist->is_active) {
            // Log atau notifikasi jika diperlukan
            Log::info("User {$userId} telah diblacklist sampai {$blacklist->blacklist_expires_at}");
        }

        return $blacklist;
    }

    // Method umum untuk memperbarui status blacklist berdasarkan jumlah pembatalan terkini
    public static function updateBlacklistStatus($userId)
    {
        // Cek dan reset blacklist yang sudah expired secara otomatis
        self::checkAndResetExpiredBlacklists();

        // Cek apakah ada blacklist aktif saat ini
        $activeBlacklist = self::where('user_id', $userId)
            ->where('is_active', true)
            ->where('blacklist_expires_at', '>', Carbon::now())
            ->first();

        // Jika sudah ada blacklist aktif, tidak perlu buat lagi
        if ($activeBlacklist) {
            return $activeBlacklist;
        }

        // Hitung total pembatalan untuk user ini
        $totalCancellations = \App\Models\PeminjamanModel::where('user_id', $userId)
            ->where('status', 'Dibatalkan')
            ->count();

        // Hitung berapa banyak blacklist yang seharusnya ada
        $validBlacklistCount = floor($totalCancellations / 3);

        // Hitung berapa blacklist yang sudah ada
        $existingBlacklistCount = self::where('user_id', $userId)->count();

        // Jika sudah ada jumlah blacklist yang sesuai atau lebih, tidak perlu buat lagi
        if ($existingBlacklistCount >= $validBlacklistCount) {
            // Bersihkan blacklist berlebihan jika ada
            if ($existingBlacklistCount > $validBlacklistCount) {
                self::autoCleanupInvalidBlacklists($userId);
            }
            return null;
        }

        // Ambil blacklist terakhir untuk menentukan kapan mulai hitung pembatalan baru
        $lastBlacklist = self::where('user_id', $userId)
            ->orderBy('blacklist_expires_at', 'desc')
            ->first();

        // Tentukan dari kapan mulai hitung pembatalan
        $countFromDate = $lastBlacklist
            ? $lastBlacklist->blacklist_expires_at
            : Carbon::createFromDate(1970, 1, 1);

        // Hitung pembatalan setelah periode blacklist terakhir berakhir
        $newCancellations = \App\Models\PeminjamanModel::where('user_id', $userId)
            ->where('status', 'Dibatalkan')
            ->where('updated_at', '>', $countFromDate)
            ->orderBy('updated_at', 'asc')
            ->get();

        // Jika pembatalan baru mencapai 3, buat blacklist baru
        if ($newCancellations->count() >= 3) {
            // Ambil pembatalan ke-3 sebagai trigger blacklist
            $thirdCancellation = $newCancellations->get(2);

            // Buat blacklist baru
            $newBlacklist = self::create([
                'user_id' => $userId,
                'cancelled_bookings_count' => 3,
                'is_active' => true,
                'blacklisted_at' => $thirdCancellation->updated_at,
                'blacklist_expires_at' => Carbon::parse($thirdCancellation->updated_at)->addDays(7)
            ]);

            return $newBlacklist;
        }

        return null;
    }

    // Method untuk mengecek dan reset blacklist yang sudah expired secara otomatis
    public static function checkAndResetExpiredBlacklists()
    {
        $now = Carbon::now();
        $expiredBlacklists = self::where('is_active', true)
            ->where('blacklist_expires_at', '<=', $now)
            ->get();

        foreach ($expiredBlacklists as $blacklist) {
            $blacklist->update(['is_active' => false]);
        }

        return $expiredBlacklists->count();
    }

    // Method untuk otomatis membersihkan data blacklist yang tidak valid
    public static function autoCleanupInvalidBlacklists($userId = null)
    {
        // Langkah 1: Hapus duplikat berdasarkan user_id dan blacklisted_at yang sama
        $baseQuery = DB::table('user_blacklist');
        if ($userId) {
            $baseQuery->where('user_id', $userId);
        }

        $duplicates = $baseQuery->select('user_id', 'blacklisted_at', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id', 'blacklisted_at')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = self::where('user_id', $duplicate->user_id)
                ->where('blacklisted_at', $duplicate->blacklisted_at)
                ->orderBy('id', 'asc')
                ->pluck('id');

            // Simpan ID pertama, hapus sisanya
            $toDelete = $ids->slice(1);

            if ($toDelete->isNotEmpty()) {
                self::whereIn('id', $toDelete)->delete();
            }
        }

        // Langkah 2: Pastikan setiap user hanya memiliki entri blacklist yang valid
        // berdasarkan pembatalan aktual di tabel peminjaman
        $userIds = $userId ? [$userId] : self::distinct('user_id')->pluck('user_id')->toArray();

        foreach ($userIds as $uId) {
            // Dapatkan semua pembatalan user
            $cancellations = \App\Models\PeminjamanModel::where('user_id', $uId)
                ->where('status', 'Dibatalkan')
                ->orderBy('updated_at', 'asc')
                ->get();

            // Hitung berapa set pembatalan 3 yang valid
            $cancellationCount = $cancellations->count();
            $validBlacklistCount = floor($cancellationCount / 3);

            // Dapatkan semua blacklist untuk user ini
            $blacklists = self::where('user_id', $uId)
                ->orderBy('blacklisted_at', 'asc')
                ->get();

            // Jika jumlah blacklist lebih dari yang seharusnya, hapus yang berlebih
            if ($blacklists->count() > $validBlacklistCount) {
                // Simpan ID blacklist yang akan dipertahankan
                $keepIds = $blacklists->take($validBlacklistCount)->pluck('id')->toArray();

                // Hapus semua blacklist yang tidak valid
                if (count($keepIds) > 0) {
                    self::where('user_id', $uId)
                        ->whereNotIn('id', $keepIds)
                        ->delete();
                } else if ($validBlacklistCount == 0) {
                    // Jika tidak ada blacklist yang valid, hapus semua
                    self::where('user_id', $uId)->delete();
                }
            }
        }

        // Langkah 3: Perbaiki counter yang salah (seharusnya selalu 3)
        $query = self::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $query->where('cancelled_bookings_count', '!=', 3)->update(['cancelled_bookings_count' => 3]);

        // Langkah 4: Update status aktif berdasarkan tanggal expire
        $now = Carbon::now();
        $query = self::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Status aktif harus true jika tanggal expire > now
        $query->where('blacklist_expires_at', '>', $now)
            ->where('is_active', false)
            ->update(['is_active' => true]);

        // Status aktif harus false jika tanggal expire <= now
        $query->where('blacklist_expires_at', '<=', $now)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return true;
    }

    // Method untuk mendapatkan statistik blacklist user
    public static function getUserBlacklistStats($userId)
    {
        $totalCancellations = \App\Models\PeminjamanModel::where('user_id', $userId)
            ->where('status', 'Dibatalkan')
            ->count();

        $totalBlacklists = self::where('user_id', $userId)->count();
        $activeBlacklist = self::where('user_id', $userId)
            ->where('is_active', true)
            ->where('blacklist_expires_at', '>', Carbon::now())
            ->first();

        return [
            'total_cancellations' => $totalCancellations,
            'total_blacklist_periods' => $totalBlacklists,
            'active_blacklist' => $activeBlacklist,
            'is_currently_blacklisted' => $activeBlacklist ? true : false
        ];
    }

    // Method untuk menghapus blacklist dan mereset counter pembatalan
    public static function resetCancelledBookingsCounter($userId)
    {
        // Hapus semua data blacklist user
        self::where('user_id', $userId)->delete();

        return true;
    }
}
