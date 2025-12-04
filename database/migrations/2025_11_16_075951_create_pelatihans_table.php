<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelatihansTable extends Migration
{
    public function up()
    {
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('bidang', [
                'Keperawatan',
                'Pelayanan Medik',
                'Penunjang Klinik',
                'Penunjang Non Klinik',
                'Kepegawaian',
                'Perencanaan',
                'Keuangan'
            ])->nullable();
            $table->string('jabatan')->nullable();
            $table->string('unit')->nullable();
            $table->enum('status_pegawai', ['ASN', 'KARYAWAN TETAP', 'NON ASN']);
            $table->string('nip')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('golongan')->nullable();
            $table->string('nirp')->nullable();
            $table->text('pelatihan_dasar')->nullable();
            $table->text('pelatihan_peningkatan_kompetensi')->nullable(); // Baru
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pelatihans');
    }
}
