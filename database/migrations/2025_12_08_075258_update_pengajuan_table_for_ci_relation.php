<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePengajuanTableForCiRelation extends Migration
{
    public function up()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            // Hapus kolom CI manual yang lama
            $table->dropColumn(['ci_nama', 'ci_no_hp', 'ci_bidang']);
            
            // Tambahkan foreign key ci_id
            $table->foreignId('ci_id')
                  ->nullable()
                  ->constrained('corporate_instructors') 
                  ->after('bukti_pembayaran'); 
        });
    }

    public function down()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            // Hapus foreign key
            $table->dropForeign(['ci_id']);
            $table->dropColumn('ci_id');

            // Kembalikan kolom manual (Jika dibutuhkan, tapi biasanya tidak)
            $table->string('ci_nama')->nullable();
            $table->string('ci_no_hp')->nullable();
            $table->string('ci_bidang')->nullable();
        });
    }
}
