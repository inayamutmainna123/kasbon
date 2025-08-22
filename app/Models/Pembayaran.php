<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Pembayaran extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pembayaran';

    protected $fillable = [
        'karyawan_id',
        'user_id',
        'kasbon_id',
        'jumlah_bayar',
        'metode',
        'tanggal_bayar'
    ];

    public function kasbon()
    {
        return $this->belongsTo(Kasbon::class, 'kasbon_id', 'id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id', 'id');
    // }

    public function karyawan()
    {
        return $this->belongsTo(\App\Models\Karyawan::class, 'karyawan_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
