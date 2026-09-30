<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRuanganIdToPengajuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('pengajuan', function (Blueprint $table) {
        // Menambahkan kolom ruangan_id, boleh kosong (nullable) jaga-jaga
        // Asumsi tabel referensinya bernama 'ruangans'
        $table->foreignId('ruangan_id')->nullable()->after('ci_id')->constrained('ruangans')->onDelete('set null');

        // JIKA tabel kamu tidak pakai foreign key constraint, pakai ini saja:
        // $table->unsignedBigInteger('ruangan_id')->nullable()->after('ci_id');
    });
}

public function down()
{
    Schema::table('pengajuan', function (Blueprint $table) {
        $table->dropForeign(['ruangan_id']); // Hapus baris ini jika tidak pakai constrained di atas
        $table->dropColumn('ruangan_id');
    });
}
}
