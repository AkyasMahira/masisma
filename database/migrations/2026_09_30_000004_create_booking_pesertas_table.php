<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingPesertasTable extends Migration
{
    public function up()
    {
        Schema::create('booking_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_ruangan_id')->constrained('booking_ruangans')->onDelete('cascade');
            $table->string('nama');
            $table->string('nim')->nullable();
            $table->string('prodi')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('no_hp')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_pesertas');
    }
}
