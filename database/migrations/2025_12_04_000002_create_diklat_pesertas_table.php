<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiklatPesertasTable extends Migration
{
    public function up()
    {
        Schema::create('diklat_pesertas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diklat_form_id');
            $table->string('nama_lengkap');
            $table->string('gelar')->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nik');
            $table->string('email');
            $table->string('nip')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('jabatan');
            $table->string('instansi');
            $table->text('alamat');
            $table->string('no_hp');
            $table->json('pilihan_pelatihan');
            $table->json('pilihan_tempat');
            $table->string('ukuran_kaos');
            $table->string('pas_foto')->nullable()->comment('Foto 4x6 Background Merah');
            $table->string('bukti_pembayaran');
            $table->json('jawaban_custom')->nullable();
            $table->timestamps();
            $table->foreign('diklat_form_id')->references('id')->on('diklat_forms')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('diklat_pesertas');
    }
}
