<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_peserta_id')->constrained('kegiatan_peserta')->onDelete('cascade');
            $table->foreignId('item_penilaian_id')->constrained('item_penilaian_skill')->onDelete('cascade');
            $table->foreignId('fasilitator_id')->constrained('kegiatan_fasilitator')->onDelete('cascade');
            $table->decimal('nilai', 3, 1); // 0/1 (centang) atau 0/1/2 (skor)
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['kegiatan_peserta_id', 'item_penilaian_id'], 'uniq_peserta_item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_skill');
    }
}