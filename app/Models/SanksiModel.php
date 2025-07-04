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

    protected $casts = [
        'denda_keterlambatan' => 'integer',
        'denda_kerusakan' => 'integer',
        'total_denda' => 'integer',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanModel::class, 'peminjaman_id');
    }
}
