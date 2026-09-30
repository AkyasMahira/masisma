<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPelatihansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::create('master_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelatihan');
            $table->string('kategori')->nullable();
            $table->string('durasi')->nullable();
            $table->string('sasaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_pelatihans');
    }
}
