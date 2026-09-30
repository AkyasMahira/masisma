<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanPenilaianSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan_penilaian_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
            $table->enum('jenis_penilaian', ['centang', 'skor'])->default('centang');
            // centang: nilai tiap item 0/1 (Tidak Kompeten / Kompeten)
            // skor: nilai tiap item 0,1,2
            $table->decimal('batas_lulus_persen', 5, 2)->default(80.00);
            $table->timestamps();

            $table->unique('kegiatan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kegiatan_penilaian_settings');
    }
}