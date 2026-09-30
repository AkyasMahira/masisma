<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingRuangansTable extends Migration
{
    public function up()
    {
        Schema::create('booking_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mou_id')->constrained('mous')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // akun instansi pengaju
            $table->unsignedInteger('jumlah_peserta')->default(1);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_ruangans');
    }
}
