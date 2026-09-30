<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCiId2ToPengajuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('pengajuan', function (Blueprint $table) {
        $table->foreignId('ci_id_2')
              ->nullable()
              ->after('ci_id') // Letakkan tepat di bawah ci_id yang lama
              ->constrained('corporate_instructors')
              ->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            //
        });
    }
}
