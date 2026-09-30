<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganTerlambatToDispensasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up(): void
    {
        Schema::table('dispensasis', function (Blueprint $table) {
            $table->json('keterangan_terlambat')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('dispensasis', function (Blueprint $table) {
            $table->dropColumn('keterangan_terlambat');
        });
    }
}
