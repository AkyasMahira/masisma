<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Booking instansi: ruangan hanya dipesan untuk MINGGU PERTAMA (ruangan_selesai),
 * sedangkan tanggal_mulai–tanggal_selesai tetap = periode magang penuh.
 * Rolling berikutnya diatur admin / karu / mahasiswa. NULL = ruangan dipakai seluruh periode.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('booking_ruangans', function (Blueprint $t) {
            $t->date('ruangan_selesai')->nullable()->after('tanggal_selesai');
        });
    }

    public function down()
    {
        Schema::table('booking_ruangans', function (Blueprint $t) {
            $t->dropColumn('ruangan_selesai');
        });
    }
};
