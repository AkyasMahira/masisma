<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Model;

class Mou extends Model
{
    /**
     * Nama tabel
     * @var string
     */
    protected $table = 'mous';

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nama_universitas',
        'tanggal_masuk',
        'tanggal_keluar',
        'file_mou',
        'surat_keterangan',
        'keterangan',
    ];

    /**
     * Tipe data bawaan (casting).
     *
     * @var array
     */
    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];
}