<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanTujuansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up() {
    Schema::create('kegiatan_tujuan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
        $table->text('tujuan');
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
        Schema::dropIfExists('kegiatan_tujuans');
    }
}
