<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterKompetensisTable extends Migration
{
   public function up()
    {
        Schema::create('master_kompetensi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kompetensi');
            $table->text('deskripsi_default')->nullable(); // Opsional, jika kompetensi punya standar deskripsi
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_kompetensi');
    }
}
