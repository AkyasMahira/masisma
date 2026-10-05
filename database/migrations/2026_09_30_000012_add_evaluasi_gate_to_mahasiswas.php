<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluasiGateToMahasiswas extends Migration
{
    public function up()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Diisi saat mahasiswa menyelesaikan evaluasi magang (syarat unduh sertifikat)
            $table->timestamp('evaluasi_at')->nullable()->after('nilai_evaluasi');
        });
        Schema::table('evaluasis', function (Blueprint $table) {
            // Kaitkan tanggapan evaluasi ke mahasiswa/periode tertentu (nullable: evaluasi publik umum)
            $table->unsignedBigInteger('mahasiswa_id')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('evaluasi_at');
        });
        Schema::table('evaluasis', function (Blueprint $table) {
            $table->dropColumn('mahasiswa_id');
        });
    }
}
