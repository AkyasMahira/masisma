<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToDiklatPesertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('diklat_pesertas', function (Blueprint $table) {
        $table->string('profesi')->nullable()->after('jabatan');
        $table->string('pendidikan_terakhir')->nullable()->after('profesi');
        $table->string('status_pegawai')->nullable()->after('pendidikan_terakhir');
        // Pastikan kolom email ada di tabel diklat_pesertas
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diklat_pesertas', function (Blueprint $table) {
            //
        });
    }
}
