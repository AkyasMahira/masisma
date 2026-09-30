<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Mou;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. DATA SUMMARY CARD ADMIN
        // ==========================================
        $totalMahasiswa = Mahasiswa::where('status', 'aktif')->count();
        $totalRuangan   = Ruangan::count();
        $totalUsers     = User::count();
        $todayAbsensi   = Absensi::whereDate('created_at', Carbon::today())->count();

        // ==========================================
        // 2. DATA UNTUK 4 GRAFIK (CHARTS)
        // ==========================================
        // A. Grafik Mahasiswa (Line Chart) - 7 Bulan Terakhir
        $months = []; $mahasiswaPerMonth = [];
        for ($i = 6; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $months[] = $dt->translatedFormat('M y');
            $mahasiswaPerMonth[] = Mahasiswa::whereYear('created_at', $dt->year)->whereMonth('created_at', $dt->month)->count();
        }

        // B. Grafik Tren Absensi 7 Hari Terakhir (Bar Chart)
        $last7Days = []; $absensi7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = $date->translatedFormat('d M');
            $absensi7Days[] = Absensi::whereDate('created_at', $date->toDateString())->count();
        }

        // C. Grafik Ruangan (Doughnut Chart)
        $ruangansChart = Ruangan::withCount(['mahasiswa' => function($q) {
            $q->where('status', 'aktif');
        }])->get();
        $ruanganLabels = $ruangansChart->pluck('nm_ruangan')->toArray();
        $ruanganData   = $ruangansChart->pluck('mahasiswa_count')->toArray();

        // D. Grafik Status Mahasiswa (Pie Chart)
        $statusRaw = Mahasiswa::select('status', DB::raw('count(*) as total'))->groupBy('status')->get();
        $statusLabels = $statusRaw->pluck('status')->map(fn($item) => ucfirst($item))->toArray();
        $statusData   = $statusRaw->pluck('total')->toArray();


        // ==========================================
        // 3. DATA KALENDER 1: JADWAL INDIVIDU
        // ==========================================
        $mahasiswaAktif = Mahasiswa::with(['ruangan', 'roomSequences.ruangan', 'mou'])
                                   ->where('status', 'aktif')
                                   ->get();
        
        $calendarEvents = [];
        $colors = ['#7c1316', '#1e40af', '#166534', '#b45309', '#6b21a8', '#0f766e', '#be123c', '#4338ca', '#047857'];
        $roomColorMap = []; $colorIndex = 0;

        foreach ($mahasiswaAktif as $mhs) {
            $institusi = $mhs->univ_asal ?? '-';

            if ($mhs->roomSequences->isNotEmpty()) {
                foreach ($mhs->roomSequences as $seq) {
                    $namaRuang = $seq->ruangan ? $seq->ruangan->nm_ruangan : 'Tanpa Ruangan';
                    if (!isset($roomColorMap[$namaRuang])) { $roomColorMap[$namaRuang] = $colors[$colorIndex % count($colors)]; $colorIndex++; }

                    $calendarEvents[] = [
                        'id'    => 'mhs-'.$mhs->id.'-'.$seq->id,
                        'title' => $mhs->nm_mahasiswa . ' (' . $namaRuang . ')',
                        'start' => $seq->start_date,
                        'end'   => $seq->end_date ? Carbon::parse($seq->end_date)->addDay()->format('Y-m-d') : null,
                        'color' => $roomColorMap[$namaRuang],
                        'extendedProps' => ['ruangan' => $namaRuang, 'institusi' => $institusi]
                    ];
                }
            } else {
                $namaRuang = $mhs->ruangan ? $mhs->ruangan->nm_ruangan : ($mhs->nm_ruangan ?? 'Tanpa Ruangan');
                if (!isset($roomColorMap[$namaRuang])) { $roomColorMap[$namaRuang] = $colors[$colorIndex % count($colors)]; $colorIndex++; }

                if ($mhs->tanggal_mulai) {
                    $calendarEvents[] = [
                        'id'    => 'mhs-'.$mhs->id,
                        'title' => $mhs->nm_mahasiswa . ' (' . $namaRuang . ')',
                        'start' => $mhs->tanggal_mulai->format('Y-m-d'),
                        'end'   => $mhs->tanggal_berakhir ? $mhs->tanggal_berakhir->addDay()->format('Y-m-d') : null,
                        'color' => $roomColorMap[$namaRuang],
                        'extendedProps' => ['ruangan' => $namaRuang, 'institusi' => $institusi]
                    ];
                }
            }
        }

        // ==========================================
        // 4. DATA CARD KETERSEDIAAN RUANGAN
        // ==========================================
        $rooms = Ruangan::all();
        $todayStr = Carbon::today()->format('Y-m-d');
        $roomCards = [];

        foreach ($rooms as $room) {
            $kuota = $room->kuota_ruangan ?? 0;
            $occupantsToday = [];
            $nextEndDates = []; // Kapan ruangan ini akan ditinggalkan

            foreach ($mahasiswaAktif as $mhs) {
                $inRoomToday = false;
                $endDate = null;

                // Cek jadwal rolling
                if ($mhs->roomSequences->isNotEmpty()) {
                    foreach ($mhs->roomSequences as $seq) {
                        if ($seq->ruangan_id == $room->id && $seq->start_date <= $todayStr && (!$seq->end_date || $seq->end_date >= $todayStr)) {
                            $inRoomToday = true;
                            $endDate = $seq->end_date;
                        }
                    }
                } 
                // Cek jadwal utama (tanpa rolling)
                else {
                    if ($mhs->ruangan_id == $room->id && $mhs->tanggal_mulai && $mhs->tanggal_mulai->format('Y-m-d') <= $todayStr && (!$mhs->tanggal_berakhir || $mhs->tanggal_berakhir->format('Y-m-d') >= $todayStr)) {
                        $inRoomToday = true;
                        $endDate = $mhs->tanggal_berakhir ? $mhs->tanggal_berakhir->format('Y-m-d') : null;
                    }
                }

                if ($inRoomToday) {
                    $institusi = $mhs->univ_asal ?? '-';
                    $occupantsToday[] = $mhs->nm_mahasiswa . ' (' . $institusi . ')';
                    if ($endDate) {
                        $nextEndDates[] = $endDate;
                    }
                }
            }

            $terisi = count($occupantsToday);
            $sisa = max(0, $kuota - $terisi);
            
            // Hitung kapan siap diisi lagi
            $nextAvailableStr = 'Saat ini juga (Sekarang)';
            if ($sisa == 0 && count($nextEndDates) > 0) {
                sort($nextEndDates); // Cari tanggal keluar terdekat
                $nextAvailableStr = Carbon::parse($nextEndDates[0])->addDay()->translatedFormat('d F Y');
            } elseif ($sisa == 0 && count($nextEndDates) == 0) {
                $nextAvailableStr = 'Belum diketahui batasnya';
            }

            $roomCards[] = [
                'nama' => $room->nm_ruangan,
                'kuota' => $kuota,
                'terisi' => $terisi,
                'sisa' => $sisa,
                'status' => $sisa > 0 ? 'Tersedia' : 'Penuh',
                'next_available' => $nextAvailableStr,
                'occupants' => $occupantsToday
            ];
        }

        // ==========================================
        // 5. DATA UNTUK USER BIASA
        // ==========================================
        $hour = date('H');
        if ($hour < 12) $greeting = 'Selamat Pagi';
        elseif ($hour < 15) $greeting = 'Selamat Siang';
        elseif ($hour < 18) $greeting = 'Selamat Sore';
        else $greeting = 'Selamat Malam';

        return view('dashboard', compact(
            'totalMahasiswa', 'totalRuangan', 'totalUsers', 'todayAbsensi',
            'months', 'mahasiswaPerMonth', 'last7Days', 'absensi7Days', 
            'ruanganLabels', 'ruanganData', 'statusLabels', 'statusData', // Data chart lengkap
            'calendarEvents', 'roomCards', 'greeting'
        ));
    }
    
