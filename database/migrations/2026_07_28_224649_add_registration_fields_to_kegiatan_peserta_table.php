<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationFieldsToKegiatanPesertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('kegiatan_peserta', function (Blueprint $table) {
            // Data Penanggung Jawab / Instansi Eksternal
            $table->string('email_pj')->nullable();
            $table->string('no_hp_pj')->nullable();
            $table->text('alamat_instansi')->nullable();
            
            // Data Pribadi Peserta
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('jabatan')->nullable();
            $table->string('email_plataran_sehat')->nullable();
            $table->string('no_hp_peserta')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('status_pegawai')->nullable();
            $table->string('nip')->nullable();
            $table->string('pangkat_golongan')->nullable();
            
            // Preferensi Pelatihan
            $table->string('metode_pelatihan')->nullable(); // Daring / Blended
            $table->string('ukuran_kaos')->nullable();
            $table->boolean('punya_akun_lms')->default(false);
            
            // Administrasi
            $table->string('bukti_bayar')->nullable();
            $table->string('status_pendaftaran')->default('pending'); // pending, terverifikasi
        });
    }

    public function down()
    {
        Schema::table('kegiatan_peserta', function (Blueprint $table) {
            $table->dropColumn([
                'email_pj', 'no_hp_pj', 'alamat_instansi', 'tempat_lahir', 'tanggal_lahir', 'nik',
                'jabatan', 'email_plataran_sehat', 'no_hp_peserta', 'pendidikan_terakhir',
                'status_pegawai', 'nip', 'pangkat_golongan', 'metode_pelatihan', 'ukuran_kaos',
                'punya_akun_lms', 'bukti_bayar', 'status_pendaftaran'
            ]);
        });
    }
}
