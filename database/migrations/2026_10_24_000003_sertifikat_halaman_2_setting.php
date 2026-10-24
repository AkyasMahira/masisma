<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Saklar sertifikat pelatihan: tampilkan halaman 2 (tabel materi & JP) atau tidak. */
class SertifikatHalaman2Setting extends Migration
{
    public function up()
    {
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->boolean('sertifikat_halaman_2')->default(true)->after('pakai_posttest');
        });
    }

    public function down()
    {
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->dropColumn('sertifikat_halaman_2');
        });
    }
}
