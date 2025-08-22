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
        'kasbon_id',
        'jumlah_bayar',
        'metode',
        'tanggal_bayar'
    ];

    public function kasbon()
    {
        return $this->belongsTo(Kasbon::class, 'kasbon_id', 'id');
    }
}
