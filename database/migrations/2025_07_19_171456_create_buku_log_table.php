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
        Schema::create('buku_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buku_id')->nullable(); // Will be null if the book is deleted
            $table->foreign('buku_id')->references('id')->on('buku')->onDelete('set null');
            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('users');
            $table->enum('tipe', ['masuk', 'keluar']); // 'masuk' for book added, 'keluar' for book removed
            $table->string('judul_buku');
            $table->string('kode_buku');
            $table->text('alasan')->nullable(); // Reason for addition or removal
            $table->integer('jumlah')->default(1); // Quantity of books added or removed
            $table->date('tanggal'); // Date of action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_log');
    }
};
