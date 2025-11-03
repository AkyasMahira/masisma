<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary counts
        $totalMahasiswa = Mahasiswa::count();
        $totalRuangan = Ruangan::count();
        $totalUsers = User::count();

        // Today's absensi (as recent activity)
        $todayAbsensi = Absensi::whereDate('created_at', Carbon::today())->count();

        // Mahasiswa per month for last 7 months (labels + data)
        $months = [];
        $mahasiswaPerMonth = [];
        for ($i = 6; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $months[] = $dt->format('M');
            $mahasiswaPerMonth[] = Mahasiswa::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->count();
        }

        // Ruangan distribution (labels + data)
        $ruangans = Ruangan::withCount('mahasiswa')->get();
        $ruanganLabels = $ruangans->pluck('nm_ruangan')->toArray();
        $ruanganData = $ruangans->pluck('mahasiswa_count')->toArray();

        return view('dashboard', compact(
            'totalMahasiswa',
            'totalRuangan',
            'totalUsers',
            'todayAbsensi',
            'months',
            'mahasiswaPerMonth',
            'ruanganLabels',
            'ruanganData'
        ));
    }
}
