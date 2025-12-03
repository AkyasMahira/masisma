<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'mahasiswa_id',
        'jam_masuk',
        'jam_keluar',
        'type',
        'durasi_menit',
        'latitude',
        'longitude',
        'location_accuracy',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
