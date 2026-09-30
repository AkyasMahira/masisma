<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPelatihan extends Model
{
    protected $fillable = ['nama_pelatihan', 'kategori', 'durasi', 'sasaran'];
}