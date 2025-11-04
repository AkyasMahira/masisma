<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuanganKetersediaan extends Model
{
    protected $table = 'ruangan_ketersediaans';

    protected $fillable = [
        'ruangan_id',
        'tanggal',
        'tersedia',
    ]; 

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
