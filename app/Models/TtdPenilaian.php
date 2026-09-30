<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TtdPenilaian extends Model
{
    protected $table = 'ttd_penilaian';

 protected $fillable = [
    'kegiatan_peserta_id', 
    'fasilitator_id', 
    'materi_id', // <--- Wajib ditambahkan
    'tanda_tangan', 
    'waktu_ttd'
];

    public function peserta()
    {
        return $this->belongsTo(KegiatanPeserta::class, 'kegiatan_peserta_id');
    }

    public function fasilitator()
    {
        return $this->belongsTo(KegiatanFasilitator::class, 'fasilitator_id');
    }
}