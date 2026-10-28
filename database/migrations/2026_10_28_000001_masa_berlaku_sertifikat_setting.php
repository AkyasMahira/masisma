<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Masa berlaku sertifikat pelatihan per kegiatan (tahun). 0 = tidak dicantumkan / seumur hidup. Default 3 seperti sebelumnya. */
return new class extends Migration
{
    public function up()
    {
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->unsignedTinyInteger('masa_berlaku_tahun')->default(3)->after('sertifikat_halaman_2');
        });
    }

    public function down()
    {
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->dropColumn('masa_berlaku_tahun');
        });
    }
};
