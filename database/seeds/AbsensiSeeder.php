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
                'type' => 'masuk',
                'jam_masuk' => Carbon::parse('2025-11-01 08:00:00'),
                'jam_keluar' => Carbon::parse('2025-11-01 17:00:00'),
                'durasi_menit' => 540,
            ],
            [
                'mahasiswa_id' => 1,
                'type' => 'masuk',
                'jam_masuk' => Carbon::parse('2025-11-02 08:15:00'),
                'jam_keluar' => Carbon::parse('2025-11-02 16:45:00'),
                'durasi_menit' => 510,
            ],
            [
                'mahasiswa_id' => 2,
                'type' => 'masuk',
                'jam_masuk' => Carbon::parse('2025-11-01 09:00:00'),
                'jam_keluar' => Carbon::parse('2025-11-01 17:30:00'),
                'durasi_menit' => 510,
            ],
            [
                'mahasiswa_id' => 2,
                'type' => 'masuk',
                'jam_masuk' => Carbon::parse('2025-11-03 08:30:00'),
                'jam_keluar' => null,
                'durasi_menit' => null,
            ],
            [
                'mahasiswa_id' => 3,
                'type' => 'masuk',
                'jam_masuk' => Carbon::parse('2025-11-02 10:00:00'),
                'jam_keluar' => Carbon::parse('2025-11-02 18:00:00'),
                'durasi_menit' => 480,
            ],
        ];

        foreach ($absensis as $data) {
            Absensi::create($data);
        }
    }
}
