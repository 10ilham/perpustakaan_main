<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Relasi Many-to-Many tidak dapat diimplementasikan hanya dengan foreign key biasa di salah satu tabel.
     * Untuk menghubungkan dua tabel yang memiliki relasi many-to-many, kita perlu membuat tabel pivot (tabel perantara).
     * Karena jika menambahkan kolom kategori_id di tabel buku, maka satu buku hanya bisa memiliki satu kategori (one to many).
     * maka kolom tabelnya harus seperti ini: buku_id, kategori_id1, kategori_id2, kategori_id3, ... (tidak efisien dan tidak ada normalisasi).
     */

    // Tabel pivot (tabel perantara) untuk relasi many-to-many antara buku dan kategori
    public function up(): void
    {
        Schema::create('kategori_buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');

            // Pastikan sebuah buku tidak bisa terhubung dengan kategori yang sama dua kali
            $table->unique(['buku_id', 'kategori_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_buku');
    }
};
