<?php

namespace App\Http\Controllers;

use App\Models\RoomSequence;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Services\RoomSyncService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RoomSequenceController extends Controller
{
    // =========================================================================
    // 1. INDEX (Menampilkan Data & Status Lock)
    // =========================================================================
   public function index(Request $request)
    {
        $user = auth()->user();
        $query = RoomSequence::with(['mahasiswa', 'ruangan']);
        $statusKelengkapan = null;
        $mahasiswaAktif = null; // Variabel opsional untuk dikirim ke view jika diperlukan

        // --- LOGIKA MAHASISWA ---
        if ($user->role !== 'admin') {
            // FIX MULTI-PERIODE: Ambil data mahasiswa yang PALING BARU menggunakan latest()
            $mahasiswaAktif = Mahasiswa::where('user_id', $user->id)->latest()->first();

            if (!$mahasiswaAktif) {
                // Fallback jika data mahasiswa belum ada sama sekali
                return view('room_sequences.index', [
                    'sequences' => RoomSequence::where('id', null)->paginate(10),
                    'ruangans' => [],
                    'statusKelengkapan' => ['is_locked' => false, 'percent' => 0, 'message' => 'Data mahasiswa tidak ditemukan.'],
                    'mahasiswaAktif' => null
                ]);
            }

            // Filter query hanya untuk ID mahasiswa periode terbaru
            $query->where('mahasiswa_id', $mahasiswaAktif->id);

            // Cek Persentase Kelengkapan Jadwal
            $statusKelengkapan = $this->checkScheduleCompleteness($mahasiswaAktif);

            // [BARU] KUNCI JADWAL JIKA MAGANG SUDAH SELESAI
            // Jika status mahasiswa sudah nonaktif, otomatis kunci semua tombol aksi di Frontend
            if ($mahasiswaAktif->status === 'nonaktif') {
                $statusKelengkapan['is_locked'] = true;
                $statusKelengkapan['message'] = 'Periode magang ini telah selesai. Jadwal bersifat Read-Only.';
            }
        } 
        // --- LOGIKA ADMIN ---
        else {
            if ($request->filled('search')) {
                $query->whereHas('mahasiswa', function($q) use ($request) {
                    $q->where('nm_mahasiswa', 'like', "%{$request->search}%");
                });
            }
            if ($request->filled('filter_ruangan')) {
                $query->where('ruangan_id', $request->filter_ruangan);
            }
        }

        $sequences = $query->orderBy('start_date', 'asc')->paginate(10)->withQueryString();
        $ruangans = Ruangan::orderBy('nm_ruangan', 'asc')->get();

        // Pastikan Anda mempassing variabel mahasiswaAktif jika sewaktu-waktu dibutuhkan di Blade
        return view('room_sequences.index', compact('sequences', 'ruangans', 'statusKelengkapan', 'mahasiswaAktif'));
    }

    // =========================================================================
    // 2. CREATE (Cek Lock)
    // =========================================================================
  public function create()
    {
        $user = auth()->user();
        $infoMahasiswa = null;

        // Jika Mahasiswa, Cek apakah sudah Locked?
        if ($user->role !== 'admin') {
            // FIX MULTI-PERIODE: Gunakan latest()
            $infoMahasiswa = Mahasiswa::where('user_id', $user->id)->latest()->first();
            
            if ($infoMahasiswa) {
                // Jangan izinkan create jika statusnya sudah selesai/nonaktif
                if ($infoMahasiswa->status === 'nonaktif') {
                    return redirect()->route('room_sequences.index')
                        ->with('error', 'Akses Ditolak: Periode magang ini telah selesai.');
                }

                $status = $this->checkScheduleCompleteness($infoMahasiswa);
                if ($status['is_locked']) {
                    return redirect()->route('room_sequences.index')
                        ->with('error', 'Akses Ditolak: Jadwal Anda sudah LENGKAP dan TERKUNCI.');
                }
            }
        }

        $mahasiswas = Mahasiswa::where('status', 'aktif')->get();
        $ruangans = Ruangan::orderBy('nm_ruangan')->get();

        return view('room_sequences.create', compact('mahasiswas', 'ruangans', 'infoMahasiswa'));
    }

    // =========================================================================
    // 3. STORE (SIMPAN DENGAN SEMUA VALIDASI)
    // =========================================================================
    public function store(Request $request, RoomSyncService $roomSyncService)
    {
        // 1. Validasi Input
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'ruangan_id'   => 'required|exists:ruangans,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
        ]);

        // Jika Mahasiswa, paksa ID diri sendiri
        if (auth()->user()->role !== 'admin') {
            // FIX MULTI-PERIODE: Gunakan latest()
            $mhsLogin = Mahasiswa::where('user_id', auth()->id())->latest()->firstOrFail();
            
            // Cek Lock lagi sebelum simpan
            if ($mhsLogin->status === 'nonaktif' || $this->checkScheduleCompleteness($mhsLogin)['is_locked']) {
                abort(403, 'Jadwal Anda sudah terkunci atau masa magang selesai.');
            }
            
            $request->merge(['mahasiswa_id' => $mhsLogin->id]);
        }

        $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);
        
        // ... (Lanjutan kode validasi start_date, end_date, dan overlap Anda tidak perlu diubah) ...
        $inputStart = Carbon::parse($request->start_date);
        $inputEnd   = Carbon::parse($request->end_date);

        // --- VALIDASI 1: TIDAK BOLEH DILUAR PERIODE MAGANG ---
        $globalStart = Carbon::parse($mahasiswa->tanggal_mulai);
        $globalEnd   = Carbon::parse($mahasiswa->tanggal_berakhir);

        if ($inputStart->lt($globalStart)) {
            return back()->withErrors(['start_date' => 
                'Tanggal mulai tidak boleh sebelum periode magang (' . $globalStart->format('d M Y') . ').'
            ])->withInput();
        }

        if ($inputEnd->gt($globalEnd)) {
            return back()->withErrors(['end_date' => 
                'Tanggal selesai melebihi batas akhir magang (' . $globalEnd->format('d M Y') . ').'
            ])->withInput();
        }

        // --- VALIDASI 2: TIDAK BOLEH BENTROK/TUMPUK ---
        if ($this->isOverlap($mahasiswa->id, $request->start_date, $request->end_date)) {
            return back()->withErrors(['start_date' => 
                'Tanggal BENTROK! Anda sudah memiliki jadwal di ruangan lain pada tanggal tersebut.'
            ])->withInput();
        }

        // Simpan
        RoomSequence::create($request->all());
        
        // Update status active room di tabel Mahasiswa
        $roomSyncService->syncRooms();

        return redirect()->route('room_sequences.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    // =========================================================================
    // 4. EDIT
    // =========================================================================
 public function edit($id)
    {
        $sequence = RoomSequence::findOrFail($id);

        if (auth()->user()->role !== 'admin') {
            // FIX MULTI-PERIODE: Gunakan latest()
            $mahasiswa = Mahasiswa::where('user_id', auth()->id())->latest()->first();
            
            // Cek Lock
            if ($mahasiswa->status === 'nonaktif' || $this->checkScheduleCompleteness($mahasiswa)['is_locked']) {
                return redirect()->route('room_sequences.index')
                    ->with('error', 'Jadwal Terkunci atau magang selesai. Tidak bisa diedit.');
            }
            // Cek Kepemilikan (Memastikan jadwal yg diedit milik ID mahasiswa yang TERBARU)
            if ($sequence->mahasiswa_id !== $mahasiswa->id) abort(403);
        }

        $mahasiswas = Mahasiswa::all();
        $ruangans = Ruangan::all();
        return view('room_sequences.edit', compact('sequence', 'mahasiswas', 'ruangans'));
    }

    // =========================================================================
    // 5. UPDATE
    // =========================================================================
   public function update(Request $request, $id, RoomSyncService $roomSyncService)
    {
        $sequence = RoomSequence::findOrFail($id);

        // Cek Lock Mahasiswa
        if (auth()->user()->role !== 'admin') {
            // FIX MULTI-PERIODE: Gunakan latest()
            $mahasiswa = Mahasiswa::where('user_id', auth()->id())->latest()->first();
            if ($mahasiswa->status === 'nonaktif' || $this->checkScheduleCompleteness($mahasiswa)['is_locked']) {
                abort(403, 'Jadwal Terkunci.');
            }
        }

        // ... (Lanjutan kode update Anda tidak perlu diubah) ...
        $request->validate([
            'ruangan_id'   => 'required|exists:ruangans,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
        ]);

        $mahasiswa = Mahasiswa::findOrFail($sequence->mahasiswa_id);
        $inputStart = Carbon::parse($request->start_date);
        $inputEnd   = Carbon::parse($request->end_date);
        $globalStart = Carbon::parse($mahasiswa->tanggal_mulai);
        $globalEnd   = Carbon::parse($mahasiswa->tanggal_berakhir);

        // Validasi 1: Periode Global
        if ($inputStart->lt($globalStart) || $inputEnd->gt($globalEnd)) {
            return back()->withErrors(['start_date' => 
                'Tanggal harus dalam periode: ' . $globalStart->format('d/m/Y') . ' - ' . $globalEnd->format('d/m/Y')
            ])->withInput();
        }

        // Validasi 2: Bentrok (Exclude ID Sendiri)
        if ($this->isOverlap($mahasiswa->id, $request->start_date, $request->end_date, $id)) {
            return back()->withErrors(['start_date' => 'Gagal! Tanggal bentrok dengan jadwal lain.'])->withInput();
        }

        $sequence->update($request->all());
        $roomSyncService->syncRooms();

        return redirect()->route('room_sequences.index')->with('success', 'Jadwal diperbarui.');
    }
    // =========================================================================
    // 6. DESTROY
    // =========================================================================
  public function destroy($id, RoomSyncService $roomSyncService)
    {
        $sequence = RoomSequence::findOrFail($id);

        if (auth()->user()->role !== 'admin') {
            // FIX MULTI-PERIODE: Gunakan latest()
            $mahasiswa = Mahasiswa::where('user_id', auth()->id())->latest()->first();
            
            // Cek Lock: Jika sudah penuh, tidak bisa hapus (Policy Strict)
            if ($mahasiswa->status === 'nonaktif' || $this->checkScheduleCompleteness($mahasiswa)['is_locked']) {
                return back()->with('error', 'Jadwal sudah Final/Terkunci. Hubungi admin untuk menghapus.');
            }
            if ($sequence->mahasiswa_id !== $mahasiswa->id) abort(403);
        }

        $sequence->delete();
        $roomSyncService->syncRooms();

        return back()->with('success', 'Jadwal dihapus.');
    }
    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Mengecek apakah jadwal mahasiswa sudah penuh (100% Coverage)
     * Mengembalikan status Lock dan Pesan
     */
    private function checkScheduleCompleteness($mahasiswa)
    {
        if (!$mahasiswa->tanggal_mulai || !$mahasiswa->tanggal_berakhir) {
            return ['is_locked' => false, 'message' => 'Data tanggal magang belum diset admin.', 'percent' => 0];
        }

        $start = Carbon::parse($mahasiswa->tanggal_mulai);
        $end = Carbon::parse($mahasiswa->tanggal_berakhir);
        $totalDaysNeeded = $start->diffInDays($end) + 1; // Total hari magang

        // Ambil semua jadwal ruangan mahasiswa ini
        $sequences = RoomSequence::where('mahasiswa_id', $mahasiswa->id)->get();

        $filledDates = []; 

        // Hitung hari unik yang sudah terisi
        foreach ($sequences as $seq) {
            $period = CarbonPeriod::create($seq->start_date, $seq->end_date);
            foreach ($period as $date) {
                // Hanya hitung jika tanggal berada dalam kontrak
                if ($date->between($start, $end)) {
                    $filledDates[$date->toDateString()] = true;
                }
            }
        }

        $coveredCount = count($filledDates);
        $percent = ($totalDaysNeeded > 0) ? round(($coveredCount / $totalDaysNeeded) * 100) : 0;

        // JIKA SUDAH PENUH (>= 100%) -> LOCK
        if ($coveredCount >= $totalDaysNeeded) {
            return [
                'is_locked' => true,
                'message' => 'Jadwal Anda sudah LENGKAP. Sistem mengunci perubahan data.',
                'percent' => 100
            ];
        } else {
            $missing = $totalDaysNeeded - $coveredCount;
            return [
                'is_locked' => false,
                'message' => "Masih kurang $missing hari lagi untuk memenuhi periode magang.",
                'percent' => $percent
            ];
        }
    }

    /**
     * Mengecek apakah tanggal input bertabrakan dengan jadwal lain
     */
    private function isOverlap($mhsId, $start, $end, $ignoreId = null)
    {
        return RoomSequence::where('mahasiswa_id', $mhsId)
            ->where('id', '!=', $ignoreId) // Abaikan ID yang sedang diedit
            ->where(function($query) use ($start, $end) {
                // Logic overlap:
                // (StartBaru <= EndLama) AND (EndBaru >= StartLama)
                $query->where('start_date', '<=', $end)
                      ->where('end_date', '>=', $start);
            })->exists();
    }
}