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
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_admin')->nullable(); //unsignedInteger yaitu hanya menerima nilai positif atau 0
            $table->string('kode_buku', 22)->unique();
            $table->string('judul', 60);
            $table->string('pengarang', 50);
            $table->string('penerbit', 50);
            $table->string('tahun_terbit', 4);
            $table->text('deskripsi');
            $table->text('foto')->nullable();
            $table->unsignedInteger('total_buku')->default(0); //unsignedInteger yaitu hanya menerima nilai positif atau 0
            $table->unsignedInteger('stok_buku')->default(0);
            $table->string('status', 8)->default('Tersedia');
            $table->timestamps();

            // Foreign key
            $table->foreign('id_admin')->references('id')->on('admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
