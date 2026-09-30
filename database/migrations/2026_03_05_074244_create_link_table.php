<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkTable extends Migration
{
  public function up()
{
    
    Schema::create('link_packages', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique()->index(); 
        $table->string('image')->nullable();
        $table->boolean('is_active')->default(true)->index();
        $table->timestamps();
    });

    Schema::create('link_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('package_id')->constrained('link_packages')->onDelete('cascade');
        $table->string('title');
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });


    Schema::create('link_contents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('item_id')->constrained('link_items')->onDelete('cascade');
        $table->enum('type', ['link', 'file', 'image']);
        $table->string('label'); // Nama tampilan (misal: "Download PDF")
        $table->text('value'); // URL atau Path File
        $table->timestamps();
    });
}

   
    public function down()
    {
        Schema::dropIfExists('links');
    }
}
