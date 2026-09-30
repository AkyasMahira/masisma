<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSchemaForComplexAbsensi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
     
        // 2. Tambah Device ID di User (Untuk Lock HP)
        Schema::table('users', function (Blueprint $table) {
            $table->string('device_id')->nullable()->after('password');
        });

        // 3. Tabel Jadwal Shift Harian (Diisi Mahasiswa)
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->date('tanggal');
            // Pagi, Siang, Malam, Libur
            $table->string('shift_type'); 
            $table->timestamps();
            
            // Mencegah duplikasi jadwal di tanggal yang sama untuk mhs yang sama
            $table->unique(['mahasiswa_id', 'tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift_schedules');
        Schema::table('ruangans', function (Blueprint $table) { $table->dropColumn('kategori'); });
        Schema::table('users', function (Blueprint $table) { $table->dropColumn('device_id'); });
    }
}
