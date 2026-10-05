<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class WidenDispensasiKategori extends Migration
{
    /**
     * Pastikan kolom `kategori` muat nilai baru 'lupa_pulang'.
     * Di server kolom ini kemungkinan ENUM('biasa','terlambat') yang ditambah manual,
     * jadi diubah menjadi VARCHAR agar aman.
     */
    public function up()
    {
        if (!Schema::hasColumn('dispensasis', 'kategori')) {
            Schema::table('dispensasis', function (Blueprint $table) {
                $table->string('kategori', 30)->default('biasa')->after('tanggal_selesai');
            });
            return;
        }

        try {
            DB::statement("ALTER TABLE `dispensasis` MODIFY `kategori` VARCHAR(30) NOT NULL DEFAULT 'biasa'");
        } catch (\Throwable $e) {
            // Abaikan bila driver tidak mendukung MODIFY (mis. sqlite saat testing).
        }
    }

    public function down()
    {
        // Tidak perlu mengembalikan ke ENUM.
    }
}
