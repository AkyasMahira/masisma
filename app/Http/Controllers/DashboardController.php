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
        // 1. Summary Counts (Card Atas)
        $totalMahasiswa = Mahasiswa::where('status', 'aktif')->count(); // Filter status aktif lebih akurat
        $totalRuangan   = Ruangan::count();
        $totalUsers     = User::count();

        // 2. Absensi Hari Ini (Card Atas)
        $todayAbsensi = Absensi::whereDate('created_at', Carbon::today())->count();

        // 3. Grafik Mahasiswa (Line Chart) - 7 Bulan Terakhir
        $months = [];
        $mahasiswaPerMonth = [];

        for ($i = 6; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            
            // Label Bulan (Jan, Feb, Mar)
            $months[] = $dt->format('M'); 
            
            // Hitung data pada bulan & tahun tersebut
            $count = Mahasiswa::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->count();
                
            $mahasiswaPerMonth[] = $count;
        }

        // 4. Grafik Ruangan (Doughnut Chart)
        // Pastikan di Model Ruangan ada function public function mahasiswa() { return $this->hasMany(...); }
        $ruangans = Ruangan::withCount('mahasiswa')->get();
        
        $ruanganLabels = $ruangans->pluck('nm_ruangan')->toArray();
        $ruanganData   = $ruangans->pluck('mahasiswa_count')->toArray();

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