<?php

namespace App\Http\Controllers;

use App\Models\RoomSequence;
use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ShiftScheduleController extends Controller
{
    // =========================================================================
    // 1. TAMPILKAN FORM INPUT (LIST TANGGAL)
    // =========================================================================
    public function manage($sequence_id)
    {
        // Tarik data sequence beserta relasi ruangan dan shift custom-nya
        $sequence = RoomSequence::with(['mahasiswa', 'ruangan.roomShifts'])->findOrFail($sequence_id);
        $user = auth()->user();

        // 1. Validasi Akses
        if ($user->role !== 'admin' && $sequence->mahasiswa->user_id !== $user->id) {
            abort(403, 'Akses Ditolak.');
        }

        // 2. Cek Lock / Kunci Jadwal
        $isLocked = false;
        if ($user->role !== 'admin') {
            $start = \Carbon\Carbon::parse($sequence->start_date)->startOfDay();
            $end   = \Carbon\Carbon::parse($sequence->end_date)->startOfDay();
            $totalDaysNeeded = $start->diffInDays($end) + 1;

            $filledCount = ShiftSchedule::where('mahasiswa_id', $sequence->mahasiswa_id)
                            ->whereBetween('tanggal', [$sequence->start_date, $sequence->end_date])
                            ->whereNotNull('shift_type') 
                            ->count();

            if ($filledCount >= $totalDaysNeeded) {
                $isLocked = true;
            }
        }

        $period = CarbonPeriod::create($sequence->start_date, $sequence->end_date);
        
        $existingShifts = ShiftSchedule::where('mahasiswa_id', $sequence->mahasiswa_id)
                            ->whereBetween('tanggal', [$sequence->start_date, $sequence->end_date])
                            ->pluck('shift_type', 'tanggal')
                            ->toArray(); 

        // --- FITUR BARU: PILIHAN SHIFT DINAMIS ---
        // Ambil nama-nama shift dari database untuk ruangan ini
        $customShifts = $sequence->ruangan->roomShifts->pluck('nama_shift')->toArray();
        
        // Jika ruangan belum punya custom shift, berikan opsi default lama.
        // Opsi 'Libur' selalu ditambahkan agar mahasiswa tetap bisa libur.
        if (empty($customShifts)) {
            if ($sequence->ruangan->kategori === 'shift') {
                $availableShifts = ['Pagi', 'Siang', 'Malam', 'Libur'];
            } else {
                $availableShifts = ['Reguler', 'Jumat', 'Libur'];
            }
        } else {
            $availableShifts = array_merge($customShifts, ['Libur']);
        }

        return view('room_sequences.manage_shift', compact('sequence', 'period', 'existingShifts', 'isLocked', 'availableShifts'));
    }

    // =========================================================================
    // 2. SIMPAN DATA KE DATABASE 
    // =========================================================================
    public function store(Request $request, $sequence_id)
    {
        $sequence = RoomSequence::findOrFail($sequence_id);

        // Validasi 'in:Pagi,Siang...' dihapus, diganti 'string' agar bisa menerima nama shift custom dari database
        $request->validate([
            'shifts' => 'required|array',
            'shifts.*' => 'required|string', 
        ]);

        DB::transaction(function() use ($request, $sequence) {
            foreach ($request->shifts as $date => $shiftValue) {
                ShiftSchedule::updateOrCreate(
                    [
                        'mahasiswa_id' => $sequence->mahasiswa_id,
                        'tanggal'      => $date
                    ],
                    [
                        'room_sequence_id' => $sequence->id, 
                        'ruangan_id'       => $sequence->ruangan_id,
                        'shift_type'       => $shiftValue 
                    ]
                );
            }
        });

        return redirect()->route('room_sequences.index')
            ->with('success', 'Jadwal Shift berhasil disimpan! Silakan absen sesuai jadwal.');
    }
}