<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanKompetensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up() {
    Schema::create('kegiatan_kompetensi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
        $table->foreignId('kompetensi_id')->constrained('master_kompetensi')->onDelete('cascade');
        $table->text('indikator_keberhasilan')->nullable();
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
        Schema::dropIfExists('kegiatan_kompetensi');
    }
}
