<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiJawaban extends Model
{
    protected $table = 'evaluasi_jawaban';
    protected $fillable = ['evaluasi_id', 'master_evaluasi_id', 'nilai', 'jawaban_text'];

    public function evaluasi()
    {
        return $this->belongsTo(Evaluasi::class, 'evaluasi_id');
    }

    public function unsur()
    {
        return $this->belongsTo(MasterEvaluasi::class, 'master_evaluasi_id');
    }
}
