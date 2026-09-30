<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProdi extends Model
{
    protected $table = 'master_prodis';
    protected $fillable = ['kategori', 'nama_prodi', 'jenjang', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }

    /**
     * Daftar prodi aktif dikelompokkan per kategori (untuk optgroup dropdown).
     * @return array [kategori => [nama_prodi, ...]]
     */
    public static function grouped()
    {
        return static::aktif()
            ->orderBy('kategori')->orderBy('nama_prodi')
            ->get()
            ->groupBy('kategori')
            ->map(function ($items) {
                return $items->pluck('nama_prodi')->all();
            })
            ->all();
    }

    /** Daftar jenjang unik yang aktif */
    public static function jenjangList()
    {
        return static::aktif()->whereNotNull('jenjang')
            ->distinct()->orderBy('jenjang')->pluck('jenjang')->all();
    }
}
