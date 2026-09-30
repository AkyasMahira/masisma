<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispensasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 // database/migrations/xxxx_create_dispensasis_table.php
public function up()
{
    Schema::create('dispensasis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        $table->text('keterangan');
        $table->string('file_path'); // Lokasi file PDF
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('catatan_admin')->nullable(); // Jika ditolak/acc ada catatan
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
        Schema::dropIfExists('dispensasis');
    }
}
