<?php

namespace App\Models;

// Menggunakan traits dan kelas dari framework - Konsep OOP: Reusability (menggunakan kembali kode yang sudah ada di laravel)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Mengimpor model-model terkait - Konsep OOP: Dependency
use App\Models\BukuModel;
use App\Models\User;

/**
 * Kelas PeminjamanModel - Representasi transaksi peminjaman buku di perpustakaan
 * Konsep OOP: Inheritance (Pewarisan) - Kelas ini mewarisi kelas Model dari Laravel
 */
class PeminjamanModel extends Model // Konsep OOP: Inheritance - mewarisi sifat dan metode dari kelas Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan dalam database
     * Konsep OOP: Encapsulation - Menggunakan modifier protected untuk membatasi akses
     */
    protected $table = 'peminjaman'; // protected - hanya dapat diakses oleh kelas ini dan turunannya

    /**
     * Atribut yang dapat diisi secara massal (mass assignment)
     * Konsep OOP: Encapsulation - Melindungi atribut dari modifikasi yang tidak diinginkan
     */
    protected $fillable = [
        'user_id',               // ID pengguna yang meminjam buku
        'buku_id',               // ID buku yang dipinjam
        'no_peminjaman',         // Nomor peminjaman untuk tracking
        'tanggal_pinjam',        // Tanggal buku dipinjam
        'tanggal_kembali',       // Tanggal buku harus dikembalikan
        'tanggal_pengembalian',  // Tanggal buku dikembalikan (aktual)
        'status',                // Status peminjaman (Dipinjam, Dikembalikan, Terlambat)
        'catatan',               // Catatan terkait peminjaman
        'is_terlambat',          // Flag apakah peminjaman terlambat
        'jumlah_hari_terlambat', // Jumlah hari keterlambatan
        'is_stok_returned',      // Flag apakah stok sudah dikembalikan untuk peminjaman gagal
        'diproses_by',
        'created_at',            // Timestamp saat peminjaman dibuat
        'updated_at',            // Timestamp saat peminjaman terakhir diperbarui
    ];

    /**
     * Relasi dengan user (peminjam) - Menghubungkan PeminjamanModel dengan User
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan antara Peminjaman dan User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi many-to-one - 1 User bisa memiliki banyak Peminjaman (record historis semua peminjaman) dan satu peminjaman hanya milik satu user
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi dengan buku - Menghubungkan PeminjamanModel dengan BukuModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan antara Peminjaman dan Buku
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buku() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi many-to-one - Banyak peminjaman bisa dilakukan terhadap satu buku (tergantung stok buku) dan satu peminjaman hanya berisi satu buku
        return $this->belongsTo(BukuModel::class, 'buku_id');
    }

    /**
     * Relasi dengan sanksi - Menghubungkan PeminjamanModel dengan SanksiModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan antara Peminjaman dan Sanksi
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sanksi() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi one-to-one - Satu peminjaman hanya bisa memiliki satu sanksi
        return $this->hasOne(\App\Models\SanksiModel::class, 'peminjaman_id');
    }
}
