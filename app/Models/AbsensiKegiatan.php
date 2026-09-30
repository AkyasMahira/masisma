<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiKegiatan extends Model
{
    protected $table = 'absensi_kegiatan';
    
    // UBAH BAGIAN INI:
    protected $fillable = [
        'kegiatan_id', 
        'kegiatan_peserta_id', 
        'status_kehadiran', 
        'foto_bukti', 
        'waktu_masuk',   // <-- Ganti dari jam_masuk
        'waktu_keluar'   // <-- Ganti dari jam_pulang
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function profilPeserta()
    {
        return $this->belongsTo(KegiatanPeserta::class, 'kegiatan_peserta_id');
    }
}