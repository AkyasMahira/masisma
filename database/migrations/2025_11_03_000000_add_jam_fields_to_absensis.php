<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJamFieldsToAbsensis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensis', function (Blueprint $table) {
            if (!Schema::hasColumn('absensis', 'jam_masuk')) {
                $table->timestamp('jam_masuk')->nullable()->after('mahasiswa_id');
            }

            if (!Schema::hasColumn('absensis', 'jam_keluar')) {
                $table->timestamp('jam_keluar')->nullable()->after('jam_masuk');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensis', function (Blueprint $table) {
            if (Schema::hasColumn('absensis', 'jam_keluar')) {
                $table->dropColumn('jam_keluar');
            }
            if (Schema::hasColumn('absensis', 'jam_masuk')) {
                $table->dropColumn('jam_masuk');
            }
        });
    }
}
