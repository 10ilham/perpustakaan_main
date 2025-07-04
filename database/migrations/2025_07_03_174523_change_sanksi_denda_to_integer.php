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
        Schema::table('sanksi', function (Blueprint $table) {
            $table->integer('denda_keterlambatan')->default(0)->change();
            $table->integer('denda_kerusakan')->default(0)->change();
            $table->integer('total_denda')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sanksi', function (Blueprint $table) {
            $table->decimal('denda_keterlambatan', 10, 2)->default(0)->change();
            $table->decimal('denda_kerusakan', 10, 2)->default(0)->change();
            $table->decimal('total_denda', 10, 2)->default(0)->change();
        });
    }
};
