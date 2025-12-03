<?php

namespace App\Models;

use App\Http\Controllers\PresentasiController;
use Illuminate\Database\Eloquent\Model;

class PresentasiDokumen extends Model
{
    protected $table = 'presentasi_dokumen';

    protected $fillable = [
        'presentasi_id',
        'nama_lengkap',
        'surat_selesai',
        'sertifikat',
    ];

    public function presentasi()
    {
        return $this->belongsTo(Presentasi::class);
    }
}