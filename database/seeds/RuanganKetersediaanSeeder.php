<?php

use Illuminate\Database\Seeder;
use App\Models\Ruangan;
use App\Models\RuanganKetersediaan;
use Carbon\Carbon;

class RuanganKetersediaanSeeder extends Seeder
{
    public function run()
    {
        $today = Carbon::now()->toDateString();
        $tomorrow = Carbon::now()->addDay()->toDateString();

        $ruangans = Ruangan::all();
        foreach ($ruangans as $ruangan) {
            // Snapshot untuk hari ini
            RuanganKetersediaan::updateOrCreate(
                ['ruangan_id' => $ruangan->id, 'tanggal' => $today],
                ['tersedia' => $ruangan->kuota_ruangan]
            );

            // Snapshot untuk besok (dengan sedikit pengurangan)
            $reduced = max(0, $ruangan->kuota_ruangan - rand(0, 3));
            RuanganKetersediaan::updateOrCreate(
                ['ruangan_id' => $ruangan->id, 'tanggal' => $tomorrow],
                ['tersedia' => $reduced]
            );
        }
    }
}
