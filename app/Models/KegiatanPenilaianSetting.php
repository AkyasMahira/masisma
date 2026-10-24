<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanPenilaianSetting extends Model
{
    protected $table = 'kegiatan_penilaian_settings';

    protected $fillable = ['kegiatan_id', 'jenis_penilaian', 'skala_json', 'batas_lulus_persen', 'pakai_posttest'];

    protected $casts = ['skala_json' => 'array', 'pakai_posttest' => 'boolean'];

    const PRESET = [
        'centang' => [['nilai' => 0, 'label' => 'Tidak Kompeten'], ['nilai' => 1, 'label' => 'Kompeten']],
        'skor'    => [['nilai' => 0, 'label' => 'Tidak Dilakukan'], ['nilai' => 1, 'label' => 'Kurang Tepat'], ['nilai' => 2, 'label' => 'Tepat & Sempurna']],
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    /**
     * Daftar pilihan skor (urut nilai kecil -> besar): [['nilai' => float, 'label' => string], ...]
     * centang & skor = preset, custom = ditentukan admin.
     */
    public function opsiSkala()
    {
        $list = ($this->jenis_penilaian === 'custom' && is_array($this->skala_json) && count($this->skala_json) >= 2)
            ? $this->skala_json
            : (self::PRESET[$this->jenis_penilaian] ?? self::PRESET['centang']);
        $list = array_map(function ($o) { return ['nilai' => (float) $o['nilai'], 'label' => (string) $o['label']]; }, $list);
        usort($list, function ($a, $b) { return $a['nilai'] <=> $b['nilai']; });
        return $list;
    }

    /** Nilai-nilai yang sah untuk satu item. */
    public function nilaiSah()
    {
        return array_column($this->opsiSkala(), 'nilai');
    }

    // Nilai maksimum per item = pilihan skor tertinggi
    public function nilaiMaksPerItem()
    {
        $n = $this->nilaiSah();
        return $n ? max($n) : 1;
    }

    /** Label singkat format, mis. "Centang", "Skor 0-2", "Custom (0-3)". */
    public function labelFormat()
    {
        if ($this->jenis_penilaian === 'centang') return 'Centang';
        $n = $this->nilaiSah();
        $rentang = $n ? (rtrim(rtrim(number_format(min($n), 1, '.', ''), '0'), '.') . '-' . rtrim(rtrim(number_format(max($n), 1, '.', ''), '0'), '.')) : '';
        return ($this->jenis_penilaian === 'custom' ? 'Custom ' : 'Skor ') . $rentang;
    }

    /** Format angka skor tanpa ".0" yang tidak perlu. */
    public static function fmt($v)
    {
        return rtrim(rtrim(number_format((float) $v, 1, '.', ''), '0'), '.');
    }
}
