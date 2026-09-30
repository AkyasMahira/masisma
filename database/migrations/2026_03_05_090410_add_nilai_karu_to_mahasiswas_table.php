<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiKaruToMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Menambahkan kolom nilai_karu dengan pilihan A, B, C, D. Boleh kosong (nullable)
            $table->enum('nilai_karu', ['A', 'B', 'C', 'D'])->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('nilai_karu');
        });
    }
}
