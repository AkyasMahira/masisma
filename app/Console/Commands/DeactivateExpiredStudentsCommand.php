<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Carbon\Carbon;

class DeactivateExpiredStudentsCommand extends Command
{
    protected $signature = 'mahasiswa:deactivate-expired';
    protected $description = 'Deactivate expired students and update room quotas';

    public function handle()
    {
        $this->info('Starting to process expired students...');

        // Get all students whose end date has passed
        $expiredStudents = Mahasiswa::where('tanggal_berakhir', '<', Carbon::now()->startOfDay())
            ->where('status', '!=', 'inactive')
            ->get();

        foreach ($expiredStudents as $student) {
            // Store ruangan_id before we remove it
            $ruanganId = $student->ruangan_id;

            // Update student status and remove from room
            $student->update([
                'status' => 'inactive',
                'ruangan_id' => null,
                'nm_ruangan' => null
            ]);

            $this->info("Deactivated student: {$student->nm_mahasiswa}");

            // Update room quota if the student was assigned to a room
            if ($ruanganId) {
                $ruangan = Ruangan::find($ruanganId);
                if ($ruangan) {
                    // Update the room's quota snapshot for today
                    $ruangan->syncAllKuota();
                    $this->info("Updated quota for room: {$ruangan->nm_ruangan}");
                }
            }
        }

        $this->info('Finished processing expired students.');
    }
}
