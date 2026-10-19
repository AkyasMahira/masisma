<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Form pendaftaran (login) & invoice kini mengambil prodi dari master prodi.
 * Prodi yang dulu hanya ada di daftar tertulis manual ditambahkan ke master agar tidak hilang.
 */
class TambahProdiDariDaftarLama extends Migration
{
    private $data = [
        ['TEKNIK & INFORMATIKA', 'S1 FISIKA', 'S1'],
        ['TEKNIK & INFORMATIKA', 'S2 FISIKA', 'S2'],
        ['MANAJEMEN & SOSIAL', 'S1 ILMU EKONOMI', 'S1'],
        ['MANAJEMEN & SOSIAL', 'S2 MAGISTER MANAJEMEN', 'S2'],
        ['KEDOKTERAN & FARMASI', 'S2 FARMASI', 'S2'],
        ['KEBIDANAN', 'S2 KEBIDANAN', 'S2'],
        ['PENUNJANG MEDIS & KESEHATAN', 'S2 MAGISTER KESEHATAN', 'S2'],
        ['PENUNJANG MEDIS & KESEHATAN', 'S2 MAGISTER KESEHATAN MASYARAKAT', 'S2'],
    ];

    public function up()
    {
        $now = now();
        foreach ($this->data as $d) {
            if (DB::table('master_prodis')->where('nama_prodi', $d[1])->exists()) continue;
            DB::table('master_prodis')->insert([
                'kategori' => $d[0], 'nama_prodi' => $d[1], 'jenjang' => $d[2], 'aktif' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        // tidak dihapus otomatis: bisa sudah dipakai data mahasiswa/booking
    }
}
