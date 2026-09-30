<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiToKegiatanMateri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('kegiatan_materi', function (Blueprint $table) {
        $table->integer('nilai_teori')->nullable()->after('nama_materi');
        $table->integer('nilai_praktik')->nullable()->after('nilai_teori');
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
