<?php

namespace App\Support;

/**
 * Pencocok nama instansi/kampus yang toleran: huruf besar-kecil, isi kurung,
 * tanda baca, singkatan umum (STIKES, IIK, Poltekkes) dan salah ketik ringan.
 */
class MouMatcher
{
    public static function norm($s)
    {
        $s = mb_strtolower((string) $s);
        $s = preg_replace('/\(.*?\)/u', ' ', $s);                 // buang isi kurung: (KD), ( Gadar ), (SMT IV)
        $s = str_replace(['&', '.', ',', '-', '/', "'"], ' ', $s);
        $map = [
            'sekolah tinggi ilmu kesehatan' => 'stikes',
            'institut ilmu kesehatan'       => 'iik',
            'politeknik kesehatan'          => 'poltekkes',
            'akademi keperawatan'           => 'akper',
            'akademi kebidanan'             => 'akbid',
            'universitas'                   => 'univ',
            'university'                    => 'univ',
        ];
        $s = str_replace(array_keys($map), array_values($map), $s);
        $s = preg_replace('/\b(unv\w*|univ\w*|unive\w*|unverst\w*)\b/u', 'univ', $s); // univeristas, unverstas, ...
        $s = preg_replace('/[^a-z0-9 ]/u', ' ', $s);
        return trim(preg_replace('/\s+/', ' ', $s));
    }

    /** Skor kemiripan 0..1 antara dua nama. */
    public static function score($a, $b)
    {
        $a = self::norm($a); $b = self::norm($b);
        if ($a === '' || $b === '') return 0.0;
        if ($a === $b) return 1.0;

        $ca = str_replace(' ', '', $a); $cb = str_replace(' ', '', $b);
        if ($ca === $cb) return 0.99;

        // satu nama memuat nama lain (mis. "stikes bhakti mulia" vs "stikes bhakti mulia kediri")
        $short = strlen($ca) <= strlen($cb) ? $ca : $cb;
        $long  = $short === $ca ? $cb : $ca;
        $contain = (strlen($short) >= 8 && strpos($long, $short) !== false) ? 0.9 : 0.0;

        similar_text($ca, $cb, $pct);
        $ta = array_unique(explode(' ', $a)); $tb = array_unique(explode(' ', $b));
        $inter = count(array_intersect($ta, $tb)); $union = count(array_unique(array_merge($ta, $tb)));
        $jac = $union ? $inter / $union : 0;

        return round(max($pct / 100, $jac, $contain), 3);
    }

    /**
     * Kandidat terbaik dari $candidates (array id => nama).
     * @return array [id|null, skor]
     */
    public static function best($name, array $candidates)
    {
        $bestId = null; $bestScore = 0.0;
        foreach ($candidates as $id => $nm) {
            $sc = self::score($name, $nm);
            if ($sc > $bestScore) { $bestScore = $sc; $bestId = $id; }
        }
        return [$bestId, $bestScore];
    }
}