public function evaluasiInstitusi(Request $request)
    {
        // ==========================================
        // A. DATA SUMMARY DASHBOARD
        // ==========================================
        $totalMahasiswa = \App\Models\Mahasiswa::where('status', 'aktif')->count();
        $totalRuangan   = \App\Models\Ruangan::count();
        $totalUsers     = \App\Models\User::count();
        $todayAbsensi   = \App\Models\Absensi::whereDate('created_at', \Carbon\Carbon::today())->count();

        // Grafik Tren Absensi 7 Hari Terakhir
        $last7Days = []; $absensi7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i);
            $last7Days[] = $date->translatedFormat('d M');
            $absensi7Days[] = \App\Models\Absensi::whereDate('created_at', $date->toDateString())->count();
        }

        // ==========================================
        // B. PROSES FILTER & EVALUASI MAHASISWA
        // ==========================================
        $search = $request->input('search');
        $filterInstansi = $request->input('instansi');
        $filterProdi = $request->input('prodi');
        $sortMahasiswa = $request->input('sort_mhs', 'asc');

        $query = \App\Models\Mahasiswa::with('mou')->whereNotNull('nilai_evaluasi');

        if ($search) {
            $query->where('nm_mahasiswa', 'like', "%{$search}%");
        }
        
        // Filter Instansi menggunakan relasi MOU
        if ($filterInstansi) {
            $query->whereHas('mou', function($q) use ($filterInstansi) {
                $q->where(function($subQuery) use ($filterInstansi) {
                    $subQuery->where('nama_instansi', $filterInstansi)
                             ->orWhere('nama_universitas', $filterInstansi);
                });
            });
        }
        
        if ($filterProdi) {
            $query->where('prodi', $filterProdi);
        }

        $mahasiswas = $query->get();
        $ruanganMap = \App\Models\Ruangan::pluck('nm_ruangan', 'id')->toArray();

        $rekap = [];
        $detailMahasiswa = [];
        
        // Variabel untuk Grafik Radar Kriteria
        $radarSum = ['kedisiplinan' => 0, 'komunikasi' => 0, 'kepatuhan' => 0, 'pemahaman' => 0, 'keterampilan' => 0];
        $radarCount = 0;

        foreach ($mahasiswas as $mhs) {
            $instansi = $mhs->univ_asal ?? 'Umum';
            $prodi = $mhs->prodi ?? 'Tidak Diketahui';
            $key = $instansi . '|' . $prodi;

            if (!isset($rekap[$key])) {
                $rekap[$key] = [
                    'instansi' => $instansi,
                    'prodi' => $prodi,
                    'total_mahasiswa' => 0,
                    'total_penilaian' => 0,
                    'sum_kedisiplinan' => 0, 'sum_komunikasi' => 0, 'sum_kepatuhan' => 0,
                    'sum_pemahaman' => 0, 'sum_keterampilan' => 0,
                ];
            }

            $evaluasi = is_string($mhs->nilai_evaluasi) ? json_decode($mhs->nilai_evaluasi, true) : ($mhs->nilai_evaluasi ?? []);

            if (!empty($evaluasi)) {
                $rekap[$key]['total_mahasiswa']++;
                
                $mhs_sum_sikap = 0; 
                $mhs_sum_keahlian = 0; 
                $mhs_total_penilaian = 0;
                $rincian_ruangan = [];

                foreach ($evaluasi as $ruangan_id => $nilai) {
                    // Ambil detail nilai per kriteria
                    $v_disiplin = $nilai['sikap']['kedisiplinan'] ?? 0;
                    $v_komunikasi = $nilai['sikap']['komunikasi'] ?? 0;
                    $v_kepatuhan = $nilai['sikap']['kepatuhan'] ?? 0;
                    $v_pemahaman = $nilai['keahlian']['pemahaman'] ?? 0;
                    $v_keterampilan = $nilai['keahlian']['keterampilan'] ?? 0;
                    $catatan = $nilai['catatan'] ?? 'Tidak ada catatan.';

                    $rekap[$key]['total_penilaian']++;
                    $rekap[$key]['sum_kedisiplinan'] += $v_disiplin;
                    $rekap[$key]['sum_komunikasi'] += $v_komunikasi;
                    $rekap[$key]['sum_kepatuhan'] += $v_kepatuhan;
                    $rekap[$key]['sum_pemahaman'] += $v_pemahaman;
                    $rekap[$key]['sum_keterampilan'] += $v_keterampilan;

                    // Hitung untuk Grafik Radar Global
                    $radarSum['kedisiplinan'] += $v_disiplin;
                    $radarSum['komunikasi'] += $v_komunikasi;
                    $radarSum['kepatuhan'] += $v_kepatuhan;
                    $radarSum['pemahaman'] += $v_pemahaman;
                    $radarSum['keterampilan'] += $v_keterampilan;
                    $radarCount++;

                    $mhs_total_penilaian++;
                    
                    // Rata-rata per ruangan
                    $rata_sikap_ruang = ($v_disiplin + $v_komunikasi + $v_kepatuhan) / 3;
                    $rata_keahlian_ruang = ($v_pemahaman + $v_keterampilan) / 2;
                    
                    $mhs_sum_sikap += $rata_sikap_ruang;
                    $mhs_sum_keahlian += $rata_keahlian_ruang;

                    $nama_ruangan = $ruanganMap[$ruangan_id] ?? "Ruang ID {$ruangan_id}";
                    
                    // Simpan ke rincian untuk ditampilkan di tabel collapse
                    $rincian_ruangan[] = [
                        'ruangan_id' => $ruangan_id,
                        'ruangan' => $nama_ruangan,
                        'sikap' => round($rata_sikap_ruang, 1),
                        'keahlian' => round($rata_keahlian_ruang, 1),
                        'detail' => [
                            'kedisiplinan' => $v_disiplin,
                            'komunikasi' => $v_komunikasi,
                            'kepatuhan' => $v_kepatuhan,
                            'pemahaman' => $v_pemahaman,
                            'keterampilan' => $v_keterampilan,
                            'catatan' => $catatan
                        ]
                    ];
                }

                // Kalkulasi akhir mahasiswa bersangkutan
                $avg_mhs_sikap = $mhs_total_penilaian > 0 ? ($mhs_sum_sikap / $mhs_total_penilaian) : 0;
                $avg_mhs_keahlian = $mhs_total_penilaian > 0 ? ($mhs_sum_keahlian / $mhs_total_penilaian) : 0;
                $score_akhir_mhs = ($avg_mhs_sikap * 0.4) + ($avg_mhs_keahlian * 0.6);

                $detailMahasiswa[] = [
                    'id' => $mhs->id,
                    'nama' => $mhs->nm_mahasiswa,
                    'instansi' => $instansi,
                    'prodi' => $prodi,
                    'rata_sikap' => round($avg_mhs_sikap, 1),
                    'rata_keahlian' => round($avg_mhs_keahlian, 1),
                    'score_akhir' => round($score_akhir_mhs, 1),
                    'rincian' => $rincian_ruangan
                ];
            }
        }

        // --- Proses Data untuk Rekap Institusi ---
        $hasilEvaluasi = collect($rekap)->map(function ($item) {
            $total = $item['total_penilaian'];
            if ($total > 0) {
                $item['avg_kedisiplinan'] = round($item['sum_kedisiplinan'] / $total, 1);
                $item['avg_komunikasi'] = round($item['sum_komunikasi'] / $total, 1);
                $item['avg_kepatuhan'] = round($item['sum_kepatuhan'] / $total, 1);
                $item['avg_pemahaman'] = round($item['sum_pemahaman'] / $total, 1);
                $item['avg_keterampilan'] = round($item['sum_keterampilan'] / $total, 1);
                
                $item['rata_sikap'] = round(($item['avg_kedisiplinan'] + $item['avg_komunikasi'] + $item['avg_kepatuhan']) / 3, 1);
                $item['rata_keahlian'] = round(($item['avg_pemahaman'] + $item['avg_keterampilan']) / 2, 1);
                $item['score_akhir'] = round(($item['rata_sikap'] * 0.4) + ($item['rata_keahlian'] * 0.6), 1);
            }
            return $item;
        })->sortByDesc('score_akhir')->values();

        // --- Proses Sorting Detail Mahasiswa ---
        $detailMahasiswa = collect($detailMahasiswa);
        if ($sortMahasiswa === 'desc') {
            $detailMahasiswa = $detailMahasiswa->sortByDesc('score_akhir')->values();
        } else {
            $detailMahasiswa = $detailMahasiswa->sortBy('score_akhir')->values();
        }

        // --- Kalkulasi Data Radar Chart Global ---
        $chartRadarData = [];
        if ($radarCount > 0) {
            $chartRadarData = [
                round($radarSum['kedisiplinan'] / $radarCount, 1),
                round($radarSum['komunikasi'] / $radarCount, 1),
                round($radarSum['kepatuhan'] / $radarCount, 1),
                round($radarSum['pemahaman'] / $radarCount, 1),
                round($radarSum['keterampilan'] / $radarCount, 1),
            ];
        }

        // --- Ambil List Instansi & Prodi untuk Dropdown Filter ---
        $listInstansi = \App\Models\Mou::selectRaw('COALESCE(nama_instansi, nama_universitas) as label')
            ->whereNotNull('nama_instansi')
            ->orWhereNotNull('nama_universitas')
            ->distinct()
            ->pluck('label')
            ->filter()
            ->toArray();

        $listProdi = \App\Models\Mahasiswa::whereNotNull('prodi')->distinct()->pluck('prodi')->toArray();

        // Data untuk Grafik Nilai Rata-rata Institusi (Bar Chart)
        $chartInstitusiLabels = $hasilEvaluasi->pluck('instansi')->toArray();
        $chartInstitusiScores = $hasilEvaluasi->pluck('score_akhir')->toArray();

        return view('admin.evaluasi.index', compact(
            'totalMahasiswa', 'totalRuangan', 'totalUsers', 'todayAbsensi',
            'last7Days', 'absensi7Days',
            'chartInstitusiLabels', 'chartInstitusiScores', 'chartRadarData',
            'hasilEvaluasi', 'detailMahasiswa', 'listInstansi', 'listProdi', 
            'search', 'filterInstansi', 'filterProdi', 'sortMahasiswa'
        ));
    }
