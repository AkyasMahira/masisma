<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Status pra penelitian boleh 'Batal' (tombol Batalkan di daftar penelitian). */
class AddBatalToPraPenelitiansStatus extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE pra_penelitians MODIFY status ENUM('Pending','Approved','Rejected','Batal') NOT NULL DEFAULT 'Pending'");
    }

    public function down()
    {
        DB::table('pra_penelitians')->where('status', 'Batal')->update(['status' => 'Rejected']);
        DB::statement("ALTER TABLE pra_penelitians MODIFY status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending'");
    }
}
