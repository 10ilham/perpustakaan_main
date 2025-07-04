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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('buku_id');
            $table->string('no_peminjaman', 21)->unique();
            $table->dateTime('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->date('tanggal_pengembalian')->nullable();
            $table->enum('status', ['Diproses', 'Dipinjam', 'Dikembalikan', 'Terlambat', 'Dibatalkan'])->default('Diproses');
            $table->text('catatan')->nullable();
            $table->boolean('is_terlambat')->default(false);
            $table->boolean('is_stok_returned')->default(false);
            $table->integer('jumlah_hari_terlambat')->default(0);
            $table->string('diproses_by', 6)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('buku_id')->references('id')->on('buku')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
