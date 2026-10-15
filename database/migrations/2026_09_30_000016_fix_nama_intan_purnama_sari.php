<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixNamaIntanPurnamaSari extends Migration
{
    public function up()
    {
        // Perbaiki "INTAN PURNAMASARI" (dan varian spasi/kapital) -> "INTAN PURNAMA SARI"
        // di SEMUA kegiatan (IHT). Idempotent: yang sudah benar tidak berubah maknanya.
        try {
            DB::table('kegiatan_peserta')
                ->whereRaw("REPLACE(UPPER(nama_lengkap_gelar), ' ', '') = ?", ['INTANPURNAMASARI'])
                ->update(['nama_lengkap_gelar' => 'INTAN PURNAMA SARI']);
        } catch (\Throwable $e) {
            // abaikan bila tabel/kolom tidak ada di environment tertentu
        }
    }

    public function down()
    {
        // Tidak dikembalikan (perbaikan data).
    }
}
