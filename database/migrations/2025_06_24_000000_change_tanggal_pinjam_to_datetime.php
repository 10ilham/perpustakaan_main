<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('peminjaman', function (Blueprint $table) {
        //     // Ubah tipe kolom tanggal_pinjam dari date menjadi datetime
        //     // Hal ini diperlukan agar bisa menyimpan informasi jam peminjaman yang akurat
        //     $table->dateTime('tanggal_pinjam')->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Kembalikan tipe kolom ke dateTime juga jika rollback
            $table->dateTime('tanggal_pinjam')->change();
        });
    }
};
