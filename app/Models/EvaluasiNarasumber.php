<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiNarasumber extends Model
{
    protected $table = 'evaluasi_narasumber';
    protected $fillable = ['kegiatan_id', 'nama', 'profesi', 'tanggal_pengisian', 'saran'];
    protected $casts = ['tanggal_pengisian' => 'date'];

    /** Skala: nilai (1-4) => [label, rentang, nilai wakil (titik tengah rentang)] */
    const SKALA = [
        1 => ['Kurang', '45 - 55', 50.0],
        2 => ['Sedang', '56 - 75', 65.5],
        3 => ['Baik', '76 - 85', 80.5],
        4 => ['Sangat Baik', '86 ke atas', 93.0],
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function nilai()
    {
        return $this->hasMany(EvaluasiNarasumberNilai::class, 'evaluasi_narasumber_id');
    }

    /** Nilai angka (skala 45-100) dari pilihan 1-4. */
    public static function skor($pilihan)
    {
        return self::SKALA[(int) $pilihan][2] ?? 0.0;
    }

    /** Kategori dari nilai angka. */
    public static function kategori($skor)
    {
        if ($skor >= 86) return 'Sangat Baik';
        if ($skor >= 76) return 'Baik';
        if ($skor >= 56) return 'Sedang';
        return 'Kurang';
    }
}
