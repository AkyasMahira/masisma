<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Saklar rekap kelulusan pelatihan: pakai nilai Post-Test atau hanya nilai skill. */
class PakaiPosttestPenilaianSetting extends Migration
{
    public function up()
    {
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->boolean('pakai_posttest')->default(true)->after('batas_lulus_persen');
        });
    }

    public function down()
    {
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->dropColumn('pakai_posttest');
        });
    }
}
