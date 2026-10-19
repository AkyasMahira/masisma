<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Evaluasi narasumber (fasilitator) oleh peserta pelatihan.
 * Aspek penilaian disimpan di master_evaluasi dengan jenis = 'narasumber'.
 */
class CreateEvaluasiNarasumberTables extends Migration
{
    public function up()
    {
        Schema::create('evaluasi_narasumber', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $t->string('nama')->nullable();
            $t->string('profesi')->nullable();
            $t->date('tanggal_pengisian')->nullable();
            $t->text('saran')->nullable();
            $t->timestamps();
        });

        Schema::create('evaluasi_narasumber_nilai', function (Blueprint $t) {
            $t->id();
            $t->foreignId('evaluasi_narasumber_id')->constrained('evaluasi_narasumber')->cascadeOnDelete();
            $t->unsignedBigInteger('fasilitator_id')->nullable()->index();   // kegiatan_fasilitator.id
            $t->string('nama_narasumber');                                   // disalin agar laporan tetap utuh
            $t->unsignedBigInteger('master_evaluasi_id')->nullable()->index();
            $t->string('aspek');                                             // disalin
            $t->unsignedTinyInteger('nilai');                                // 1 Kurang, 2 Sedang, 3 Baik, 4 Sangat Baik
            $t->timestamps();
        });

        $now = now();
        $aspek = [
            'Penguasaan Materi', 'Sistem Penyajian', 'Kemampuan Menyajikan', 'Ketepatan Waktu Kehadiran dan Menyajikan',
            'Penggunaan Metode dan Sarana Diklat', 'Sikap dan Perilaku', 'Cara Menjawab Pertanyaan dari Peserta',
            'Penggunaan Bahasa', 'Pemberian Motivasi Kepada Peserta', 'Pencapaian Tujuan Pembelajaran',
            'Kerapian Berpakaian', 'Kerjasama Antar Tenaga Pengajar',
        ];
        foreach ($aspek as $i => $a) {
            DB::table('master_evaluasi')->insert([
                'jenis' => 'narasumber', 'kegiatan_id' => null, 'kode' => 'A' . ($i + 1), 'pertanyaan' => $a,
                'tipe' => 'rating', 'urutan' => $i + 1, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('evaluasi_narasumber_nilai');
        Schema::dropIfExists('evaluasi_narasumber');
        DB::table('master_evaluasi')->where('jenis', 'narasumber')->delete();
    }
}
