<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileProposalToPraPenelitiansTable extends Migration
{
    public function up()
    {
        Schema::table('pra_penelitians', function (Blueprint $table) {
            $table->string('file_proposal')
                ->nullable()
                ->after('file_ethical_clearance');
        });
    }

    public function down()
    {
        Schema::table('pra_penelitians', function (Blueprint $table) {
            $table->dropColumn('file_proposal');
        });
    }
}
