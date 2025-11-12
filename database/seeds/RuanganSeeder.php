<?php

use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class RuanganSeeder extends Seeder
{
    public function run()
    {
        $ruangans = [
            ['nm_ruangan' => 'IT', 'kuota_ruangan' => 3],
            ['nm_ruangan' => 'Gelatik', 'kuota_ruangan' => 30],
            ['nm_ruangan' => 'Perkutut', 'kuota_ruangan' => 25],
            ['nm_ruangan' => 'Punai', 'kuota_ruangan' => 10],
            ['nm_ruangan' => 'Kasuari', 'kuota_ruangan' => 30],
            ['nm_ruangan' => 'Merak', 'kuota_ruangan' => 25],
            ['nm_ruangan' => 'Poli', 'kuota_ruangan' => 26],
            ['nm_ruangan' => 'IGD', 'kuota_ruangan' => 27],
            ['nm_ruangan' => 'ICU', 'kuota_ruangan' => 28],
            ['nm_ruangan' => 'ICVCU', 'kuota_ruangan' => 29],
        ];

        foreach ($ruangans as $data) {
            Ruangan::create($data);
        }
    }
}
