<?php

namespace App\Models;

// Menggunakan traits dan kelas dari framework - Konsep OOP: Reusability (menggunakan kembali kode yang sudah ada di laravel)
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Kelas KategoriModel - Representasi kategori buku di perpustakaan
 * Konsep OOP: Inheritance (Pewarisan) - Kelas ini mewarisi kelas Model dari Laravel
 */
class KategoriModel extends Model // Konsep OOP: Inheritance - mewarisi sifat dan metode dari kelas Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan dalam database
     * Konsep OOP: Encapsulation - Menggunakan modifier protected untuk membatasi akses
     */
    protected $table = 'kategori'; // protected - hanya dapat diakses oleh kelas ini dan turunannya

    /**
     * Atribut
     * Konsep OOP: Encapsulation - Melindungi atribut dari modifikasi yang tidak diinginkan
     */
    protected $fillable = [
        'id_admin',  // Foreign key ke tabel admin
        'nama_kategori',      // Nama kategori buku
        'deskripsi', // Deskripsi kategori buku
        'created_at', // Timestamp saat kategori dibuat
        'updated_at', // Timestamp saat kategori terakhir diperbarui
    ];

    /**
     * Relasi ke tabel admin - Menghubungkan KategoriModel dengan AdminModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan many-to-one antara Kategori dan Admin
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        // Implementasi relasi many-to-one - Banyak kategori bisa dimiliki oleh satu admin
        return $this->belongsTo(AdminModel::class, 'id_admin');
    }

    /**
     * Relasi ke tabel buku - Menghubungkan KategoriModel dengan BukuModel
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan many-to-many antara Kategori dan Buku
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function buku() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi many-to-many - Satu kategori bisa dimiliki oleh banyak buku,
        // dan satu buku bisa memiliki banyak kategori
        // Menggunakan tabel pivot (tabel perantara) 'kategori_buku' untuk menyimpan relasi antara kategori dan buku
        return $this->belongsToMany(BukuModel::class, 'kategori_buku', 'kategori_id', 'buku_id');
    }
}
