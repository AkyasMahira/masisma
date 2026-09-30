<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMasterProdisTable extends Migration
{
    public function up()
    {
        Schema::create('master_prodis', function (Blueprint $table) {
            $table->id();
            $table->string('kategori')->nullable();   // rumpun, mis. KEPERAWATAN
            $table->string('nama_prodi');
            $table->string('jenjang', 20)->nullable(); // SMK/D3/D4/S1/S2/PROFESI/PPDS
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Seed dari data yang selama ini hardcode di form mahasiswa
        $data = [
            'TEKNIK & INFORMATIKA' => [
                'SMK REKAYASA PERANGKAT LUNAK','SMK TEKNIK KOMPUTER JARINGAN','SMK MULTIMEDIA',
                'S1 TEKNIK INFORMATIKA','S1 SISTEM INFORMASI','S1 ILMU KOMPUTER','D3 TEKNIK ELEKTROMEDIK',
                'D4 TEKNIK ELEKTROMEDIK','S1 TEKNIK ELEKTRO','S1 TEKNIK LINGKUNGAN',
                'S1 TEKNOLOGI LABORATORIUM MEDIS','D4 TEKNOLOGI LABORATORIUM MEDIS',
            ],
            'KEPERAWATAN' => [
                'SMK ASISTEN KEPERAWATAN','D3 KEPERAWATAN','D4 KEPERAWATAN','S1 KEPERAWATAN',
                'SMK KEPERAWATAN','PROFESI NERS','S2 KEPERAWATAN',
            ],
            'KEBIDANAN' => [
                'D3 KEBIDANAN','D4 KEBIDANAN','S1 KEBIDANAN','PROFESI BIDAN',
            ],
            'KEDOKTERAN & FARMASI' => [
                'S1 KEDOKTERAN UMUM','PROFESI DOKTER (KOAS)','S1 KEDOKTERAN GIGI','PROFESI DOKTER GIGI',
                'PPDS (SPESIALIS)','SMK FARMASI','D3 FARMASI','S1 FARMASI','PROFESI APOTEKER',
            ],
            'PENUNJANG MEDIS & KESEHATAN' => [
                'D4 REKAM MEDIK','D3 REKAM MEDIK','SMK LABORATORIUM','D3 REKAM MEDIS & INFORMASI KESEHATAN',
                'D4 REKAM MEDIS & INFORMASI KESEHATAN','S1 TEKNOLOGI LABORATORIUM MEDIK','D4 TEKNOLOGI LABORATORIUM MEDIK',
                'D3 ANALIS KESEHATAN (TLM)','D4 ANALIS KESEHATAN (TLM)','D3 RADIOLOGI','D4 RADIOLOGI',
                'D3 FISIOTERAPI','S1 FISIOTERAPI','S1 TEKNIK BIOMEDIS','PROFESI FISIOTERAPI','D3 GIZI','S1 GIZI',
                'S1 ADMINISTRASI KESEHATAN','PROFESI DIETISIEN','D3 KESEHATAN LINGKUNGAN (SANITASI)',
                'S1 KESEHATAN MASYARAKAT','D4 PROMOSI KESEHATAN','S1 KESELAMATAN DAN KESEHATAN KERJA (K3)',
                'D4 KESELAMATAN DAN KESEHATAN KERJA (K3)',
            ],
            'MANAJEMEN & SOSIAL' => [
                'SMK OTOMATISASI TATA KELOLA PERKANTORAN (OTKP)','SMK AKUNTANSI','D3 AKUNTANSI','S1 AKUNTANSI',
                'S1 MANAJEMEN','S1 HUKUM','S1 PSIKOLOGI','S1 ADMINISTRASI PUBLIK','S1 ADMINISTRASI RUMAH SAKIT',
            ],
        ];

        $now = now();
        $rows = [];
        foreach ($data as $kategori => $prodis) {
            foreach ($prodis as $nama) {
                // Jenjang = kata pertama (SMK/D3/D4/S1/S2/PROFESI/PPDS)
                $first = strtoupper(strtok($nama, ' '));
                $jenjang = in_array($first, ['SMK', 'D3', 'D4', 'S1', 'S2', 'PROFESI', 'PPDS']) ? $first : null;
                $rows[] = [
                    'kategori'   => $kategori,
                    'nama_prodi' => $nama,
                    'jenjang'    => $jenjang,
                    'aktif'      => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('master_prodis')->insert($rows);
    }

    public function down()
    {
        Schema::dropIfExists('master_prodis');
    }
}
