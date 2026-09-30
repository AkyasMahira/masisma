<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomShift extends Model
{
    protected $table = 'room_shifts';

    protected $fillable = [
        'ruangan_id',
        'nama_shift',
        'jam_masuk',
        'jam_keluar',
        'lintas_hari'
    ];

    protected $casts = [
        'lintas_hari' => 'boolean',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}