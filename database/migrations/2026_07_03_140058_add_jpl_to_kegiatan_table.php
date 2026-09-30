<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJplToKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up()
{
    Schema::table('kegiatan', function (Blueprint $table) {
        $table->integer('jpl')->nullable()->after('tipe_absen');
    });
}

public function down()
{
    Schema::table('kegiatan', function (Blueprint $table) {
        $table->dropColumn('jpl');
    });
}
}
