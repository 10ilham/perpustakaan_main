<?php

namespace App\Models;

// Menggunakan traits dan kelas dari framework - Konsep OOP: Reusability (menggunakan kembali kode yang sudah ada di laravel)
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Kelas StaffModel - Representasi data staff perpustakaan
 * Konsep OOP: Inheritance (Pewarisan) - Kelas ini mewarisi kelas Model dari Laravel
 * Konsep OOP: Encapsulation (Enkapsulasi) - Menyembunyikan detail implementasi
 */
class StaffModel extends Model // Konsep OOP: Inheritance - mewarisi sifat dan metode dari kelas Model
{
    /**
     * Konsep OOP: Traits - Mekanisme untuk menggunakan kembali kode
     */
    use HasFactory;

    /**
     * Nama tabel yang digunakan dalam database
     * Konsep OOP: Encapsulation - Menggunakan modifier protected untuk membatasi akses
     */
    protected $table = 'staff'; // protected - hanya dapat diakses oleh kelas ini dan turunannya

    /**
     * Atribut
     * Konsep OOP: Encapsulation - Melindungi atribut dari modifikasi yang tidak diinginkan
     */
    protected $fillable = [
        'user_id',      // Kunci asing (foreign key) ke tabel users
        'nip',          // Nomor Induk Pegawai untuk staff
        'bagian',       // Bagian staff di sekolah
        'tanggal_lahir', // Tanggal lahir staff
        'alamat',       // Alamat staff
        'no_telepon',   // Nomor telepon staff
        'foto',         // Path ke foto staff
        'created_at',   // Timestamp saat akun dibuat
        'updated_at',   // Timestamp saat akun terakhir diperbarui
    ];

    /**
     * Relasi ke tabel users - Menghubungkan StaffModel dengan User
     * Konsep OOP: Association (Asosiasi) - Menunjukkan hubungan antara kelas StaffModel dan User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() // public - dapat diakses dari mana saja
    {
        // Implementasi relasi one-to-one (inverse) - Staff milik satu User (satu akun milik satu orang)
        return $this->belongsTo(User::class, 'user_id');
    }
}
