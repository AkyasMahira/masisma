<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEthicalClearanceToPraPenelitiansTable extends Migration
{
    public function up()
    {
        Schema::table('pra_penelitians', function (Blueprint $table) {
            $table->string('file_ethical_clearance')
                ->nullable()
                ->after('file_surat_pengantar');
        });
    }

    public function down()
    {
        Schema::table('pra_penelitians', function (Blueprint $table) {
            $table->dropColumn('file_ethical_clearance');
        });
    }
}
