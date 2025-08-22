<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Karyawan extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'jabatan',
        'gaji_pokok'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function kasbons()
    {
        return $this->hasMany(\App\Models\Kasbon::class, 'karyawan_id', 'id');
    }
}
