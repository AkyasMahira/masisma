<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalerisTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('galeris')) {
            Schema::create('galeris', function (Blueprint $table) {
                $table->id();
                $table->string('path');            // lokasi file foto
                $table->string('judul')->nullable(); // caption/judul opsional
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('galeris');
    }
}
