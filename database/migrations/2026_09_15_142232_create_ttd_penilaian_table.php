<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTtdPenilaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ttd_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_peserta_id')->constrained('kegiatan_peserta')->onDelete('cascade');
            $table->foreignId('fasilitator_id')->constrained('kegiatan_fasilitator')->onDelete('cascade');
            $table->longText('tanda_tangan'); // base64 PNG dari signature pad
            $table->timestamp('waktu_ttd');
            $table->timestamps();

            $table->unique(['kegiatan_peserta_id', 'fasilitator_id'], 'uniq_ttd_peserta_fasilitator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ttd_penilaian');
    }
}