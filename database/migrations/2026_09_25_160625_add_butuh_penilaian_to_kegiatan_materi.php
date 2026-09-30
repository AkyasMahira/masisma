<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddButuhPenilaianToKegiatanMateri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up()
{
    Schema::table('kegiatan_materi', function (Blueprint $table) {
        // Default 1 (Ya, dinilai). Kalau 0 berarti hanya tampil di sertifikat
        $table->boolean('butuh_penilaian')->default(true)->after('nama_materi');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kegiatan_materi', function (Blueprint $table) {
            //
        });
    }
}
