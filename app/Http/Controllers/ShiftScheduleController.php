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
    /**
     * Mengembalikan jenis akses atas sebuah sequence: 'admin' | 'owner' | 'ruangan' | false.
     * - admin: bebas
     * - owner: mahasiswa pemilik sequence
     * - ruangan: kepala ruangan yang membawahi ruangan sequence tsb
     */
    private function cekAkses(RoomSequence $sequence, $user)
    {
        if (!$user) return false;
        if ($user->role === 'admin') return 'admin';
        if ($sequence->mahasiswa && (int) $sequence->mahasiswa->user_id === (int) $user->id) return 'owner';
        if ($user->role === 'ruangan') {
            $ruangan = \App\Models\Ruangan::where('user_id', $user->id)->first();
            if ($ruangan && (int) $sequence->ruangan_id === (int) $ruangan->id) return 'ruangan';
        }
        return false;
    }

    public function manage($sequence_id)
    {
        // Tarik data sequence beserta relasi ruangan dan shift custom-nya
        $sequence = RoomSequence::with(['mahasiswa', 'ruangan.roomShifts'])->findOrFail($sequence_id);
        $user = auth()->user();

        // 1. Validasi Akses (admin / pemilik / kepala ruangan)
        $akses = $this->cekAkses($sequence, $user);
        if (!$akses) {
            abort(403, 'Akses Ditolak.');
        }
        $isKaru = ($akses === 'ruangan');

        // 2. Cek Lock / Kunci Jadwal (hanya untuk mahasiswa; admin & kepala ruangan bebas edit)
        $isLocked = false;
        if ($user->role !== 'admin' && !$isKaru) {
            $start = \Carbon\Carbon::parse($sequence->start_date)->startOfDay();
            $end   = \Carbon\Carbon::parse($sequence->end_date)->startOfDay();
            $totalDaysNeeded = (int) $start->diffInDays($end, true) + 1;

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

        $backUrl    = $isKaru ? route('kepala_ruangan.dashboard') : route('room_sequences.index');
        $canRentang = ($user->role === 'admin' || $isKaru); // hanya admin/karu boleh ubah rentang

        // Semua rotasi mahasiswa ini (untuk "jadwal sebelumnya" + deteksi bentrok rentang)
        $curStart = \Carbon\Carbon::parse($sequence->start_date);
        $curEnd   = \Carbon\Carbon::parse($sequence->end_date);
        $semuaSequences = RoomSequence::with('ruangan')
            ->where('mahasiswa_id', $sequence->mahasiswa_id)
            ->orderBy('start_date')
            ->get()
            ->map(function ($s) use ($sequence, $curStart, $curEnd) {
                $s->is_current = ((int) $s->id === (int) $sequence->id);
                // Bentrok bila rentang beririsan dengan sequence yang sedang dibuka (dan bukan dirinya)
                $s->is_bentrok = !$s->is_current
                    && \Carbon\Carbon::parse($s->start_date)->lte($curEnd)
                    && \Carbon\Carbon::parse($s->end_date)->gte($curStart);
                return $s;
            });

        return view('room_sequences.manage_shift', compact('sequence', 'period', 'existingShifts', 'isLocked', 'availableShifts', 'backUrl', 'canRentang', 'semuaSequences'));
    }

    // =========================================================================
    // 2. SIMPAN DATA KE DATABASE 
    // =========================================================================
    public function store(Request $request, $sequence_id)
    {
        $sequence = RoomSequence::with('mahasiswa')->findOrFail($sequence_id);
        $akses = $this->cekAkses($sequence, auth()->user());
        if (!$akses) abort(403, 'Akses Ditolak.');

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

        if ($akses === 'ruangan') {
            return redirect()->route('kepala_ruangan.dashboard')
                ->with('success', 'Jadwal shift mahasiswa berhasil diperbarui.');
        }

        return redirect()->route('room_sequences.index')
            ->with('success', 'Jadwal Shift berhasil disimpan! Silakan absen sesuai jadwal.');
    }

    // =========================================================================
    // 3. UBAH RENTANG (PERIODE) SEQUENCE — admin & kepala ruangan
    // =========================================================================
    public function updateRentang(Request $request, $sequence_id)
    {
        $sequence = RoomSequence::with('mahasiswa')->findOrFail($sequence_id);
        $akses = $this->cekAkses($sequence, auth()->user());
        if (!$akses || $akses === 'owner') {
            abort(403, 'Hanya admin / kepala ruangan yang dapat mengubah rentang.');
        }

        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $lama = $sequence->replicate();
        $sequence->update([
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
        ]);
        ShiftSchedule::bersihkanSetelahUbahPeriode($lama, $sequence);

        return redirect()->route('shift_schedule.manage', $sequence->id)
            ->with('success', 'Rentang tanggal berhasil diperbarui. Silakan atur shift untuk tanggal baru bila ada.');
    }
}