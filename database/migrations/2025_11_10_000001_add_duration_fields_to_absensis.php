<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationFieldsToAbsensis extends Migration
{
    public function up()
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->integer('durasi_menit')->nullable();
        });
    }

    public function down()
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('durasi_menit');
        });
    }
}
