<?php

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run()
    {
        $absensis = [
            [
                'mahasiswa_id' => 1,
                'jam_masuk' => Carbon::parse('2025-11-01 18:11:15'),
                'jam_keluar' => Carbon::parse('2025-11-01 18:11:37'),
            ],
            [
                'mahasiswa_id' => 8,
                'jam_masuk' => Carbon::parse('2025-11-03 10:28:43'),
                'jam_keluar' => null,
            ],
        ];

        foreach ($absensis as $data) {
            Absensi::create($data);
        }
    }
}
