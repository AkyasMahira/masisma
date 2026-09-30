<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KegiatanTujuan extends Model {
    protected $table = 'kegiatan_tujuan';
    protected $fillable = ['kegiatan_id', 'tujuan'];
}