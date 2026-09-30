<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiSkill extends Model
{
    protected $table = 'nilai_skill';

    protected $fillable = ['kegiatan_peserta_id', 'item_penilaian_id', 'fasilitator_id', 'nilai', 'catatan'];

    // Pastikan nilai selalu float, agar perbandingan (===) di form edit (radio/select) akurat
    protected $casts = [
        'nilai' => 'float',
    ];

    public function peserta()
    {
        return $this->belongsTo(KegiatanPeserta::class, 'kegiatan_peserta_id');
    }

    public function item()
    {
        return $this->belongsTo(ItemPenilaianSkill::class, 'item_penilaian_id');
    }

    public function fasilitator()
    {
        return $this->belongsTo(KegiatanFasilitator::class, 'fasilitator_id');
    }
}