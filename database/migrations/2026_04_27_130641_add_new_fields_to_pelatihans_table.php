<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToPelatihansTable extends Migration
{
    public function up()
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('nama');
            $table->enum('lms_status', ['Ada', 'Tidak'])->default('Tidak')->after('nirp');
            $table->string('lms_email')->nullable()->after('lms_status');
        });
    }

    public function down()
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropColumn(['nik', 'lms_status', 'lms_email']);
        });
    }
}