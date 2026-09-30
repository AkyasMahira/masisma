<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrientasiResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 // database/migrations/xxxx_xx_xx_create_orientasi_results_table.php
public function up()
{
    Schema::create('orientasi_results', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->integer('pre_test_score')->nullable();
        $table->integer('post_test_score')->nullable();
        // Status: 'belum_lulus_pre', 'lulus_pre', 'lulus_orientasi'
        $table->string('status')->default('belum_lulus_pre'); 
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}
}
