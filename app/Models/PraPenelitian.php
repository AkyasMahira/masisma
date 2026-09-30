<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PraPenelitian extends Model
{
    protected $table = 'pra_penelitians';

protected $fillable = [
    'user_id',
    'judul',
    'mou_id',
    'jenis_penelitian',
    'prodi',
    'tanggal_mulai',
    'tanggal_rencana_skripsi',
    'file_kerangka',
    'file_surat_pengantar',
    'file_ethical_clearance',
    'file_proposal', 
    'dosen1_nama',
    'dosen1_hp',
    'dosen2_nama',
    'dosen2_hp',
    'status',
    'jenis_mahasiswa',
];



    protected $dates = [
        'tanggal_mulai',
        'tanggal_rencana_skripsi',
        'created_at',
        'updated_at',
    ];

    // Relasi ke User (Pendaftar)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function presentasi()
    {
        return $this->hasOne(Presentasi::class);
    }

    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class);
    }

    // Relasi ke Universitas (MOU)
    public function mou()
    {
        return $this->belongsTo(Mou::class);
    }

    // Relasi ke Anggota (Mahasiswa yang didaftarkan)
    public function anggotas()
    {
        return $this->hasMany(PraPenelitianAnggota::class, 'pra_penelitian_id');
    }
}