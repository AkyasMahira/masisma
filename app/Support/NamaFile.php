<?php

namespace App\Support;

/**
 * Nama file unduhan yang aman: Symfony 7.4+ menolak "/" dan "\" pada Content-Disposition
 * (mis. nomor invoice "INV/2026/..." atau gelar "S.Kep/Ns").
 */
class NamaFile
{
    public static function aman($nama)
    {
        $nama = str_replace(['/', '\\', '"'], '-', (string) $nama);
        $nama = preg_replace('/[\x00-\x1F\x7F]+/u', '', $nama);
        $nama = trim($nama);
        return $nama !== '' ? $nama : 'dokumen.pdf';
    }
}
