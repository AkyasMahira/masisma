<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiklatForm extends Model
{
    protected $fillable = [
        'judul',
        'tanggal_pelaksanaan',
        'keterangan',
        'peraturan',
        'banner_path',
        'opsi_pelatihan',
        'opsi_tempat',
        'pertanyaan_custom',
        'public_link',
    ];

    // Ini wajib agar otomatis jadi Array
    protected $casts = [
        'opsi_pelatihan' => 'array',
        'opsi_tempat' => 'array',
        'pertanyaan_custom' => 'array',
    ];
}
