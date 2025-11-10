<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class UpdateMahasiswaStatus extends Command
{
    protected $signature = 'mahasiswa:update-status';
    protected $description = 'Update mahasiswa status based on masa aktif';

    public function handle()
    {
        $today = Carbon::now()->toDateString();

        // Update status for mahasiswa whose tanggal_berakhir has passed
        Mahasiswa::where('status', 'aktif')
            ->whereNotNull('tanggal_berakhir')
            ->where('tanggal_berakhir', '<', $today)
            ->update(['status' => 'nonaktif']);

        $this->info('Successfully updated mahasiswa status.');
    }
}
