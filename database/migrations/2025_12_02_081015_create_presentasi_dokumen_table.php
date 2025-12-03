<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresentasiDokumenTable extends Migration
{
    public function up()
    {
        Schema::create('presentasi_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presentasi_id')->constrained('presentasi')->onDelete('cascade');
            $table->string('nama_lengkap'); // Nama anggota
            $table->string('surat_selesai')->nullable(); // Path PDF
            $table->string('sertifikat')->nullable(); // Path PDF
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('presentasi_dokumen');
    }
}