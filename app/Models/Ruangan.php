<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    // BARIS INI SANGAT PENTING
    protected $table = 'ruangans';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'nm_ruangan',
        'kuota_ruangan',
    ];
}
