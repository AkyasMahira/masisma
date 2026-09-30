<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipeMahasiswaToMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up()
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        // Tambahkan kolom tipe_mahasiswa setelah prodi
        $table->enum('tipe_mahasiswa', ['magang', 'pkl'])->default('magang')->after('prodi');
    });
}

public function down()
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->dropColumn('tipe_mahasiswa');
    });
}
}
