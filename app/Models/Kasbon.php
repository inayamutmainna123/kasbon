<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Kasbon extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'kasbon';

    protected $fillable = [
        'karyawan_id',
        'jumlah',
        'alasan',
        'status',
        'tanggal_pengajuan',
        'tanggal_approval'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'kasbon_id', 'id');
    }
}