public function hapusNilai($mahasiswa_id, $ruangan_id)
{
    $mahasiswa = \App\Models\Mahasiswa::findOrFail($mahasiswa_id);

    // Ambil data nilai
    $nilaiJson = is_string($mahasiswa->nilai_ruangan_json) ? json_decode($mahasiswa->nilai_ruangan_json, true) : ($mahasiswa->nilai_ruangan_json ?? []);
    $evaluasiJson = is_string($mahasiswa->nilai_evaluasi) ? json_decode($mahasiswa->nilai_evaluasi, true) : ($mahasiswa->nilai_evaluasi ?? []);

    // Hapus data berdasarkan kunci ruangan_id
    if (isset($nilaiJson[$ruangan_id])) {
        unset($nilaiJson[$ruangan_id]);
    }
    
    if (isset($evaluasiJson[$ruangan_id])) {
        unset($evaluasiJson[$ruangan_id]);
    }

    // Update Database
    $mahasiswa->nilai_ruangan_json = !empty($nilaiJson) ? json_encode($nilaiJson) : null;
    $mahasiswa->nilai_evaluasi = !empty($evaluasiJson) ? json_encode($evaluasiJson) : null;
    $mahasiswa->save();

    return back()->with('success', 'Nilai di ruangan tersebut berhasil dihapus.');
}
}