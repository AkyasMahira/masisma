<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * TTD penilaian disimpan PER MATERI: unik (peserta, fasilitator, materi), bukan (peserta, fasilitator).
 * Index lama membuat fasilitator gagal menyimpan penilaian materi kedua untuk peserta yang sama (error 1062).
 */
class TtdPenilaianUnikPerMateri extends Migration
{
    private function adaIndex($nama)
    {
        return count(DB::select("SHOW INDEX FROM ttd_penilaian WHERE Key_name = ?", [$nama])) > 0;
    }

    public function up()
    {
        // Index baru dulu (diawali kegiatan_peserta_id, sekaligus dipakai foreign key), baru index lama dibuang
        if (!$this->adaIndex('uniq_ttd_peserta_fasilitator_materi')) {
            DB::statement('ALTER TABLE ttd_penilaian ADD UNIQUE KEY uniq_ttd_peserta_fasilitator_materi (kegiatan_peserta_id, fasilitator_id, materi_id)');
        }
        if ($this->adaIndex('uniq_ttd_peserta_fasilitator')) {
            DB::statement('ALTER TABLE ttd_penilaian DROP INDEX uniq_ttd_peserta_fasilitator');
        }
    }

    public function down()
    {
        // Sengaja tidak mengembalikan index lama (akan gagal bila sudah ada TTD lebih dari satu materi)
    }
}
