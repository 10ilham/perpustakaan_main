<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    // Method untuk mengecek apakah user sedang di-blacklist
    public static function isUserBlacklisted($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->where('blacklist_expires_at', '>', Carbon::now())
            ->exists();
    }

    // Method untuk menambah counter pembatalan booking
    public static function incrementCancelledBookings($userId)
    {
        $blacklist = self::firstOrCreate(
            ['user_id' => $userId],
            ['cancelled_bookings_count' => 0]
        );

        $blacklist->increment('cancelled_bookings_count');

        // Jika sudah 3 kali, blacklist selama 7 hari
        if ($blacklist->cancelled_bookings_count >= 3) {
            $blacklist->update([
                'is_active' => true,
                'blacklisted_at' => Carbon::now(),
                'blacklist_expires_at' => Carbon::now()->addDays(7)
            ]);
        }

        return $blacklist;
    }
}
