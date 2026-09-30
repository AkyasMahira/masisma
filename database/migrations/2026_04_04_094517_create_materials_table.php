<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
    public function up()
    {
        // 1. Tabel Induk: Materials
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('external_link')->nullable(); // Opsi jika admin ingin input link saja
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 2. Tabel Anak: Material Files (Bisa nampung banyak file per materi)
        Schema::create('material_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->string('file_name'); // Nama asli file (Contoh: modul-ppi.pdf)
            $table->string('file_path'); // Lokasi file di folder storage
            $table->string('file_type'); // Ekstensi (pdf, mp4, pptx, dll)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_files');
        Schema::dropIfExists('materials');
    }
}