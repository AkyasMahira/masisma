<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{

    protected $table = 'pelatihans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'jabatan',
        'unit',
        'is_pns',
        'nip',
        'golongan',
        'pelatihan_dasar',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pelatihan_dasar' => 'array',
        'is_pns' => 'boolean',
    ];

}
