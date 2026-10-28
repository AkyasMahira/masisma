<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanPenilaianSetting extends Model
{
    protected $table = 'kegiatan_penilaian_settings';

    protected $fillable = ['kegiatan_id', 'jenis_penilaian', 'skala_json', 'batas_lulus_persen', 'pakai_posttest', 'sertifikat_halaman_2', 'masa_berlaku_tahun'];

    protected $casts = ['skala_json' => 'array', 'pakai_posttest' => 'boolean', 'sertifikat_halaman_2' => 'boolean', 'masa_berlaku_tahun' => 'integer'];

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

    const TERBILANG = [1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima', 6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh'];

    /** Teks masa berlaku, mis. "3 (tiga) tahun"; null bila tidak dicantumkan (0). */
    public function teksMasaBerlaku()
    {
        $n = (int) ($this->masa_berlaku_tahun ?? 3);
        if ($n <= 0) return null;
        return $n . (isset(self::TERBILANG[$n]) ? ' (' . self::TERBILANG[$n] . ')' : '') . ' tahun';
    }

    /** Format angka skor tanpa ".0" yang tidak perlu. */
    public static function fmt($v)
    {
        return rtrim(rtrim(number_format((float) $v, 1, '.', ''), '0'), '.');
    }
}
