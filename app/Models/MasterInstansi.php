<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterInstansi extends Model
{
    protected $table = 'master_instansi';
    protected $fillable = ['nama_instansi'];

    // Relasi: Satu instansi punya banyak ruangan
    public function ruangan()
    {
        return $this->hasMany(MasterRuangan::class, 'instansi_id');
    }

    // Relasi: Satu instansi bisa menyelenggarakan banyak kegiatan
    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'penyelenggara_id');
    }
}