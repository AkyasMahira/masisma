<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBiodataToBookingPesertas extends Migration
{
    /**
     * Lengkapi biodata peserta agar setara form mahasiswa
     * (email untuk akun, tipe magang/pkl, weekend, pas foto, kompetensi).
     */
    public function up()
    {
        Schema::table('booking_pesertas', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nim');
            $table->enum('tipe_mahasiswa', ['magang', 'pkl'])->default('magang')->after('prodi');
            $table->boolean('weekend_aktif')->default(false)->after('tipe_mahasiswa');
            $table->string('foto_path')->nullable()->after('no_hp');
            $table->json('kompetensi_json')->nullable()->after('foto_path');
        });
    }

    public function down()
    {
        Schema::table('booking_pesertas', function (Blueprint $table) {
            $table->dropColumn(['email', 'tipe_mahasiswa', 'weekend_aktif', 'foto_path', 'kompetensi_json']);
        });
    }
}
