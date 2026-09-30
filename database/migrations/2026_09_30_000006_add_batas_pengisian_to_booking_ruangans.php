<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatasPengisianToBookingRuangans extends Migration
{
    public function up()
    {
        Schema::table('booking_ruangans', function (Blueprint $table) {
            // Batas tanggal instansi boleh mengisi/menambah peserta magang (di-set admin).
            // Null = tanpa batas.
            $table->date('batas_pengisian')->nullable()->after('catatan_admin');
        });
    }

    public function down()
    {
        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->dropColumn('batas_pengisian');
        });
    }
}
