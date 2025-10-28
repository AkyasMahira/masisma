<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    // public card (no auth) to show mahasiswa info and buttons
    public function card($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();
        return view('absensi.card', compact('mahasiswa'));
    }

    public function masuk(Request $request, $token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        $today = Carbon::now()->toDateString();
        $already = $mahasiswa->absensis()->whereDate('created_at', $today)->where('type', 'masuk')->exists();
        if ($already) {
            return redirect()->back()->with('error', 'You have already checked in today.');
        }

        Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'type' => 'masuk',
        ]);

        return redirect()->back()->with('success', 'Absensi masuk tercatat.');
    }

    public function keluar(Request $request, $token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        $today = Carbon::now()->toDateString();
        $hasMasuk = $mahasiswa->absensis()->whereDate('created_at', $today)->where('type', 'masuk')->exists();
        if (!$hasMasuk) {
            return redirect()->back()->with('error', 'Cannot check out without checking in first.');
        }

        $alreadyKeluar = $mahasiswa->absensis()->whereDate('created_at', $today)->where('type', 'keluar')->exists();
        if ($alreadyKeluar) {
            return redirect()->back()->with('error', 'You have already checked out today.');
        }

        Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'type' => 'keluar',
        ]);

        return redirect()->back()->with('success', 'Absensi keluar tercatat.');
    }

    // admin view for today's absensi with filters
    public function index(Request $request)
    {
        // only admin
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $ruangans = Ruangan::all();

        $query = Absensi::with('mahasiswa')->whereDate('created_at', Carbon::now()->toDateString());

        if ($request->filled('ruangan_id')) {
            $ruanganId = $request->ruangan_id;
            $query->whereHas('mahasiswa', function ($q) use ($ruanganId) {
                $q->where('ruangan_id', $ruanganId);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $absensis = $query->orderBy('created_at', 'desc')->get();

        return view('absensi.index', compact('absensis', 'ruangans'));
    }
}
