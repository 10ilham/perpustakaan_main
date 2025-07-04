<?php

namespace App\Models;

// Menggunakan traits dan kelas dari framework - Konsep OOP: Reusability (menggunakan kembali kode yang sudah ada di laravel)
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Kelas SiswaModel - Representasi data siswa di perpustakaan
 * Konsep OOP: Inheritance (Pewarisan) - Kelas ini mewarisi kelas Model dari Laravel
 * Konsep OOP: Encapsulation (Enkapsulasi) - Menyembunyikan detail implementasi
 */
class SiswaModel extends Model // Konsep OOP: Inheritance - mewarisi sifat dan metode dari kelas Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan dalam database
     * Konsep OOP: Encapsulation - Menggunakan modifier protected untuk membatasi akses
     */
    protected $table = 'siswa'; // protected - hanya dapat diakses oleh kelas ini dan turunannya

    /**
     * Konsep OOP: Encapsulation - Melindungi atribut dari modifikasi yang tidak diinginkan
     */
    protected $fillable = [
        'user_id',      // Kunci asing (foreign key) ke tabel users
        'nisn',          // Nomor Induk Siswa
        'kelas',        // Kelas siswa
        'tanggal_lahir', // Tanggal lahir siswa
        'alamat',       // Alamat siswa
        'no_telepon',   // Nomor telepon siswa
        'foto',         // Path ke foto siswa
        'created_at',   // Timestamp saat akun dibuat
        'updated_at',   // Timestamp saat akun terakhir diperbarui
    ];

    /**
     * Relasi ke tabel users - Menghubungkan SiswaModel dengan User
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan antara kelas SiswaModel dan User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi one-to-one (inverse) - Siswa milik satu User (satu akun milik satu orang)
        return $this->belongsTo(User::class, 'user_id');
    }
}
