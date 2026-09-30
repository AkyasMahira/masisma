<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanPenilaianSetting extends Model
{
    protected $table = 'kegiatan_penilaian_settings';

    protected $fillable = ['kegiatan_id', 'jenis_penilaian', 'batas_lulus_persen'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    // Nilai maksimum per item tergantung jenis penilaian
    public function nilaiMaksPerItem()
    {
        return $this->jenis_penilaian === 'skor' ? 2 : 1;
    }
}