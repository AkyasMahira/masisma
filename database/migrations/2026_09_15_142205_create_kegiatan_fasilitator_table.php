<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanFasilitatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan_fasilitator', function (Blueprint $table) {
            $table->id();
      $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
            $table->string('nama_fasilitator');
            $table->string('no_hp')->nullable();
            $table->uuid('token_fasilitator')->unique();
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
        Schema::dropIfExists('kegiatan_fasilitator');
    }
}