<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRoomSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('room_sequences', function (Blueprint $table) {
        $table->dropColumn('urutan');       // Hapus kolom urutan
        $table->date('start_date');         // Tambah tanggal mulai
        $table->date('end_date');           // Tambah tanggal selesai
    });
}

public function down()
{
    Schema::table('room_sequences', function (Blueprint $table) {
        $table->integer('urutan');
        $table->dropColumn(['start_date', 'end_date']);
    });
}
}
