<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanTable extends Migration
{
    public function up()
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->enum('jenis_kegiatan', ['internal', 'eksternal']);
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->foreignId('penyelenggara_id')->constrained('master_instansi')->cascadeOnDelete();
            $table->string('platform');
            $table->enum('tipe_absen', ['masuk_saja', 'masuk_keluar']);
            $table->string('token_absensi')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kegiatan');
    }
}
