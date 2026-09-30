<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterRuangan extends Model
{
    protected $table = 'master_ruangan';
    protected $fillable = ['instansi_id', 'nama_ruangan'];

    // Relasi: Ruangan ini milik dari instansi mana
    public function instansi()
    {
        return $this->belongsTo(MasterInstansi::class, 'instansi_id');
    }
}