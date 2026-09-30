<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPenilaianSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_penilaian_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
            $table->string('kategori')->nullable(); // contoh: "DANGER", "RESPONSE", dst (opsional, buat grouping ala BTCLS)
            $table->integer('urutan')->default(0);
            $table->text('aspek_tindakan'); // teks item observasi/tindakan yang dinilai
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
        Schema::dropIfExists('item_penilaian_skill');
    }
}