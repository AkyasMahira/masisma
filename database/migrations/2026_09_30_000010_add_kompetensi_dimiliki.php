<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKompetensiDimiliki extends Migration
{
    /**
     * Dua jenis kompetensi:
     * - kompetensi_json           = yang INGIN dikuasai (diisi mahasiswa di edit profil)
     * - kompetensi_dimiliki_json  = yang DIMILIKI (diisi instansi saat booking, auto ke semua mhs)
     */
    public function up()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->json('kompetensi_dimiliki_json')->nullable()->after('kompetensi_json');
        });
        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->json('kompetensi_dimiliki_json')->nullable()->after('semester');
        });
    }

    public function down()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('kompetensi_dimiliki_json');
        });
        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->dropColumn('kompetensi_dimiliki_json');
        });
    }
}
