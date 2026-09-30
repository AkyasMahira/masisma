<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterInstansiTable extends Migration
{
public function up()
    {
        Schema::create('master_instansi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_instansi');
    }
}
