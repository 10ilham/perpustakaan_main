<?php
// Deklarasi namespace - Konsep OOP: Namespace (memisahkan dan mengorganisir kode)
namespace App\Models;

// Menggunakan traits dan kelas dari framework - Konsep OOP: Reusability (menggunakan kembali kode yang sudah ada di laravel)
use Illuminate\Contracts\Auth\MustVerifyEmail; // Interface untuk verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory; // untuk pattern Factory
use Illuminate\Foundation\Auth\User as Authenticatable; // Base class untuk autentikasi
use Illuminate\Notifications\Notifiable; // untuk sistem notifikasi

/**
 * Model User - Kelas utama untuk pengguna sistem perpustakaan
 * Konsep OOP: Inheritance - Mewarisi kelas Authenticatable
 * Konsep OOP: Interface Implementation - Mengimplementasikan interface MustVerifyEmail
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /**
     * HasFactory: Memungkinkan pembuatan model dengan factory untuk testing
     * Notifiable: Menambahkan kemampuan menerima notifikasi
     */
    use HasFactory, Notifiable;

    /**
     * Konsep OOP: Encapsulation - Menggunakan modifier protected untuk membatasi akses
     */
    protected $table = 'users';
    // Jika nama primary key berbeda dari default (id), tentukan di sini
    // protected $primaryKey = 'id';

    /**
     * Konsep OOP: Encapsulation - Melindungi atribut dari modifikasi yang tidak diinginkan
     */
    // Atribut
    protected $fillable = [
        'nama',                     // Nama pengguna
        'email',                    // Email pengguna (digunakan untuk login)
        'password',                 // Password pengguna (dienkripsi HASH)
        'level',                    // Peran pengguna (admin, siswa, guru, staff)
        'email_verified_at',        // Timestamp saat email diverifikasi
        'email_verification_token', // Token untuk verifikasi email
        'created_at',             // Timestamp saat akun dibuat
        'updated_at',             // Timestamp saat akun terakhir diperbarui
    ];

    /**
     * Atribut yang harus disembunyikan saat konversi ke array atau JSON
     * Konsep OOP: Encapsulation - Melindungi data sensitif
     * Konsep OOP: Information Hiding - Menyembunyikan properti yang tidak perlu dilihat
     */
    protected $hidden = [
        'password',      // Menyembunyikan password dari response JSON
    ];

    /**
     * Relasi ke tabel admin
     * Konsep OOP: Association - Menunjukkan hubungan antara objek User dan AdminModel
     * Konsep OOP: Composition - User "memiliki" profil Admin
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admin()
    {
        // Implementasi relasi one-to-one - User memiliki satu Admin (satu akun milik satu orang)
        return $this->hasOne(AdminModel::class, 'user_id');
    }

    /**
     * Relasi ke tabel siswa
     * Konsep OOP: Association - Menunjukkan hubungan antara objek User dan SiswaModel
     * Konsep OOP: Composition - User "memiliki" profil Siswa
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function siswa()
    {
        // Implementasi relasi one-to-one - User memiliki satu Siswa (satu akun milik satu orang)
        return $this->hasOne(SiswaModel::class, 'user_id');
    }

    /**
     * Relasi ke tabel guru
     * Konsep OOP: Association - Menunjukkan hubungan antara objek User dan GuruModel
     * Konsep OOP: Composition - User "memiliki" profil Guru
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guru()
    {
        // Implementasi relasi one-to-one - User memiliki satu Guru
        return $this->hasOne(GuruModel::class, 'user_id');
    }

    /**
     * Relasi ke tabel staff
     * Konsep OOP: Association - Menunjukkan hubungan antara objek User dan StaffModel
     * Konsep OOP: Composition - User "memiliki" profil Staff
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function staff()
    {
        // Implementasi relasi one-to-one - User memiliki satu Staff
        return $this->hasOne(StaffModel::class, 'user_id');
    }

    /**
     * Relasi ke tabel peminjaman
     * Konsep OOP: Association - Menunjukkan hubungan antara objek User dan PeminjamanModel
     * Konsep OOP: Composition - User "memiliki" banyak peminjaman
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peminjaman()
    {
        // Implementasi relasi one-to-many - User bisa memiliki banyak peminjaman (historis peminjaman)
        return $this->hasMany(PeminjamanModel::class, 'user_id');
    }
}
