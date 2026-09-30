<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiEvaluasiToMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->json('nilai_evaluasi')->nullable()->after('nilai_ruangan_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('nilai_evaluasi');
        });
    }
}
