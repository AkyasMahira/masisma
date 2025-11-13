<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // === Public card (tanpa login, diakses via token) ===
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

    // === Tombol toggle (masuk / keluar otomatis) ===
    public function toggle($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();
        $today = Carbon::today();
        $sekarang = now();

        // Ambil absensi terakhir hari ini
        $lastAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->first();

        // --- Jika belum ada absen hari ini, buat absen masuk ---
        if (!$lastAbsen) {
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jam_masuk' => $sekarang,
                'type' => 'masuk'
            ]);

            return back()->with('success', 'Absensi masuk berhasil direkam! Kamu sedang dalam fase praktik, tunggu 3 jam sebelum absen keluar.');
        }

        // --- Jika terakhir adalah absen masuk ---
        if ($lastAbsen->type === 'masuk') {
            $jamMasuk = Carbon::parse($lastAbsen->jam_masuk);

            // Cek cooldown 3 jam sebelum boleh keluar
            $cooldownBerakhir = $jamMasuk->copy()->addHours(3);
            if ($sekarang->lt($cooldownBerakhir)) {
                $menitTersisa = $sekarang->diffInMinutes($cooldownBerakhir);
                $jamTersisa = floor($menitTersisa / 60);
                $sisaMenit = $menitTersisa % 60;

                return back()->with('error', "Kamu sudah absen masuk hari ini. Tunggu sekitar {$jamTersisa} jam {$sisaMenit} menit lagi sebelum bisa absen keluar.");
            }

            // Hitung durasi
            $durasiMenit = $jamMasuk->diffInMinutes($sekarang);

            // Simpan absen keluar
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => $sekarang,
                'type' => 'keluar',
                'durasi_menit' => $durasiMenit
            ]);

            $message = "Absensi keluar berhasil direkam! Durasi: " .
                floor($durasiMenit / 60) . " jam " .
                ($durasiMenit % 60) . " menit. Kamu sedang dalam fase cooldown, tunggu 3 jam sebelum bisa absen lagi.";

            return back()->with('success', $message);
        }

        // --- Jika terakhir adalah absen keluar ---
        if ($lastAbsen->type === 'keluar') {
            $jamKeluar = Carbon::parse($lastAbsen->jam_keluar);
            $cooldownBerakhir = $jamKeluar->copy()->addHours(3);

            if ($sekarang->lt($cooldownBerakhir)) {
                $menitTersisa = $sekarang->diffInMinutes($cooldownBerakhir);
                $jamTersisa = floor($menitTersisa / 60);
                $sisaMenit = $menitTersisa % 60;

                return back()->with('error', "Kamu sudah absen keluar hari ini. Tunggu sekitar {$jamTersisa} jam {$sisaMenit} menit lagi sebelum bisa absen masuk kembali.");
            }

            // Buat absen masuk baru setelah cooldown
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jam_masuk' => $sekarang,
                'type' => 'masuk'
            ]);

            return back()->with('success', 'Absensi masuk berhasil direkam kembali setelah masa cooldown!');
        }

        // Jika terjadi kondisi tak terduga
        return back()->with('error', 'Terjadi kesalahan pada sistem absensi.');
    }

    // === Halaman admin untuk melihat data absensi ===
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $ruangans = Ruangan::all();

        $query = Absensi::with(['mahasiswa', 'mahasiswa.ruangan']);

        if ($request->filled('ruangan_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_id);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $absensi = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('absensi.index', compact('absensi', 'ruangans'));
    }
}
