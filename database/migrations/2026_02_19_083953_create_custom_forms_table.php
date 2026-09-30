<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    // Tabel Master Form
    Schema::create('custom_forms', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->json('fields'); // Menyimpan array tipe input, label, dan opsi
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    // Tabel Jawaban Peserta
    Schema::create('custom_form_responses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('custom_form_id')->constrained()->onDelete('cascade');
        $table->json('answers'); // Menyimpan jawaban peserta dalam format JSON
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
        Schema::dropIfExists('custom_form_responses');
        Schema::dropIfExists('custom_forms');
    }
}
