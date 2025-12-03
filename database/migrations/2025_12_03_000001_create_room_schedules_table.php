<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomSchedulesTable extends Migration{
    public function up(): void
    {
        Schema::create('room_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');
            $table->unsignedBigInteger('ruangan_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'waiting', 'completed'])->default('waiting');
            $table->timestamps();

            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas')->onDelete('cascade');
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_schedules');
    }
};
