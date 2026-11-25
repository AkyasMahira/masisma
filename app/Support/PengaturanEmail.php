<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

/**
 * Akun Gmail pengirim (SMTP) yang bisa diganti admin dari aplikasi, tanpa mengedit .env.
 * Disimpan di storage/app/pengaturan_email.json; App Password dienkripsi dengan APP_KEY.
 * Bila file tidak ada, aplikasi memakai nilai MAIL_* di .env.
 */
class PengaturanEmail
{
    private static function path()
    {
        return storage_path('app/pengaturan_email.json');
    }

    public static function baca()
    {
        $f = self::path();
        if (!is_file($f)) return null;
        $d = json_decode((string) @file_get_contents($f), true);
        return is_array($d) ? $d : null;
    }

    /** Password asli (didekripsi) atau null. */
    public static function password(?array $d = null)
    {
        $d = $d ?? self::baca();
        if (empty($d['password'])) return null;
        try { return Crypt::decryptString($d['password']); } catch (\Throwable $e) { return null; }
    }

    public static function simpan($username, $passwordPolos, $namaPengirim, $olehUserId)
    {
        $d = [
            'username' => $username,
            'password' => Crypt::encryptString($passwordPolos),
            'from_name' => $namaPengirim,
            'diubah_at' => now()->toDateTimeString(),
            'diubah_oleh' => $olehUserId,
        ];
        @mkdir(dirname(self::path()), 0775, true);
        file_put_contents(self::path(), json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        @chmod(self::path(), 0640);
        self::terapkan($d);
    }

    /** Timpa config mail runtime dengan pengaturan tersimpan. */
    public static function terapkan(?array $d = null)
    {
        $d = $d ?? self::baca();
        if (!$d || empty($d['username'])) return;
        $pw = self::password($d);
        if ($pw === null) return;
        config([
            'mail.mailers.smtp.username' => $d['username'],
            'mail.mailers.smtp.password' => $pw,
            'mail.from.address' => $d['username'],
            'mail.from.name' => $d['from_name'] ?: config('mail.from.name'),
        ]);
    }

    /** Coba login SMTP. Mengembalikan null bila berhasil, atau pesan error. */
    public static function uji($username, $password)
    {
        try {
            $t = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                config('mail.mailers.smtp.host', 'smtp.gmail.com'),
                (int) config('mail.mailers.smtp.port', 587),
                (int) config('mail.mailers.smtp.port', 587) === 465
            );
            $t->setUsername($username);
            $t->setPassword($password);
            $t->start();
            $t->stop();
            return null;
        } catch (\Throwable $e) {
            $m = $e->getMessage();
            if (str_contains($m, '535') || stripos($m, 'Username and Password not accepted') !== false) {
                return 'Gmail menolak login: email atau App Password salah. Pastikan memakai App Password 16 huruf (bukan password Gmail biasa).';
            }
            return 'Tidak bisa terhubung ke server email: ' . mb_substr($m, 0, 200);
        }
    }
}
