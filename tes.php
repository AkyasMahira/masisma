<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

// 1. Matikan sementara gembok relasi (Foreign Key)
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// 2. Hapus kedua tabel yang saling terkait
Schema::dropIfExists('absensi_kegiatan');
Schema::dropIfExists('kegiatan_peserta');

// 3. Buat ulang tabel kegiatan_peserta dengan sempurna
Schema::create('kegiatan_peserta', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('kegiatan_id'); // Kolom yang tadi hilang
    $table->string('nama_lengkap_gelar');
    $table->string('profesi')->nullable();
    $table->unsignedBigInteger('instansi_id')->nullable();
    $table->unsignedBigInteger('ruangan_id')->nullable();
    $table->timestamps();
    
    // Sambungkan ke tabel kegiatan
    $table->foreign('kegiatan_id')->references('id')->on('kegiatan')->onDelete('cascade');
});

// 4. Buat ulang tabel absensi_kegiatan
Schema::create('absensi_kegiatan', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('kegiatan_id');
    $table->unsignedBigInteger('kegiatan_peserta_id');
    $table->dateTime('waktu_masuk')->nullable();
    $table->dateTime('waktu_keluar')->nullable();
    $table->timestamps();

    // Sambungkan relasinya
    $table->foreign('kegiatan_id')->references('id')->on('kegiatan')->onDelete('cascade');
    $table->foreign('kegiatan_peserta_id')->references('id')->on('kegiatan_peserta')->onDelete('cascade');
});

// 5. Hidupkan kembali gembok relasi
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Selesai! Tabel kegiatan_peserta dan absensi_kegiatan berhasil dirombak total dan siap digunakan!" . PHP_EOL;