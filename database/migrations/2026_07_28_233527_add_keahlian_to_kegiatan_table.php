<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeahlianToKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->json('keahlian')->nullable()->after('deskripsi');
        });
    }

    public function down()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropColumn('keahlian');
        });
    }
}
