<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAbsenToKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            // otomatis, buka, tutup
            $table->enum('status_absen', ['otomatis', 'buka', 'tutup'])->default('otomatis')->after('token_absensi');
        });
    }

    public function down()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropColumn('status_absen');
        });
    }
}
