<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BukuModel;
use App\Models\User;

class BukuLogModel extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan dalam database
     */
    protected $table = 'buku_log';

    /**
     * Atribut yang dapat diisi secara massal (mass assignment)
     */
    protected $fillable = [
        'buku_id',
        'tipe',
        'judul_buku',
        'kode_buku',
        'alasan',
        'jumlah',
        'tanggal',
    ];

    /**
     * Relasi ke tabel buku
     */
    public function buku()
    {
        return $this->belongsTo(BukuModel::class, 'buku_id');
    }
}
