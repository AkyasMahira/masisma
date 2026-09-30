<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoomSyncService; // <--- IMPOR SERVICE BARU
// Hapus use App\Models\... dan use Carbon\Carbon; karena sudah ada di Service

class RoomRollingCommand extends Command
{
    protected $signature = 'room:sync';
    protected $description = 'Menyelaraskan jadwal aktif dengan rencana tanggal yang dibuat admin';

    // Inject Service Class ke dalam method handle()
    public function handle(RoomSyncService $roomSyncService) 
    {
        $this->info("Memulai sinkronisasi jadwal...");
        
        // Panggil method sinkronisasi dari Service
        $message = $roomSyncService->syncRooms();
        
        $this->info($message); // Tampilkan pesan yang dikembalikan Service
        $this->info("Sinkronisasi selesai.");
    }
}