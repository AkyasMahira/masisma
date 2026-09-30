<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterRuanganTable extends Migration
{
   public function up()
    {
        Schema::create('master_ruangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instansi_id')->constrained('master_instansi')->cascadeOnDelete();
            $table->string('nama_ruangan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_ruangan');
    }
}
