<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToBookingPesertas extends Migration
{
    public function up()
    {
        Schema::table('booking_pesertas', function (Blueprint $table) {
            // Alur ACC admin (mirip pengajuan magang):
            // pending = diinput instansi, menunggu; approved = jadi akun mahasiswa; rejected = ditolak
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('keterangan');
            $table->text('catatan_admin')->nullable()->after('status');
            $table->unsignedBigInteger('user_id')->nullable()->after('catatan_admin');       // akun login yang dibuat
            $table->unsignedBigInteger('mahasiswa_id')->nullable()->after('user_id');         // record mahasiswa yang dibuat
        });
    }

    public function down()
    {
        Schema::table('booking_pesertas', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_admin', 'user_id', 'mahasiswa_id']);
        });
    }
}
