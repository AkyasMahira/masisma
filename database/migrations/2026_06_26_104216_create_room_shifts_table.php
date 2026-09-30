<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::create('room_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->string('nama_shift'); // Contoh: 'Pagi', 'Siang', 'Malam', 'Reguler', 'Jumat'
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->boolean('lintas_hari')->default(false); // True jika jam keluar ada di keesokan harinya (Shift Malam)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_shifts');
    }
}
