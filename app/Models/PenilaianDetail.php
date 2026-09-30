<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianDetail extends Model
{
    // Hapus baris "use HasFactory;" di sini jika ada

    protected $table = 'penilaian_details';

    protected $fillable = [
        'presentasi_id',
        'nama_ci',
        'skor_angka',
        'catatan',
    ];

    protected $casts = [
        'catatan' => 'array',
    ];

    public function presentasi()
    {
        return $this->belongsTo(Presentasi::class);
    }
}