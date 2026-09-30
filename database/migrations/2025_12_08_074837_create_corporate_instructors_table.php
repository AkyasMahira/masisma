<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateInstructorsTable extends Migration
{
    public function up()
    {
        Schema::create('corporate_instructors', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->string('bidang');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('corporate_instructors');
    }
}
