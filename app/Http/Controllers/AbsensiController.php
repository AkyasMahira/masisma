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
            ->first();

        return view('absensi.card', compact('mahasiswa', 'absenHariIni'));
    }

    // 1 tombol toggle
    public function toggle($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();
        $today = Carbon::today();

        $absen = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereDate('created_at', $today)
            ->first();

        // Jika belum absen hari ini → catat jam masuk
        if (!$absen) {
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jam_masuk' => now(),
            ]);

            return back()->with('success', 'Absensi Masuk berhasil direkam!');
        }

        // Jika sudah masuk tapi belum keluar → update jam keluar
        if (!$absen->jam_keluar) {
            $absen->update(['jam_keluar' => now()]);
            return back()->with('success', 'Absensi Keluar berhasil direkam!');
        }

        // Jika sudah masuk & keluar → kasih info
        return back()->with('error', 'Anda sudah absen masuk & keluar hari ini.');
    }


    // Admin view
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $ruangans = Ruangan::all();
        $today = Carbon::today();

        $query = Absensi::with('mahasiswa')
            ->whereDate('created_at', $today);

        if ($request->filled('ruangan_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_id);
            });
        }

        $absensis = $query->orderBy('created_at', 'desc')->get();

        return view('absensi.index', compact('absensis', 'ruangans'));
    }
}
