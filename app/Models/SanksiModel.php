<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanksiModel extends Model
{
    protected $table = 'sanksi';

    protected $fillable = [
        'peminjaman_id',
        'jenis_sanksi',
        'hari_terlambat',
        'denda_keterlambatan',
        'denda_kerusakan',
        'total_denda',
        'status_bayar',
        'keterangan'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanModel::class, 'peminjaman_id');
    }
}
