<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrientasiResult extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahkan relasi ini
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}