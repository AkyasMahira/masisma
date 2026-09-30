<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('room_schedules', function (Blueprint $table) {
        $table->id();
        // Relasi ke Mahasiswa
        $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
        // Relasi ke Ruangan
        $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
        
        $table->date('start_date');
        $table->date('end_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_schedules');
    }
}
