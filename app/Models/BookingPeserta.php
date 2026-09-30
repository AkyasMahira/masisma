<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPeserta extends Model
{
    protected $table = 'booking_pesertas';

    protected $fillable = [
        'booking_ruangan_id', 'nama', 'nim', 'email', 'prodi',
        'tipe_mahasiswa', 'weekend_aktif', 'jenis_kelamin', 'no_hp',
        'foto_path', 'kompetensi_json', 'keterangan',
        'status', 'catatan_admin', 'user_id', 'mahasiswa_id',
    ];

    protected $casts = [
        'weekend_aktif'   => 'boolean',
        'kompetensi_json' => 'array',
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
