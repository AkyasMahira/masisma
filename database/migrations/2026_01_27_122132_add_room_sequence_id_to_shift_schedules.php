<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoomSequenceIdToShiftSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            // Menambahkan kolom FK room_sequence_id setelah id
            // nullable() ditambahkan untuk berjaga-jaga jika sudah ada data sebelumnya
            $table->foreignId('room_sequence_id')
                  ->after('id')
                  ->nullable() 
                  ->constrained('room_sequences')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            // Hapus foreign key dulu baru kolomnya
            $table->dropForeign(['room_sequence_id']);
            $table->dropColumn('room_sequence_id');
        });
    }
}
