<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('mahasiswa_id');

            // jenis absen
            $table->enum('type', ['masuk', 'keluar']);

            // waktu absen
            $table->dateTime('jam_masuk')->nullable();
            $table->dateTime('jam_keluar')->nullable();

            // durasi total menit (untuk absen keluar)
            $table->integer('durasi_menit')->nullable();

            // === LOKASI (GEOFENCE) ===
            $table->decimal('latitude', 11, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            // akurasi dari browser (meter)
            $table->integer('location_accuracy')->nullable();

            $table->timestamps();

            $table
                ->foreign('mahasiswa_id')
                ->references('id')
                ->on('mahasiswas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absensis');
    }
}
