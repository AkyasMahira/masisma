<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisMahasiswaToPraPenelitiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('pra_penelitians', function (Blueprint $table) {
        // Menambahkan kolom jenis_mahasiswa setelah kolom status
        $table->enum('jenis_mahasiswa', ['Internal', 'Eksternal'])->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('pra_penelitians', function (Blueprint $table) {
        $table->dropColumn('jenis_mahasiswa');
    });
}
}
