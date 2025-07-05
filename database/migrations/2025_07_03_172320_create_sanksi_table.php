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
        Schema::create('sanksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peminjaman_id');
            $table->enum('jenis_sanksi', ['keterlambatan', 'rusak_hilang']);
            $table->integer('hari_terlambat')->default(0);
            $table->integer('denda_keterlambatan')->default(0);
            $table->integer('denda_kerusakan')->default(0);
            $table->integer('total_denda')->default(0);
            $table->enum('status_bayar', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanksi');
    }
};
