<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTampilDiSertifikatToKegiatanMateri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('kegiatan_materi', function (Blueprint $table) {
        // Default 1 (Otomatis tampil di sertifikat)
        $table->boolean('tampil_di_sertifikat')->default(true)->after('butuh_penilaian');
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
