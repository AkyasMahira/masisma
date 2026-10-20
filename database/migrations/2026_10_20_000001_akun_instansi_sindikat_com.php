<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Support\AkunInstansi;

/**
 * Akun portal instansi:
 * - password_tampil: password buatan admin (terenkripsi APP_KEY) agar bisa dilihat lagi di admin
 * - reset_diminta_at: instansi menekan "Lupa Password" -> admin yang mereset
 * - akun lama @mitra.rsudslg.id diganti ke <jenis>.<nama>@sindikat.com + password baru
 *   (password lama ter-hash, tidak bisa ditampilkan)
 */
class AkunInstansiSindikatCom extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'password_tampil')) $t->text('password_tampil')->nullable();
            if (!Schema::hasColumn('users', 'reset_diminta_at')) $t->timestamp('reset_diminta_at')->nullable();
        });

        $akun = DB::table('users')->where('role', 'instansi')->orderBy('id')->get(['id', 'email', 'name', 'mou_id']);
        foreach ($akun as $u) {
            $mou = $u->mou_id ? DB::table('mous')->where('id', $u->mou_id)->first() : null;
            $nama = $mou ? ($mou->nama_instansi ?: $mou->nama_universitas) : $u->name;
            $email = AkunInstansi::emailUnik(AkunInstansi::saranUsername($nama), $u->id);
            $pass = AkunInstansi::passwordBaru();
            DB::table('users')->where('id', $u->id)->update([
                'email' => $email,
                'password' => Hash::make($pass),
                'password_tampil' => AkunInstansi::enkripsi($pass),
                'remember_token' => null,
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['password_tampil', 'reset_diminta_at']);
        });
    }
}
