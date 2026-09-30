<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\RoomSequence;
use App\Models\Ruangan;
use App\Models\RuanganKetersediaan;
use Carbon\Carbon;

class RoomSyncService
{
    /**
     * Sinkronisasi posisi ruangan mahasiswa & Cek Masa Magang Habis
     */
    public function syncRooms()
    {
        $today = Carbon::now()->toDateString();
        
        // 1. Ambil semua mahasiswa yang statusnya 'aktif'
        $mahasiswas = Mahasiswa::where('status', 'aktif')->get();

        foreach ($mahasiswas as $mhs) {
            
            // === [LOGIKA BARU] CEK MASA MAGANG HABIS ===
            // Jika tanggal berakhir sudah lewat (kemarin), maka nonaktifkan & keluarkan dari ruangan
            if ($mhs->tanggal_berakhir && $mhs->tanggal_berakhir < $today) {
                
                $oldRuangan = $mhs->ruangan_id;

                $mhs->update([
                    'status' => 'nonaktif', // Status jadi nonaktif
                    'ruangan_id' => null,   // Kosongkan ruangan
                    'nm_ruangan' => null
                ]);

                // Update kuota ketersediaan harian ruangan lama (opsional jika pakai tabel history)
                if ($oldRuangan) {
                    $this->recalculateQuota($oldRuangan, $today);
                }

                // Lanjut ke mahasiswa berikutnya (skip logika rolling di bawah)
                continue; 
            }
            // ===========================================

            // 2. LOGIKA JADWAL ROLLING (RoomSequence)
            $jadwalHariIni = RoomSequence::where('mahasiswa_id', $mhs->id)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->orderBy('created_at', 'desc')
                ->first();

            $oldRuanganId = $mhs->ruangan_id;

            // SKENARIO A: ADA JADWAL HARI INI
            if ($jadwalHariIni) {
                $targetRuanganId = $jadwalHariIni->ruangan_id;

                if ($oldRuanganId != $targetRuanganId) {
                    $nmRuanganBaru = Ruangan::where('id', $targetRuanganId)->value('nm_ruangan');
                    
                    $mhs->update([
                        'ruangan_id' => $targetRuanganId,
                        'nm_ruangan' => $nmRuanganBaru
                    ]);

                    $this->recalculateQuota($targetRuanganId, $today);
                    if ($oldRuanganId) $this->recalculateQuota($oldRuanganId, $today);
                }
            } 
            // SKENARIO B: TIDAK ADA JADWAL (Tapi masih nyantol di ruangan)
            else {
                // Jika dia masuk lewat jadwal rolling tapi jadwalnya habis -> Kick
                // (Note: Mahasiswa yang masuk manual tanpa jadwal rolling tidak akan kena kick disini, 
                //  mereka hanya kena kick di logika 'Masa Magang Habis' di atas)
                
                // Cek apakah dia punya riwayat rolling sebelumnya?
                $pernahRolling = RoomSequence::where('mahasiswa_id', $mhs->id)->exists();

                if ($oldRuanganId != null && $pernahRolling) {
                    $mhs->update([
                        'ruangan_id' => null,
                        'nm_ruangan' => null
                    ]);
                    $this->recalculateQuota($oldRuanganId, $today);
                }
            }
        }
    }

    /**
     * Helper: Hitung ulang kuota real-time
     */
    private function recalculateQuota($ruanganId, $date)
    {
        $ruangan = Ruangan::find($ruanganId);
        if ($ruangan) {
            // Hitung hanya yang AKTIF
            $terisi = Mahasiswa::where('ruangan_id', $ruanganId)
                        ->where('status', 'aktif')
                        ->count();
            
            $tersedia = max(0, $ruangan->kuota_ruangan - $terisi);

            RuanganKetersediaan::updateOrCreate(
                ['ruangan_id' => $ruanganId, 'tanggal' => $date],
                ['tersedia' => $tersedia]
            );
        }
    }
}