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
        Schema::table('buku', function (Blueprint $table) {
            $table->decimal('harga_buku', 10, 2)->default(0)->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn('harga_buku');
        });
    }
};
