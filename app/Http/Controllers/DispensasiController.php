<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispensasi;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\Absensi; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class DispensasiController extends Controller
{
    // ==========================================================
    // BAGIAN MAHASISWA
    // ==========================================================

    public function index()
    {
        $user = Auth::user();
        
        // FIX: Selalu ambil data magang yang sedang AKTIF dan PALING BARU
        $mahasiswa = Mahasiswa::where('user_id', $user->id)
                        ->where('status', 'aktif')
                        ->latest()
                        ->firstOrFail();

        $riwayat = Dispensasi::where('mahasiswa_id', $mahasiswa->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);

        return view('mahasiswa.dispensasi.index', compact('riwayat'));
    }

    public function create()
    {
        $user = Auth::user();

        // Ambil data magang yang sedang AKTIF dan PALING BARU milik user ini
        $mahasiswa = Mahasiswa::with('mou')
                        ->where('user_id', $user->id)
                        ->where('status', 'aktif')
                        ->latest()
                        ->first();

        // Form ini khusus akun mahasiswa magang. Jangan lempar 404 buta.
        if (!$mahasiswa) {
            if (($user->role ?? null) === 'admin') {
                return redirect()->route('admin.dispensasi.index')
                    ->with('info', 'Form pengajuan dispensasi hanya untuk akun mahasiswa magang. Gunakan menu Dispensasi admin untuk mengelola pengajuan.');
            }
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Anda belum memiliki data magang berstatus AKTIF, sehingga belum dapat mengajukan dispensasi. Hubungi admin bila ini keliru.');
        }

        $ruangans = Ruangan::orderBy('nm_ruangan', 'asc')->get();

        return view('mahasiswa.dispensasi.create', compact('mahasiswa', 'ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:biasa,terlambat,lupa_pulang',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'file_surat' => 'required_if:kategori,biasa|nullable|mimes:pdf|max:1024',
            'ruangan_id' => 'required_unless:kategori,biasa',
            'nama_penyetuju' => 'required_unless:kategori,biasa|nullable|string',
            'jabatan_penyetuju' => 'required_unless:kategori,biasa|nullable|string',
            'ttd_penyetuju' => 'required_unless:kategori,biasa|nullable|string',
            'keterangan' => 'required|string',
        ]);

        // Dispensasi Lupa Pulang: tanggal wajib & maksimal 2 hari ke belakang (tidak boleh masa depan)
        if ($request->kategori === 'lupa_pulang') {
            $request->validate([
                'tanggal_mulai' => 'required|date|after_or_equal:' . Carbon::now()->subDays(2)->toDateString()
                                   . '|before_or_equal:' . Carbon::now()->toDateString(),
            ], [], ['tanggal_mulai' => 'Tanggal Lupa Pulang']);
        }

        $user = Auth::user();
        
        // FIX: Selalu ambil data magang yang sedang AKTIF dan PALING BARU
        $mahasiswa = Mahasiswa::with('mou')
                        ->where('user_id', $user->id)
                        ->where('status', 'aktif')
                        ->latest()
                        ->firstOrFail();

        // Lupa Pulang WAJIB ada absen masuk di tanggal tsb (lupa pulang = masuk tanpa checkout).
        if ($request->kategori === 'lupa_pulang') {
            $adaMasuk = Absensi::where('mahasiswa_id', $mahasiswa->id)
                ->where('type', 'masuk')
                ->whereDate('created_at', $request->tanggal_mulai)
                ->exists();
            if (!$adaMasuk) {
                return back()->withInput()->withErrors([
                    'tanggal_mulai' => 'Tidak ada absen masuk pada tanggal tersebut. Dispensasi Lupa Pulang hanya untuk hari yang sudah absen masuk tetapi lupa tap pulang. Jika memang tidak hadir, gunakan Izin Biasa/Sakit.',
                ]);
            }
        }

        $path = null;
        $keteranganTerlambatJson = null;

        if ($request->kategori === 'biasa') {
            $tanggalMulai = $request->tanggal_mulai;
            $tanggalSelesai = $request->tanggal_selesai;

            if ($request->hasFile('file_surat')) {
                $path = $request->file('file_surat')->store('dispensasi', 'public');
            }
        } else {
            // terlambat & lupa_pulang: butuh ruangan + otorisasi penyetuju (Karu/CI)
            if ($request->kategori === 'lupa_pulang') {
                $tanggalMulai = $request->tanggal_mulai;              // tanggal lupa checkout (maks 2 hari lalu)
            } else {
                $tanggalMulai = $request->tanggal_mulai ?? Carbon::now()->toDateString(); // terlambat = hari ini
            }
            $tanggalSelesai = $tanggalMulai;

            $ruangan = Ruangan::find($request->ruangan_id);

            $dataTerlambat = [
                'ruangan' => $ruangan ? $ruangan->nm_ruangan : '-',
                'nama_penyetuju' => $request->nama_penyetuju,
                'jabatan_penyetuju' => $request->jabatan_penyetuju,
                'ttd_penyetuju' => $request->ttd_penyetuju,
            ];
            $keteranganTerlambatJson = json_encode($dataTerlambat);

            if ($request->kategori === 'lupa_pulang') {
                $jenisSurat = 'SURAT DISPENSASI LUPA ABSEN PULANG';
                $aksiText   = 'Mengajukan dispensasi tidak melakukan absen pulang (lupa checkout)';
                $prefix     = 'LupaPulang_';
            } else {
                $jenisSurat = 'SURAT DISPENSASI KETERLAMBATAN ABSENSI';
                $aksiText   = 'Mengajukan dispensasi keterlambatan absensi';
                $prefix     = 'Terlambat_';
            }

            $pdf = Pdf::loadView('mahasiswa.dispensasi.pdf_terlambat', [
                'mahasiswa' => $mahasiswa,
                'dataTerlambat' => $dataTerlambat,
                'keterangan' => $request->keterangan,
                'tanggal' => Carbon::parse($tanggalMulai)->isoFormat('D MMMM YYYY'),
                'jenisSurat' => $jenisSurat,
                'aksiText' => $aksiText,
            ]);

            $fileName = 'dispensasi/' . $prefix . Str::slug($mahasiswa->nm_mahasiswa) . '_' . time() . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());
            $path = $fileName;
        }

        Dispensasi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'kategori' => $request->kategori,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'keterangan' => $request->keterangan,
            'file_path' => $path,
            'keterangan_terlambat' => $keteranganTerlambatJson,
            'status' => 'pending'
        ]);

        // Buat absen masuk bayangan jika terlambat hari ini agar ID Card terbuka
        if ($request->kategori === 'terlambat' && $tanggalMulai === Carbon::now()->toDateString()) {
            $sudahMasuk = Absensi::where('mahasiswa_id', $mahasiswa->id)
                ->whereDate('created_at', $tanggalMulai)
                ->where('type', 'masuk')
                ->exists();

            // Hanya buat absen bayangan bila SEKARANG memang di dalam jam shift
            // (mis. shift malam: pengajuan pagi hari tidak boleh membuat "sedang kerja").
            if (!$sudahMasuk && $this->sedangDalamJamShift($mahasiswa, Carbon::now())) {
                Absensi::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'jam_masuk' => now(), 
                    'type' => 'masuk',
                    'keterangan' => 'Dispen Terlambat (Pending)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('mahasiswa.dispensasi.index')->with('success', 'Pengajuan berhasil dikirim!');
    }

    /**
     * Jam mulai/selesai shift mahasiswa pada $date: dari jadwal shift (ShiftSchedule)
     * + jam shift ruangan (getShiftTimes, termasuk custom RoomShift). Kata kunci
     * "malam/siang/pagi" di keterangan hanya dipakai bila jadwal kosong.
     * @return array ['start' => Carbon, 'end' => Carbon, 'shift' => string]
     */
    private function jadwalShiftTanggal($mahasiswaId, Carbon $date, $keterangan = '')
    {
        $date = $date->copy()->startOfDay();
        $tanggal = $date->toDateString();
        $seq = \App\Models\RoomSequence::where('mahasiswa_id', $mahasiswaId)
            ->where('start_date', '<=', $tanggal)->where('end_date', '>=', $tanggal)
            ->with('ruangan')->first();
        $roomId = $seq && $seq->ruangan ? $seq->ruangan->id : 0;

        $jadwal = \App\Models\ShiftSchedule::where('mahasiswa_id', $mahasiswaId)->where('tanggal', $tanggal)->first();
        $ket = (string) $keterangan;
        if ($jadwal && strtolower($jadwal->shift_type) !== 'libur') {
            $shift = $jadwal->shift_type;
        } elseif (stripos($ket, 'malam') !== false) {
            $shift = 'Malam';
        } elseif (stripos($ket, 'siang') !== false) {
            $shift = 'Siang';
        } elseif (stripos($ket, 'pagi') !== false) {
            $shift = 'Pagi';
        } else {
            $shift = $date->dayOfWeekIso == 5 ? 'Jumat' : 'Reguler';
        }

        $t = app(AbsensiController::class)->getShiftTimes($shift, $date, $roomId);
        $start = !empty($t['start']) ? $t['start']->copy() : null;
        $end   = !empty($t['end']) ? $t['end']->copy() : null;

        if (!$start || !$end) {
            // cadangan bila jam shift tidak terdefinisi
            $map = [
                'Malam' => [[21, 0], [7, 0], true],
                'Siang' => [[14, 0], [21, 0], false],
                'Pagi'  => [[7, 0], [14, 0], false],
                'Jumat' => [[7, 0], [14, 30], false],
            ];
            $m = $map[$shift] ?? [[7, 15], [15, 30], false];
            $start = $date->copy()->setTime($m[0][0], $m[0][1]);
            $end   = $date->copy()->setTime($m[1][0], $m[1][1]);
            if ($m[2]) $end->addDay();
        }
        if ($end->lte($start)) $end->addDay();

        return ['start' => $start, 'end' => $end, 'shift' => $shift];
    }

    /**
     * Apakah $now berada di jendela shift (mulai -30 menit s/d selesai) untuk
     * jadwal hari ini, atau shift kemarin yang lintas hari (shift malam).
     */
    private function sedangDalamJamShift(Mahasiswa $mahasiswa, Carbon $now)
    {
        $absensi = app(AbsensiController::class);
        foreach ([$now->copy()->subDay(), $now->copy()] as $tgl) {
            $tanggal = $tgl->toDateString();
            $seq = \App\Models\RoomSequence::where('mahasiswa_id', $mahasiswa->id)
                ->where('start_date', '<=', $tanggal)->where('end_date', '>=', $tanggal)
                ->with('ruangan')->first();
            if (!$seq || !$seq->ruangan) continue;

            if ($seq->ruangan->kategori === 'shift') {
                $jadwal = \App\Models\ShiftSchedule::where('mahasiswa_id', $mahasiswa->id)->where('tanggal', $tanggal)->first();
                if (!$jadwal || strtolower($jadwal->shift_type) === 'libur') continue;
                $shift = $jadwal->shift_type;
            } else {
                $day = $tgl->dayOfWeekIso;
                if ($day > 5) continue;
                $shift = $day == 5 ? 'Jumat' : 'Reguler';
            }

            $t = $absensi->getShiftTimes($shift, $tgl, $seq->ruangan->id);
            if (empty($t['start']) || empty($t['end'])) continue;
            if ($now->gte($t['start']->copy()->subMinutes(30)) && $now->lte($t['end'])) return true;
        }
        return false;
    }

    public function downloadTemplate()
    {
        $path = storage_path('app/public/templates/template_dispensasi.docx');
        if (file_exists($path)) {
            return response()->download($path);
        }
        return back()->with('error', 'File template belum tersedia di server.');
    }

    // ==========================================================
    // BAGIAN ADMIN
    // ==========================================================
    
    public function adminIndex(Request $request)
    {
        $query = Dispensasi::with(['mahasiswa.mou']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function($q) use ($search) {
                $q->where('nm_mahasiswa', 'like', "%$search%");
            });
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $dispensasis = $query->orderBy('created_at', 'desc')->paginate(10);

        $rankings = Dispensasi::join('mahasiswas', 'dispensasis.mahasiswa_id', '=', 'mahasiswas.id')
            ->join('mous', 'mahasiswas.mou_id', '=', 'mous.id')
            ->select('mous.nama_universitas', \DB::raw('count(*) as total'))
            ->groupBy('mous.nama_universitas')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        return view('admin.dispensasi.index', compact('dispensasis', 'rankings'));
    }

    public function adminEdit($id)
    {
        $dispensasi = Dispensasi::with('mahasiswa')->findOrFail($id);
        return view('admin.dispensasi.edit', compact('dispensasi'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $dispensasi = Dispensasi::findOrFail($id);

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required|in:biasa,terlambat,lupa_pulang',
            'status' => 'required|in:pending,approved,rejected',
            'catatan_admin' => 'nullable|string'
        ]);

        $oldStatus = $dispensasi->status;

        $dispensasi->update([
            'kategori' => $request->kategori,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        if ($oldStatus !== 'approved' && $request->status === 'approved') {
            $this->generateAbsensiBackdate($dispensasi);
        }

        return redirect()->route('admin.dispensasi.index')->with('success', 'Data dispensasi berhasil diperbarui.');
    }

    public function adminDestroy($id)
    {
        $dispensasi = Dispensasi::findOrFail($id);
        
        if ($dispensasi->file_path && Storage::disk('public')->exists($dispensasi->file_path)) {
            Storage::disk('public')->delete($dispensasi->file_path);
        }

        $dispensasi->delete();
        return redirect()->route('admin.dispensasi.index')->with('success', 'Data dispensasi berhasil dihapus.');
    }

    public function approve($id)
    {
        $dispensasi = Dispensasi::findOrFail($id);
        abort_unless($this->bolehKelola($dispensasi), 403, 'Anda tidak berhak menyetujui dispensasi ini.');

        $dispensasi->update([
            'status' => 'approved',
            'catatan_admin' => 'Disetujui. Absensi otomatis terisi.',
        ]);

        $this->generateAbsensiBackdate($dispensasi);

        return redirect()->back()->with('success', 'Pengajuan disetujui & Absensi berhasil digenerate otomatis.');
    }

    public function reject(Request $request, $id)
    {
        $dispensasi = Dispensasi::findOrFail($id);
        abort_unless($this->bolehKelola($dispensasi), 403, 'Anda tidak berhak menolak dispensasi ini.');

        $request->validate(['catatan_admin' => 'required|string']);

        $dispensasi->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->back()->with('success', 'Pengajuan ditolak.');
    }

    /**
     * Boleh mengelola (ACC/tolak) dispensasi bila:
     *  - user admin, ATAU
     *  - user kepala ruangan (role 'ruangan') yang membawahi mahasiswa
     *    pemilik dispensasi (via ruangan_id, roomSequences, atau shiftSchedules).
     */
    private function bolehKelola(Dispensasi $dispensasi)
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->role === 'admin') return true;

        if ($user->role === 'ruangan') {
            $ruangan = \App\Models\Ruangan::where('user_id', $user->id)->first();
            $mhs = $dispensasi->mahasiswa;
            if (!$ruangan || !$mhs) return false;

            // Hanya bila tanggal dispensasi jatuh di periode mahasiswa berada di ruangan Karu ini
            // (mahasiswa rolling: Karu ruangan lama tidak bisa ACC dispensasi di ruangan baru)
            return Dispensasi::where('id', $dispensasi->id)->untukRuangan($ruangan->id)->exists();
        }

        return false;
    }

    // ==========================================================
    // HELPER: BACKDATE GENERATOR (FORCED - TANPA LIHAT JADWAL DB)
    // ==========================================================
    private function generateAbsensiBackdate($dispensasi)
    {
        $periode = CarbonPeriod::create($dispensasi->tanggal_mulai, $dispensasi->tanggal_selesai);

        foreach ($periode as $date) {
            if ($date->gt(Carbon::now()->endOfDay())) {
                continue; 
            }

            // ==========================================================
            // LOGIKA 1: DISPENSASI BIASA (IZIN / SAKIT FULL DAY)
            // ==========================================================
            if ($dispensasi->kategori === 'biasa') {
                $sudahAbsen = Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)
                                ->whereDate('created_at', $date)
                                ->exists();

                if (!$sudahAbsen) {
                    // FIX: Buat record 'masuk' agar terdeteksi dashboard
                    $absenMasuk = new Absensi();
                    $absenMasuk->mahasiswa_id = $dispensasi->mahasiswa_id;
                    $absenMasuk->jam_masuk = $date->copy()->setTime(7, 0, 0);
                    $absenMasuk->jam_keluar = $date->copy()->setTime(16, 0, 0);
                    $absenMasuk->type = 'masuk'; 
                    $absenMasuk->durasi_menit = 540; // 9 Jam
                    $absenMasuk->keterangan = "Dispensasi Disetujui: Izin Biasa";
                    $absenMasuk->created_at = $date->copy()->setTime(7, 0, 0); 
                    $absenMasuk->updated_at = $date->copy()->setTime(7, 0, 0);
                    $absenMasuk->save();

                    // FIX: Buat record 'keluar' pasangannya
                    $absenKeluar = new Absensi();
                    $absenKeluar->mahasiswa_id = $dispensasi->mahasiswa_id;
                    $absenKeluar->jam_masuk = $date->copy()->setTime(7, 0, 0);
                    $absenKeluar->jam_keluar = $date->copy()->setTime(16, 0, 0);
                    $absenKeluar->type = 'keluar'; 
                    $absenKeluar->durasi_menit = 540; 
                    $absenKeluar->keterangan = "Dispensasi Disetujui: Izin Biasa";
                    $absenKeluar->created_at = $date->copy()->setTime(16, 0, 0); 
                    $absenKeluar->updated_at = $date->copy()->setTime(16, 0, 0);
                    $absenKeluar->save();
                }
            } 
            // ==========================================================
            // LOGIKA 2: DISPENSASI TERLAMBAT (MEMAKSA ABSEN OTOMATIS)
            // ==========================================================
            else if ($dispensasi->kategori === 'terlambat') {
                
                // Jam masuk/pulang mengikuti JADWAL SHIFT tanggal tsb + jam shift ruangannya
                $js = $this->jadwalShiftTanggal($dispensasi->mahasiswa_id, $date, $dispensasi->keterangan);
                $jamMasuk  = $js['start'];
                $jamKeluar = $js['end'];
                $tipeShift = $js['shift'];

                $absenMasuk = Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)
                    ->where('type', 'masuk')
                    ->whereDate('created_at', $date)
                    ->latest()
                    ->first();

                // EKSEKUSI: BUAT / UPDATE ABSEN MASUK
                if ($absenMasuk) {
                    if (strpos((string) $absenMasuk->keterangan, 'Pending') !== false) {
                        $jamLama = $absenMasuk->jam_masuk;
                        $absenMasuk->jam_masuk = $jamMasuk;
                        $absenMasuk->keterangan = "Dispen Disetujui (Shift $tipeShift)";
                        $absenMasuk->save();
                        // pasangan checkout (bila sudah ada) ikut disesuaikan
                        Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)->where('type', 'keluar')
                            ->where('jam_masuk', $jamLama)->update(['jam_masuk' => $jamMasuk]);
                    }
                } else {
                    $masukBaru = new Absensi();
                    $masukBaru->mahasiswa_id = $dispensasi->mahasiswa_id;
                    $masukBaru->jam_masuk = $jamMasuk;
                    $masukBaru->type = 'masuk';
                    $masukBaru->keterangan = "Dispen Disetujui (Shift $tipeShift)";
                    $masukBaru->created_at = $jamMasuk;
                    $masukBaru->updated_at = $jamMasuk;
                    $masukBaru->save();
                    $absenMasuk = $masukBaru;
                }

                // EKSEKUSI: BUAT ABSEN KELUAR (JIKA JAM SHIFT SUDAH SELESAI)
                // Jika belum selesai, biarkan mahasiswa absen pulang manual via QR
                $waktuMasuk = $absenMasuk ? Carbon::parse($absenMasuk->jam_masuk) : $jamMasuk;
                $absenKeluar = Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)
                    ->where('type', 'keluar')
                    ->where('jam_masuk', $waktuMasuk)
                    ->exists();
                if ($jamKeluar->lte($waktuMasuk)) $jamKeluar = $waktuMasuk->copy()->addHours(7);

                if (!$absenKeluar && \Carbon\Carbon::now()->gt($jamKeluar)) {
                    
                    $keluarBaru = new Absensi();
                    $keluarBaru->mahasiswa_id = $dispensasi->mahasiswa_id;
                    $keluarBaru->jam_masuk = $waktuMasuk;
                    $keluarBaru->jam_keluar = $jamKeluar;
                    $keluarBaru->type = 'keluar'; 
                    $keluarBaru->durasi_menit = $waktuMasuk->diffInMinutes($jamKeluar);
                    $keluarBaru->keterangan = "Dispen Pulang Disetujui (Shift $tipeShift)";
                    $keluarBaru->created_at = $jamKeluar;
                    $keluarBaru->updated_at = $jamKeluar;
                    $keluarBaru->save();
                }
            }
            // ==========================================================
            // LOGIKA 3: DISPENSASI LUPA PULANG
            // Lengkapi CHECKOUT yang hilang untuk hari yang lupa pulang (tanggal dispensasi).
            // Jam pulang = jam selesai shift hari itu; dicatat di TANGGAL tsb (BUKAN tanggal ACC).
            // ==========================================================
            else if ($dispensasi->kategori === 'lupa_pulang') {
                // Syarat: harus sudah ada absen MASUK di hari itu (lupa pulang = masuk tanpa checkout).
                $masuk = Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)
                    ->where('type', 'masuk')
                    ->whereDate('created_at', $date)
                    ->orderBy('jam_masuk')
                    ->first();
                if (!$masuk) continue; // tidak ada masuk -> tidak bisa dibuatkan checkout

                $sudahKeluar = Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)
                    ->where('type', 'keluar')
                    ->where('jam_masuk', $masuk->jam_masuk)
                    ->exists();
                if ($sudahKeluar) continue;

                $jamMasuk = Carbon::parse($masuk->jam_masuk);

                // Jam selesai dari jadwal shift hari itu + jam shift ruangannya
                $jamKeluar = $this->jadwalShiftTanggal($dispensasi->mahasiswa_id, $date, $dispensasi->keterangan)['end'];
                if ($jamKeluar->lte($jamMasuk)) $jamKeluar = $jamMasuk->copy()->addHours(7);

                $keluarBaru = new Absensi();
                $keluarBaru->mahasiswa_id = $dispensasi->mahasiswa_id;
                $keluarBaru->jam_masuk = $jamMasuk;
                $keluarBaru->jam_keluar = $jamKeluar;
                $keluarBaru->type = 'keluar';
                $keluarBaru->durasi_menit = $jamMasuk->diffInMinutes($jamKeluar);
                $keluarBaru->keterangan = 'Dispen Lupa Pulang Disetujui';
                $keluarBaru->created_at = $jamKeluar;   // TANGGAL yang benar (hari lupa pulang), bukan tanggal ACC
                $keluarBaru->updated_at = $jamKeluar;
                $keluarBaru->save();
            }
        }
    }
}