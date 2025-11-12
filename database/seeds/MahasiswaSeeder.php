<?php

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
        $mahasiswas = [
            [
                'nm_mahasiswa' => 'Febriant Sam Antonie',
                'univ_asal' => 'SMK TI Pelita Nusantara',
                'prodi' => 'Rekayasa Perangkat Lunak',
                'nm_ruangan' => 'IT',
                'ruangan_id' => 1,
                'tanggal_mulai' => '2025-07-07',
                'tanggal_berakhir' => '2025-12-12',
                'status' => 'aktif',
                'share_token' => '2956b2d3-b6c5-442b-9f3b-beb2469b6cbf',
            ],
            [
                'nm_mahasiswa' => 'Muhammad Akyas Mahira Abduh',
                'univ_asal' => 'SMK TI Pelita Nusantara',
                'prodi' => 'Rekayasa Perangkat Lunak',
                'nm_ruangan' => 'IT',
                'ruangan_id' => 1,
                'tanggal_mulai' => '2025-07-07',
                'tanggal_berakhir' => '2025-12-12',
                'status' => 'aktif',
                'share_token' => '58b33a13-7ebe-487f-8a5b-15ae504f7ebd',
            ],
            [
                'nm_mahasiswa' => 'Andhika Wibowo',
                'univ_asal' => 'SMK TI Pelita Nusantara',
                'prodi' => 'Rekayasa Perangkat Lunak',
                'nm_ruangan' => 'IT',
                'ruangan_id' => 1,
                'tanggal_mulai' => '2025-07-07',
                'tanggal_berakhir' => '2025-12-12',
                'status' => 'aktif',
                'share_token' => '5c4fa59a-c84d-40c4-bce2-eb3f98dffb4c',
            ],
        ];

        foreach ($mahasiswas as $data) {
            Mahasiswa::create($data);
        }
    }
}
