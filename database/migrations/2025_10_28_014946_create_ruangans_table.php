<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PASTIKAN NAMA CLASS-NYA "CreateRuangansTable"
class CreateRuangansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Kita membuat tabel bernama 'ruangans' (plural)
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nm_ruangan');
            $table->integer('kuota_ruangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ruangans');
    }
}
