<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiKegiatanTable extends Migration
{
   public function up()
    {
        Schema::create('absensi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('kegiatan_peserta_id')->constrained('kegiatan_peserta')->cascadeOnDelete();
            $table->enum('status_kehadiran', ['hadir', 'izin', 'tidak_hadir'])->default('hadir');
            $table->string('foto_bukti')->nullable();
            $table->dateTime('jam_masuk')->nullable();
            $table->dateTime('jam_pulang')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi_kegiatan');
    }
}
