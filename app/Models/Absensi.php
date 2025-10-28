<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'mahasiswa_id',
        'type',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
