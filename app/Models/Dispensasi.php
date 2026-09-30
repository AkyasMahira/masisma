<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensasi extends Model
{
    // Kita hapus 'use HasFactory' agar tidak error lagi
    
    protected $table = 'dispensasis';

    protected $fillable = [
        'mahasiswa_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'file_path',
        'status',
        'catatan_admin',
        'kategori',
        'keterangan_terlambat',
    ];

    // Relasi ke tabel Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}