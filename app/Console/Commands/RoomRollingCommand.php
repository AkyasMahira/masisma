<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RoomSchedule;
use App\Models\RoomSequence;
use Carbon\Carbon;

class RoomRollingCommand extends Command
{
    protected $signature = 'room:sync';
    protected $description = 'Menyelaraskan jadwal aktif dengan rencana tanggal yang dibuat admin';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        $this->info("Memulai sinkronisasi jadwal untuk tanggal: $today");

        // 1. Ambil rencana aktif hari ini
        $activePlans = RoomSequence::whereDate('start_date', '<=', $today)
                                   ->whereDate('end_date', '>=', $today)
                                   ->get();

        if ($activePlans->isEmpty()) {
            $this->info("Tidak ada rencana jadwal aktif untuk hari ini.");
            return;
        }

        foreach ($activePlans as $plan) {
            // 2. Cek Jadwal Aktual
            $currentSchedule = RoomSchedule::where('mahasiswa_id', $plan->mahasiswa_id)
                                           ->whereDate('end_date', '>=', $today) // Cari yang masih aktif
                                           ->orderBy('start_date', 'desc') // Ambil yang paling baru
                                           ->first();

            // SKENARIO A: Belum ada jadwal sama sekali -> BUAT BARU
            if (!$currentSchedule) {
                RoomSchedule::create([
                    'mahasiswa_id' => $plan->mahasiswa_id,
                    'ruangan_id'   => $plan->ruangan_id,
                    'start_date'   => $plan->start_date,
                    'end_date'     => $plan->end_date,
                ]);
                $this->info("Jadwal BARU dibuat untuk Mahasiswa ID {$plan->mahasiswa_id}");
            } 
            
            // SKENARIO B: Sudah ada jadwal, TAPI ruangannya BEDA
            elseif ($currentSchedule->ruangan_id != $plan->ruangan_id) {
                
                // --- PERBAIKAN LOGIKA DI SINI ---
                
                // Cek: Apakah jadwal lama itu MULAINYA HARI INI (atau masa depan)?
                // Kalau iya, jangan bikin history baru, cukup GANTI/TIMPA saja data yang ada.
                if (Carbon::parse($currentSchedule->start_date)->gte(Carbon::today())) {
                    $currentSchedule->update([
                        'ruangan_id' => $plan->ruangan_id,
                        'end_date'   => $plan->end_date,
                        // Pastikan start date sinkron jika perlu, atau biarkan
                    ]);
                    $this->info("Koreksi Ruangan (Timpa) untuk Mahasiswa ID {$plan->mahasiswa_id} ke Ruangan {$plan->ruangan_id}");
                } 
                // Kalau jadwal lama itu MULAINYA MASA LALU (kemarin, minggu lalu, dll)
                // Maka tutup buku (history) dan buka lembaran baru.
                else {
                    // 1. Matikan jadwal lama (Set selesai kemarin)
                    $currentSchedule->update(['end_date' => Carbon::yesterday()]);

                    // 2. Buat jadwal baru
                    RoomSchedule::create([
                        'mahasiswa_id' => $plan->mahasiswa_id,
                        'ruangan_id'   => $plan->ruangan_id,
                        'start_date'   => $today, 
                        'end_date'     => $plan->end_date,
                    ]);
                    $this->info("Rolling Ruangan (History) untuk Mahasiswa ID {$plan->mahasiswa_id}");
                }
            }
        }
        $this->info("Sinkronisasi selesai.");
    }
}