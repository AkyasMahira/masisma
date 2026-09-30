<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToRuangansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('ruangans', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('id');
    });
}

public function down()
{
    Schema::table('ruangans', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
}
