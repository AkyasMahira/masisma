<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterEvaluasi extends Model
{
    protected $table = 'master_evaluasi';
    protected $fillable = ['kode', 'pertanyaan', 'tipe', 'urutan', 'aktif'];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function jawaban()
    {
        return $this->hasMany(EvaluasiJawaban::class, 'master_evaluasi_id');
    }

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }
}
