<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomSchedule extends Model
{

    protected $guarded = []; // Izinkan semua kolom diisi (penting buat si Robot)

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