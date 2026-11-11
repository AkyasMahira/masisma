<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tujuan (Kepada) surat yang diatur admin, mis. pengantar ke beberapa ruangan sekaligus
 * (data awal / sebar kuesioner di beberapa ruangan). NULL = otomatis dari ruangan pengajuan.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('surat_penelitians', function (Blueprint $t) {
            $t->json('tujuan')->nullable()->after('kode_verifikasi');
        });
    }

    public function down()
    {
        Schema::table('surat_penelitians', function (Blueprint $t) {
            $t->dropColumn('tujuan');
        });
    }
};
