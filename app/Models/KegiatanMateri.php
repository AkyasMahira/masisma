<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanMateri extends Model
{
    protected $table = 'kegiatan_materi';

protected $fillable = [
    'kegiatan_id', 
    'nama_materi', 
    'butuh_penilaian', 
    'tampil_di_sertifikat', // <--- TAMBAHAN BARU
    'nilai_teori', 
    'nilai_praktik', 
    'urutan'
];
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    // Item checklist / penilaian milik materi ini (sudah terurut)
    public function items()
    {
        return $this->hasMany(ItemPenilaianSkill::class, 'materi_id')
            ->orderBy('kategori')
            ->orderBy('urutan');
    }

    // Fasilitator pengampu materi ini
    public function fasilitator()
    {
        return $this->belongsToMany(KegiatanFasilitator::class, 'materi_fasilitator', 'materi_id', 'fasilitator_id');
    }
}