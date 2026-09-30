<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKompetensiJsonToMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Menambahkan kolom json, posisinya bisa disesuaikan (misal setelah kolom prodi)
            $table->json('kompetensi_json')->nullable()->after('prodi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('kompetensi_json');
        });
    }
}
