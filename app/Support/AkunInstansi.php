<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;

/**
 * Kredensial akun portal instansi mitra (MOU).
 * Username: <jenis>.<nama>@sindikat.com (mis. univ.kadiri@sindikat.com)
 * Password: mudah dibaca (mis. Kenanga4821). Disimpan hash untuk login + terenkripsi
 * (APP_KEY) agar admin bisa melihatnya lagi.
 */
class AkunInstansi
{
    const DOMAIN = 'sindikat.com';

    private static $jenis = [
        'sekolah tinggi ilmu kesehatan' => 'stikes', 'institut ilmu kesehatan' => 'iik',
        'institut teknologi sains dan kesehatan' => 'itskes', 'politeknik kesehatan' => 'poltekkes',
        'sekolah tinggi ilmu administrasi' => 'stia', 'sekolah tinggi ilmu ekonomi' => 'stie',
        'akademi keperawatan' => 'akper', 'akademi kebidanan' => 'akbid', 'universitas' => 'univ',
        'university' => 'univ', 'politeknik' => 'poltek', 'institut' => 'institut',
        'sekolah menengah kejuruan' => 'smk', 'rumah sakit umum daerah' => 'rsud', 'rumah sakit' => 'rs',
    ];
    private static $singkatanJenis = ['univ', 'stikes', 'iik', 'itskes', 'poltekkes', 'poltek', 'stia', 'stie', 'akper', 'akbid', 'institut', 'smk', 'sma', 'rsud', 'rs', 'puskesmas', 'klinik', 'upt'];
    private static $buang = ['dan', 'di', 'kabupaten', 'kab', 'kota', 'negeri', 'the', 'of', 'kemenkes', 'kementerian', 'kesehatan'];

    /** Saran bagian depan username dari nama instansi, mis. "univ.kadiri". */
    public static function saranUsername($namaInstansi)
    {
        $s = mb_strtolower((string) $namaInstansi);
        $s = preg_replace('/\(.*?\)|\(.*$/u', ' ', $s);
        foreach (self::$jenis as $panjang => $pendek) $s = str_replace($panjang, ' ' . $pendek . ' ', $s);
        $s = preg_replace('/[^a-z0-9 ]/', ' ', $s);
        $kata = array_values(array_filter(explode(' ', $s), function ($w) { return $w !== '' && !in_array($w, self::$buang, true); }));
        if (!$kata) return 'instansi';

        $tipe = null;
        foreach ($kata as $i => $w) { if (in_array($w, self::$singkatanJenis, true)) { $tipe = $w; unset($kata[$i]); break; } }
        $kata = array_values($kata);
        if (count($kata) === 0) $inti = $tipe ?: 'instansi';
        elseif (count($kata) <= 2) $inti = implode('', $kata);
        else $inti = implode('', array_map(function ($w) { return $w[0]; }, $kata)); // singkatan huruf depan

        $u = $tipe && $inti !== $tipe ? $tipe . '.' . $inti : $inti;
        return substr(trim($u, '.'), 0, 30);
    }

    /** Bersihkan input username admin menjadi bagian depan yang valid. */
    public static function bersihkan($lokal)
    {
        $lokal = mb_strtolower(trim((string) $lokal));
        $lokal = preg_replace('/@.*$/', '', $lokal);
        $lokal = preg_replace('/[^a-z0-9.\-]/', '', $lokal);
        return trim(preg_replace('/\.{2,}/', '.', $lokal), '.-');
    }

    /** Email login unik: <lokal>@sindikat.com (tambah angka bila sudah dipakai). */
    public static function emailUnik($lokal, $kecualiUserId = null)
    {
        $lokal = self::bersihkan($lokal) ?: 'instansi';
        $email = $lokal . '@' . self::DOMAIN; $i = 2;
        while (User::where('email', $email)->when($kecualiUserId, function ($q) use ($kecualiUserId) { $q->where('id', '!=', $kecualiUserId); })->exists()) {
            $email = $lokal . $i . '@' . self::DOMAIN; $i++;
        }
        return $email;
    }

    /** Password mudah dibaca & diketik, mis. "Kenanga4821". */
    public static function passwordBaru()
    {
        $kata = ['Melati', 'Mawar', 'Anggrek', 'Kenanga', 'Dahlia', 'Teratai', 'Cempaka', 'Kamboja', 'Tulip', 'Lavender',
                 'Merpati', 'Rajawali', 'Cendana', 'Jati', 'Beringin', 'Pelangi', 'Samudra', 'Mentari', 'Bintang', 'Purnama'];
        return $kata[random_int(0, count($kata) - 1)] . random_int(1000, 9999);
    }

    public static function enkripsi($password)
    {
        return Crypt::encryptString($password);
    }

    /** Password yang bisa ditampilkan ke admin, atau null bila tidak tersedia. */
    public static function passwordTampil(User $user)
    {
        if (!$user->password_tampil) return null;
        try { return Crypt::decryptString($user->password_tampil); } catch (\Throwable $e) { return null; }
    }
}
