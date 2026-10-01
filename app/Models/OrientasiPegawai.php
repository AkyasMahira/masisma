<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrientasiPegawai extends Model
{
    protected $table = 'orientasi_pegawais';

    protected $fillable = [
        'pegawai_id', 'nip', 'nama', 'unit',
        'status', 'pre_test_score', 'post_test_score', 'tahun', 'keterangan',
    ];
}
