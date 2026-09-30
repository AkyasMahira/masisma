<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiTeoriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_teori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_peserta_id')->constrained('kegiatan_peserta')->onDelete('cascade');
            $table->decimal('nilai_pretest', 5, 2)->nullable();
            $table->decimal('nilai_posttest', 5, 2)->nullable();
            $table->timestamps();

            $table->unique('kegiatan_peserta_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_teori');
    }
}