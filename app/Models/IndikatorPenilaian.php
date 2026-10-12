<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorPenilaian extends Model
{
    protected $fillable = ['ruangan_id', 'nama', 'kategori', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
