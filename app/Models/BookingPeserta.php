<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPeserta extends Model
{
    protected $table = 'booking_pesertas';

    protected $fillable = [
        'booking_ruangan_id', 'nama', 'nim', 'prodi',
        'jenis_kelamin', 'no_hp', 'keterangan',
        'status', 'catatan_admin', 'user_id', 'mahasiswa_id',
    ];

    public function booking()
    {
        return $this->belongsTo(BookingRuangan::class, 'booking_ruangan_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
