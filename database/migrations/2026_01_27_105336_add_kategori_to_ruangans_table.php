<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriToRuangansTable extends Migration
{
    
public function up()
{
    // 1. Tabel Jadwal Harian (Detail Shift)
    Schema::create('shift_schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
        $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
        $table->date('tanggal'); // 2026-01-27
        // Jenis Shift: Pagi, Siang, Malam, Libur, Non-Shift
        $table->string('shift_type'); 
        $table->timestamps();
    });

    // 2. Kolom Device ID untuk Lock HP
    Schema::table('users', function (Blueprint $table) {
        $table->string('device_id')->nullable()->after('password');
    });
}

public function down()
{
    Schema::dropIfExists('shift_schedules');
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('device_id');
    });
}
}
