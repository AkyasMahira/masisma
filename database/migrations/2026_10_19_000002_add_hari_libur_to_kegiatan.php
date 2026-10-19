<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Pelatihan dengan hari tidak berurutan: tanggal di dalam rentang yang TIDAK ada sesi. */
class AddHariLiburToKegiatan extends Migration
{
    public function up()
    {
        Schema::table('kegiatan', function (Blueprint $t) {
            $t->text('hari_libur')->nullable()->after('tanggal_selesai'); // JSON ["2026-10-21","2026-10-22"]
        });
    }

    public function down()
    {
        Schema::table('kegiatan', function (Blueprint $t) {
            $t->dropColumn('hari_libur');
        });
    }
}
