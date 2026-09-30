<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEditedToMahasiswasTable extends Migration
{
   public function up()
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        // Default 0 (Belum pernah edit)
        $table->boolean('is_edited')->default(false)->after('status'); 
    });
}

public function down()
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->dropColumn('is_edited');
    });
}
}
