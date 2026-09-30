<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterUsersRoleToVarchar extends Migration
{
    /**
     * Kolom role awalnya enum('admin','user'); produksi sudah memakai
     * 'ruangan'/'kasir'. Ubah ke VARCHAR agar aman menambah role 'instansi'
     * (portal mitra) tanpa terganjal definisi enum.
     */
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'user'");
    }

    public function down()
    {
        // Tidak dikembalikan ke enum untuk mencegah data role di luar daftar lama hilang.
    }
}
