<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaianDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('presentasi_id')->constrained('presentasi')->onDelete('cascade');
    $table->string('nama_ci'); // Nama CI yang mengisi
    $table->integer('skor_angka'); // 0 - 100
    $table->json('catatan')->nullable(); // Poin-poin penilaian
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
        Schema::dropIfExists('penilaian_details');
    }
}
