<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Format penilaian keterampilan bisa "custom": jumlah & isi pilihan skor ditentukan admin.
 * jenis_penilaian: centang | skor | custom ; skala_json: [{"nilai":0,"label":"..."}, ...]
 */
class SkalaCustomPenilaianSkill extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE kegiatan_penilaian_settings MODIFY jenis_penilaian VARCHAR(20) NOT NULL DEFAULT 'centang'");
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->text('skala_json')->nullable()->after('jenis_penilaian');
        });
    }

    public function down()
    {
        DB::table('kegiatan_penilaian_settings')->where('jenis_penilaian', 'custom')->update(['jenis_penilaian' => 'skor']);
        Schema::table('kegiatan_penilaian_settings', function (Blueprint $t) {
            $t->dropColumn('skala_json');
        });
        DB::statement("ALTER TABLE kegiatan_penilaian_settings MODIFY jenis_penilaian ENUM('centang','skor') NOT NULL DEFAULT 'centang'");
    }
}
