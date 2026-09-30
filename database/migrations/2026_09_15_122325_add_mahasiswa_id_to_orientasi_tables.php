<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMahasiswaIdToOrientasiTables extends Migration
{
    public function up()
    {
        // 1. Tambah mahasiswa_id ke tabel orientasi_results
        Schema::table('orientasi_results', function (Blueprint $table) {
            $table->unsignedBigInteger('mahasiswa_id')->nullable()->after('user_id');
            
            // Opsional: Jika ingin otomatis terhapus saat data mahasiswa dihapus
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas')->onDelete('cascade');
        });

        // 2. Tambah mahasiswa_id ke tabel material_progress
        Schema::table('material_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('mahasiswa_id')->nullable()->after('user_id');
            
            // Opsional: Jika ingin otomatis terhapus saat data mahasiswa dihapus
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orientasi_results', function (Blueprint $table) {
            $table->dropForeign(['mahasiswa_id']);
            $table->dropColumn('mahasiswa_id');
        });

        Schema::table('material_progress', function (Blueprint $table) {
            $table->dropForeign(['mahasiswa_id']);
            $table->dropColumn('mahasiswa_id');
        });
    }
}