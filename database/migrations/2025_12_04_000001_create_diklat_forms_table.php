<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiklatFormsTable extends Migration
{
    public function up()
    {
        Schema::create('diklat_forms', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->date('tanggal_pelaksanaan');
            $table->text('keterangan')->nullable();
            $table->text('peraturan')->nullable();
            $table->json('opsi_pelatihan'); // Menyimpan array opsi
            $table->json('opsi_tempat');    // Menyimpan array tempat
            $table->json('pertanyaan_custom')->nullable();
            $table->string('public_link')->unique();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('diklat_forms');
    }
}
