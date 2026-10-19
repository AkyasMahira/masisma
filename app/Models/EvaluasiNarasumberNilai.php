<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiNarasumberNilai extends Model
{
    protected $table = 'evaluasi_narasumber_nilai';
    protected $fillable = ['evaluasi_narasumber_id', 'fasilitator_id', 'nama_narasumber', 'master_evaluasi_id', 'aspek', 'nilai'];

    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiNarasumber::class, 'evaluasi_narasumber_id');
    }
}
