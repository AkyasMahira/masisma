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
        
        // FIX: Selalu ambil data magang yang sedang AKTIF dan PALING BARU
        $mahasiswa = Mahasiswa::with('mou')
                        ->where('user_id', $user->id)
                        ->where('status', 'aktif')
                        ->latest()
                        ->firstOrFail();
        
        $ruangans = Ruangan::orderBy('nm_ruangan', 'asc')->get();
        
        return view('mahasiswa.dispensasi.create', compact('mahasiswa', 'ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:biasa,terlambat',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'file_surat' => 'required_if:kategori,biasa|nullable|mimes:pdf|max:1024',
            'ruangan_id' => 'required_if:kategori,terlambat',
            'nama_penyetuju' => 'required_if:kategori,terlambat|nullable|string',
            'jabatan_penyetuju' => 'required_if:kategori,terlambat|nullable|string',
            'ttd_penyetuju' => 'required_if:kategori,terlambat|nullable|string',
            'keterangan' => 'required|string', 
        ]);

        $user = Auth::user();
        
        // FIX: Selalu ambil data magang yang sedang AKTIF dan PALING BARU
        $mahasiswa = Mahasiswa::with('mou')
                        ->where('user_id', $user->id)
                        ->where('status', 'aktif')
                        ->latest()
                        ->firstOrFail();

        $path = null;
        $keteranganTerlambatJson = null;

        if ($request->kategori === 'biasa') {
            $tanggalMulai = $request->tanggal_mulai;
            $tanggalSelesai = $request->tanggal_selesai;

            if ($request->hasFile('file_surat')) {
                $path = $request->file('file_surat')->store('dispensasi', 'public');
            }
        } else {
            $tanggalMulai = $request->tanggal_mulai ?? Carbon::now()->toDateString(); 
            $tanggalSelesai = $request->tanggal_selesai ?? $tanggalMulai;
            
            $ruangan = Ruangan::find($request->ruangan_id);

            $dataTerlambat = [
                'ruangan' => $ruangan ? $ruangan->nm_ruangan : '-',
                'nama_penyetuju' => $request->nama_penyetuju,
                'jabatan_penyetuju' => $request->jabatan_penyetuju,
                'ttd_penyetuju' => $request->ttd_penyetuju, 
            ];
            $keteranganTerlambatJson = json_encode($dataTerlambat);

            $pdf = Pdf::loadView('mahasiswa.dispensasi.pdf_terlambat', [
                'mahasiswa' => $mahasiswa,
                'dataTerlambat' => $dataTerlambat,
                'keterangan' => $request->keterangan,
                'tanggal' => Carbon::parse($tanggalMulai)->isoFormat('D MMMM YYYY'),
            ]);

            $fileName = 'dispensasi/Terlambat_' . Str::slug($mahasiswa->nm_mahasiswa) . '_' . time() . '.pdf';
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

            if (!$sudahMasuk) {
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
            'kategori' => 'required|in:biasa,terlambat',
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

            if ((int) $mhs->ruangan_id === (int) $ruangan->id) return true;
            if ($mhs->roomSequences()->where('ruangan_id', $ruangan->id)->exists()) return true;
            if ($mhs->shiftSchedules()->where('ruangan_id', $ruangan->id)->exists()) return true;
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
                
                $jamMasuk = $date->copy()->setTime(7, 0, 0);
                $jamKeluar = $date->copy()->setTime(14, 0, 0);
                $tipeShift = "Pagi/Reguler";

                if (stripos($dispensasi->keterangan, 'malam') !== false) {
                    $jamMasuk = $date->copy()->setTime(21, 0, 0);
                    $jamKeluar = $date->copy()->addDay()->setTime(7, 0, 0); // Lintas Hari
                    $tipeShift = "Malam";
                } elseif (stripos($dispensasi->keterangan, 'siang') !== false) {
                    $jamMasuk = $date->copy()->setTime(14, 0, 0);
                    $jamKeluar = $date->copy()->setTime(21, 0, 0);
                    $tipeShift = "Siang";
                }

                $absenMasuk = Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)
                    ->where('type', 'masuk')
                    ->whereDate('created_at', $date)
                    ->latest()
                    ->first();

                $absenKeluar = Absensi::where('mahasiswa_id', $dispensasi->mahasiswa_id)
                    ->where('type', 'keluar')
                    ->where(function($q) use ($date, $jamKeluar) {
                        $q->whereDate('created_at', $date)
                          ->orWhereDate('created_at', $jamKeluar->toDateString());
                    })
                    ->latest()
                    ->first();

                // EKSEKUSI: BUAT / UPDATE ABSEN MASUK
                if ($absenMasuk) {
                    if (str_contains($absenMasuk->keterangan, 'Pending')) {
                        $absenMasuk->jam_masuk = $jamMasuk;
                        $absenMasuk->keterangan = "Dispen Disetujui (Shift $tipeShift)";
                        $absenMasuk->save();
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
                if (!$absenKeluar && \Carbon\Carbon::now()->gt($jamKeluar)) { 
                    $waktuMasuk = $absenMasuk ? Carbon::parse($absenMasuk->jam_masuk) : $jamMasuk;
                    
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
        }
    }
}