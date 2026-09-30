<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPenilaianSkill extends Model
{
    protected $table = 'item_penilaian_skill';

protected $fillable = ['kegiatan_id', 'materi_id', 'kategori', 'urutan', 'aspek_tindakan'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function nilaiSkill()
    {
        return $this->hasMany(NilaiSkill::class, 'item_penilaian_id');
    }
}