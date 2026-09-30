<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEvaluasiTables extends Migration
{
    public function up()
    {
        // Master unsur/pertanyaan evaluasi (dinamis, bisa di-CRUD admin)
        Schema::create('master_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->nullable();          // mis. U1..U9
            $table->text('pertanyaan');
            $table->enum('tipe', ['rating', 'text'])->default('rating'); // rating 1-4 utuk IKM, text = isian
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Tanggapan responden
        Schema::create('evaluasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('instansi')->nullable();
            $table->string('kontak')->nullable();              // no. HP / email
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('umur', 10)->nullable();
            $table->string('nama_kegiatan')->nullable();       // diklat yang dievaluasi (opsional)
            $table->text('kritik')->nullable();
            $table->text('saran')->nullable();
            $table->decimal('nilai_ikm', 5, 2)->nullable();    // IKM tanggapan ini (0-100), cache
            $table->timestamps();
        });

        // Jawaban per unsur (relasi evaluasi x master_evaluasi)
        Schema::create('evaluasi_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluasi_id')->constrained('evaluasis')->onDelete('cascade');
            $table->foreignId('master_evaluasi_id')->constrained('master_evaluasi')->onDelete('cascade');
            $table->unsignedTinyInteger('nilai')->nullable();  // 1-4 untuk tipe rating
            $table->text('jawaban_text')->nullable();          // untuk tipe text
            $table->timestamps();
        });

        // Seed 9 unsur IKM standar (Permenpan RB No. 14 Tahun 2017)
        $now = now();
        $unsur = [
            'Kesesuaian persyaratan pelayanan diklat dengan jenis pelayanannya',
            'Kemudahan prosedur pelayanan diklat',
            'Kecepatan waktu dalam memberikan pelayanan diklat',
            'Kewajaran biaya/tarif dalam pelayanan diklat',
            'Kesesuaian produk pelayanan diklat dengan yang tercantum',
            'Kompetensi/kemampuan petugas/instruktur dalam pelayanan',
            'Kesopanan dan keramahan petugas dalam memberikan pelayanan',
            'Kualitas sarana dan prasarana diklat',
            'Penanganan pengaduan, saran, dan masukan',
        ];
        $rows = [];
        foreach ($unsur as $i => $p) {
            $rows[] = [
                'kode' => 'U' . ($i + 1),
                'pertanyaan' => $p,
                'tipe' => 'rating',
                'urutan' => $i + 1,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('master_evaluasi')->insert($rows);
    }

    public function down()
    {
        Schema::dropIfExists('evaluasi_jawaban');
        Schema::dropIfExists('evaluasis');
        Schema::dropIfExists('master_evaluasi');
    }
}
