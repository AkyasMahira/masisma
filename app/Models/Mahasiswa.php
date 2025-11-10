<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Absensi;
use App\Models\Ruangan;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswas';

    protected $fillable = [
        'nm_mahasiswa',
        'univ_asal',
        'prodi',
        'nm_ruangan',
        'ruangan_id',
        'status',
        'share_token',
        'tanggal_mulai',
        'tanggal_berakhir',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->share_token)) {
                $model->share_token = (string) Str::uuid();
            }
        });
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'mahasiswa_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function getSisaHariAttribute()
    {
        if (!$this->tanggal_berakhir) {
            return null;
        }

        $today = now()->startOfDay();
        $endDate = \Carbon\Carbon::parse($this->tanggal_berakhir)->startOfDay();

        if ($today > $endDate) {
            return 0;
        }

        return $today->diffInDays($endDate);
    }
}
