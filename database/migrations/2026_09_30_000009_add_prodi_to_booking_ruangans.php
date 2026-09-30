<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProdiToBookingRuangans extends Migration
{
    public function up()
    {
        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->string('jenjang', 20)->nullable()->after('ruangan_id');
            $table->string('prodi')->nullable()->after('jenjang');
            $table->string('semester', 20)->nullable()->after('prodi');
        });
    }

    public function down()
    {
        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->dropColumn(['jenjang', 'prodi', 'semester']);
        });
    }
}
