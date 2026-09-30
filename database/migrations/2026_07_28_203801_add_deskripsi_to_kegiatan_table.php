<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeskripsiToKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up() {
    Schema::table('kegiatan', function (Blueprint $table) {
        $table->text('deskripsi')->nullable()->after('nama_kegiatan');
    });
}
public function down() {
    Schema::table('kegiatan', function (Blueprint $table) {
        $table->dropColumn('deskripsi');
    });
}
}
