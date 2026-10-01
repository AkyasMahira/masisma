<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrientasiPegawaisTable extends Migration
{
    /**
     * Pencatatan orientasi pegawai. Data pegawai diambil dari API SDM;
     * tabel ini hanya menyimpan status orientasi per pegawai (key: nip).
     * Pegawai yang belum punya baris di sini = BELUM orientasi.
     */
    public function up()
    {
        Schema::create('orientasi_pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('pegawai_id')->nullable();        // id dari API (opsional)
            $table->string('nip')->index();                  // kunci pencocokan dengan API
            $table->string('nama')->nullable();
            $table->string('unit')->nullable();
            $table->enum('status', ['belum', 'sudah', 'lulus'])->default('sudah');
            $table->integer('pre_test_score')->nullable();
            $table->integer('post_test_score')->nullable();
            $table->year('tahun')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orientasi_pegawais');
    }
}
