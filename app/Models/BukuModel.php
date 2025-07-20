<?php

namespace App\Models;

// Menggunakan traits dan kelas dari framework - Konsep OOP: Reusability (menggunakan kembali kode yang sudah ada di laravel)
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor model-model terkait - Konsep OOP: Dependency
use App\Models\KategoriModel;
use App\Models\AdminModel;
use App\Models\PeminjamanModel;
use App\Models\BukuLogModel;

/**
 * Kelas BukuModel - Representasi data buku di perpustakaan
 * Konsep OOP: Inheritance (Pewarisan) - Kelas ini mewarisi kelas Model dari Laravel
 */
class BukuModel extends Model // Konsep OOP: Inheritance - mewarisi sifat dan metode dari kelas Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan dalam database
     * Konsep OOP: Encapsulation - Menggunakan modifier protected untuk membatasi akses
     */
    protected $table = 'buku'; // protected - hanya dapat diakses oleh kelas ini dan turunannya

    /**
     * Atribut yang dapat diisi secara massal (mass assignment)
     * Konsep OOP: Encapsulation - Melindungi atribut dari modifikasi yang tidak diinginkan
     */
    protected $fillable = [
        'id_admin',     // Foreign key ke tabel admin
        'kode_buku',    // Kode unik untuk buku
        'judul',        // Judul buku
        'pengarang',    // Nama pengarang buku
        'penerbit',     // Nama penerbit buku
        'tahun_terbit', // Tahun buku diterbitkan
        'deskripsi',    // Deskripsi atau sinopsis buku
        'foto',         // Path ke foto cover buku
        'total_buku',   // Total jumlah buku yang dimiliki perpustakaan
        'stok_buku',    // Jumlah buku yang tersedia untuk dipinjam
        'status',       // Status ketersediaan buku
        'harga_buku',   // Harga buku untuk perhitungan denda
        'created_at',   // Timestamp saat buku ditambahkan
        'updated_at',   // Timestamp saat buku terakhir diperbarui
    ];

    /**
     * Relasi ke tabel admin - Menghubungkan BukuModel dengan AdminModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan many-to-one antara Buku dan Admin
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        // Implementasi relasi many-to-one - Banyak buku bisa dimiliki oleh satu admin
        return $this->belongsTo(AdminModel::class, 'id_admin');
    }

    /**
     * Relasi ke tabel kategori - Menghubungkan BukuModel dengan KategoriModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan many-to-many antara Buku dan Kategori
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function kategori() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi many-to-many - Satu buku bisa memiliki banyak kategori,
        // dan satu kategori bisa dimiliki oleh banyak buku
        // Menggunakan tabel pivot (tabel perantara) 'kategori_buku' untuk menyimpan relasi antara buku dan kategori
        return $this->belongsToMany(KategoriModel::class, 'kategori_buku', 'buku_id', 'kategori_id');
    }

    /**
     * Relasi ke tabel peminjaman - Menghubungkan BukuModel dengan PeminjamanModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan one-to-many antara Buku dan Peminjaman
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peminjaman()
    {
        // Implementasi relasi one-to-many - Satu buku bisa banyak dipinjam (tegantung stok buku)
        return $this->hasMany(PeminjamanModel::class, 'buku_id');
    }

    /**
     * Relasi ke tabel buku_log - Menghubungkan BukuModel dengan BukuLogModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan one-to-many antara Buku dan BukuLog
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bukuLogs()
    {
        // Implementasi relasi one-to-many - Satu buku bisa memiliki banyak log aktivitas
        return $this->hasMany(BukuLogModel::class, 'buku_id');
    }
}
