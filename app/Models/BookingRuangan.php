<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRuangan extends Model
{
    protected $table = 'booking_ruangans';

    protected $fillable = [
        'mou_id', 'ruangan_id', 'user_id', 'jumlah_peserta',
        'tanggal_mulai', 'tanggal_selesai', 'keterangan',
        'status', 'catatan_admin', 'batas_pengisian',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'batas_pengisian' => 'date',
    ];

    // Apakah pengisian peserta masih dibuka (belum lewat batas)
    public function pengisianDibuka()
    {
        if (!$this->batas_pengisian) return true;
        return \Carbon\Carbon::parse($this->batas_pengisian)->endOfDay()->gte(\Carbon\Carbon::now());
    }

    public function mou()
    {
        return $this->belongsTo(Mou::class, 'mou_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pesertas()
    {
        return $this->hasMany(BookingPeserta::class, 'booking_ruangan_id');
    }
}
