<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiRuanganJsonToMahasiswasTable extends Migration
{
public function up()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Menambahkan kolom JSON, diletakkan setelah kolom nilai_karu lama
            $table->json('nilai_ruangan_json')->nullable()->after('nilai_karu');
        });
    }

    public function down()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('nilai_ruangan_json');
        });
    }
}
