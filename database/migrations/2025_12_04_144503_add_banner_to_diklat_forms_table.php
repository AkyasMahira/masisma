<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerToDiklatFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('diklat_forms', function (Blueprint $table) {
        $table->string('banner_path')->nullable()->after('judul');
    });
}

public function down()
{
    Schema::table('diklat_forms', function (Blueprint $table) {
        $table->dropColumn('banner_path');
    });
}
}
