<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomSequence extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'ruangan_id',
        'start_date',
        'end_date',
    ];

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    // Relasi ke Ruangan
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
