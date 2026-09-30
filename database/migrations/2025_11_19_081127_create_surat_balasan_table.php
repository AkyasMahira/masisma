<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratBalasanTable extends Migration
{
    public function up()
    {
        Schema::create('surat_balasan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mou_id')
                ->constrained('mous')
                ->onDelete('cascade');

            $table->string('nama_mahasiswa');
            $table->string('nim');
            $table->string('wa_mahasiswa');
            $table->string('keperluan');
            $table->string('prodi');
            $table->string('lama_berlaku');
            $table->json('data_dibutuhkan');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_balasan');
    }
}
