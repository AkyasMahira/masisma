<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKompetensi extends Model
{
    protected $table = 'master_kompetensi';
    protected $fillable = ['nama_kompetensi', 'deskripsi_default'];

    // Relasi ke tabel pivot kegiatan nantinya
    public function kegiatan()
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_kompetensi', 'kompetensi_id', 'kegiatan_id')
                    ->withPivot('indikator_keberhasilan')
                    ->withTimestamps();
    }
}