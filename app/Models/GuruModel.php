<?php

namespace App\Models;

// Menggunakan traits dan kelas dari framework - Konsep OOP: Reusability (menggunakan kembali kode yang sudah ada di laravel)
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Kelas GuruModel - Representasi data guru di perpustakaan
 * Konsep OOP: Inheritance (Pewarisan) - Kelas ini mewarisi kelas Model dari Laravel
 * Konsep OOP: Encapsulation (Enkapsulasi) - Menyembunyikan detail implementasi
 */
class GuruModel extends Model // Konsep OOP: Inheritance - mewarisi sifat dan metode dari kelas Model
{

    use HasFactory;

    /**
     * Nama tabel yang digunakan dalam database
     * Konsep OOP: Encapsulation - Menggunakan modifier protected untuk membatasi akses
     */
    protected $table = 'guru'; // protected - hanya dapat diakses oleh kelas ini dan turunannya

    /**
     * Atribut
     * Konsep OOP: Encapsulation - Melindungi atribut dari modifikasi yang tidak diinginkan
     */
    protected $fillable = [
        'user_id',         // Kunci asing ke tabel users
        'nip',             // Nomor Induk Pegawai untuk guru
        'mata_pelajaran',  // Mata pelajaran yang diampu oleh guru
        'tanggal_lahir',   // Tanggal lahir guru
        'alamat',          // Alamat guru
        'no_telepon',      // Nomor telepon guru
        'foto',            // Path ke foto guru
        'created_at',      // Timestamp saat akun dibuat
        'updated_at',      // Timestamp saat akun terakhir diperbarui
    ];

    /**
     * Relasi ke tabel users - Menghubungkan GuruModel dengan User
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan antara kelas GuruModel dan User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi one-to-one (inverse) - Guru milik satu User (satu akun milik satu orang)
        return $this->belongsTo(User::class, 'user_id');
    }
}
