<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // Public card (no auth) 
    public function card($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        $today = Carbon::today();
        $absenHariIni = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->first();

        return view('absensi.card', compact('mahasiswa', 'absenHariIni'));
    }

    // 1 tombol toggle
    public function toggle($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();
        $today = Carbon::today();
        $sekarang = now();

        // Cek absensi terakhir hari ini
        $lastAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->first();

        // Jika belum ada absen hari ini atau absen terakhir adalah keluar, buat absen masuk baru
        if (!$lastAbsen || $lastAbsen->type === 'keluar') {
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jam_masuk' => $sekarang,
                'type' => 'masuk'
            ]);

            return back()->with('success', 'Absensi Masuk berhasil direkam!');
        }

        // Jika absen terakhir adalah masuk, lakukan absen keluar
        if ($lastAbsen->type === 'masuk') {
            $jamMasuk = Carbon::parse($lastAbsen->jam_masuk);

            // Cek cooldown 3 jam
            $cooldownBerakhir = $jamMasuk->copy()->addHours(3);
            if ($sekarang->lt($cooldownBerakhir)) {
                $menitTersisa = $sekarang->diffInMinutes($cooldownBerakhir);
                return back()->with('error', "Mohon tunggu {$menitTersisa} menit lagi sebelum absen keluar.");
            }

            // Hitung durasi
            $durasiMenit = $jamMasuk->diffInMinutes($sekarang);

            // Buat record absen keluar baru
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => $sekarang,
                'type' => 'keluar',
                'durasi_menit' => $durasiMenit
            ]);

            $message = "Absensi Keluar berhasil direkam! Durasi: " . floor($durasiMenit / 60) . " jam " . ($durasiMenit % 60) . " menit.";
            return back()->with('success', $message);
        }

        // Shouldn't reach here, but just in case
        return back()->with('error', 'Terjadi kesalahan pada sistem absensi.');
    }


    // Admin view
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $ruangans = Ruangan::all();

        // Build query
        $query = Absensi::with(['mahasiswa', 'mahasiswa.ruangan']);

        // Filter Ruangan
        if ($request->filled('ruangan_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_id);
            });
        }

        // Filter Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Paginate and keep query string for links
        $absensi = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Return view with the variable name expected by blade
        return view('absensi.index', compact('absensi', 'ruangans'));
    }
}
