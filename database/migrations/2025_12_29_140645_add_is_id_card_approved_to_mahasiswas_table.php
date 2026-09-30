<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsIdCardApprovedToMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        // Default false artinya belum disetujui saat pertama dibuat
        $table->boolean('is_id_card_approved')->default(false)->after('status');
    });
}

public function down()
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->dropColumn('is_id_card_approved');
    });
}
}
