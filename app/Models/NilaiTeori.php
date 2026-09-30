<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiTeori extends Model
{
    protected $table = 'nilai_teori';

    protected $fillable = ['kegiatan_peserta_id', 'nilai_pretest', 'nilai_posttest'];

    public function peserta()
    {
        return $this->belongsTo(KegiatanPeserta::class, 'kegiatan_peserta_id');
    }
}