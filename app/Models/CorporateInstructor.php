<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateInstructor extends Model
{

    protected $fillable = ['nama', 'no_hp', 'bidang'];

    // Relasi ke Pengajuan 
    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class, 'ci_id');
    }
}