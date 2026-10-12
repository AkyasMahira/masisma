<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class KepalaRuanganController extends Controller
{
   public function dashboard(Request $request)
    {
        // 1. Validasi & Identifikasi Ruangan
        if (Auth::user()->role !== 'ruangan') {
            abort(403, 'Akses Ditolak.');
        }

        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        if (!$ruangan) {
            return abort(404, 'Akun Ruangan belum disetting.');
        }

        // 2. Parameter Filter & Tanggal
        $filter = $request->query('filter', 'bulan_ini');
        $search = $request->query('search', '');
        $page = $request->query('page', 1);
        $perPage = 10;

        // Tentukan Range Waktu Berdasarkan Filter
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!$startDate || !$endDate) {
            if ($filter == 'minggu_ini') {
                $startDate = now()->startOfWeek()->format('Y-m-d');
                $endDate = now()->endOfWeek()->format('Y-m-d');
            } elseif ($filter == 'tahun_ini') {
                $startDate = now()->startOfYear()->format('Y-m-d');
                $endDate = now()->endOfYear()->format('Y-m-d');
            } else { // default bulan_ini
                $startDate = now()->startOfMonth()->format('Y-m-d');
                $endDate = now()->endOfMonth()->format('Y-m-d');
            }
        }

        // 3. AMBIL SEMUA DATA (TANPA FILTER NILAI_KARU LAMA)
        $query = Mahasiswa::with(['roomSequences.ruangan', 'shiftSchedules', 'absensis', 'dispensasis'])
            ->where(function ($q) use ($ruangan) {
                // Pokoknya, kalau dia pernah/sedang di ruangan ini, TAMPILKAN!
                $q->where('ruangan_id', $ruangan->id)
                  ->orWhereHas('roomSequences', function ($sq) use ($ruangan) {
                      $sq->where('ruangan_id', $ruangan->id);
                  })
                  ->orWhereHas('shiftSchedules', function ($sq) use ($ruangan) {
                      $sq->where('ruangan_id', $ruangan->id);
                  });
            });

        // Filter Pencarian Nama
        if (!empty($search)) {
            $query->where('nm_mahasiswa', 'like', "%{$search}%");
        }

        $allMahasiswas = $query->get();
        $processedData = collect();
        $globalHadir = 0;

     foreach ($allMahasiswas as $mhs) {
            $logs = [];
            $countHadir = 0; 
            $countAlfa = 0; 
            $countDispen = 0; 
            $countTarget = 0;
            
            // 1. Kumpulkan Tanggal Jadwal Ruangan Ini Saja
            $assignedDates = [];
            foreach ($mhs->roomSequences->where('ruangan_id', $ruangan->id) as $seq) {
                $period = CarbonPeriod::create($seq->start_date, $seq->end_date);
                foreach ($period as $d) { 
                    $assignedDates[$d->format('Y-m-d')] = true; 
                }
            }
            
            // 2. Kumpulkan Peta Shift Khusus Ruangan Ini
            $shiftMap = [];
            foreach ($mhs->shiftSchedules->where('ruangan_id', $ruangan->id) as $shift) {
                $dateStr = Carbon::parse($shift->tanggal)->format('Y-m-d');
                $assignedDates[$dateStr] = true;
                $shiftMap[$dateStr] = $shift->shift_type;
            }

            // Penentuan Range Loop
            $loopRange = ($filter == 'semua') ? array_keys($assignedDates) : CarbonPeriod::create($startDate, $endDate);

            // --- Indexing Absen ---
            $absenIndexed = [];
            foreach($mhs->absensis as $a) {
                if ($a->created_at) {
                    $absenIndexed[\Carbon\Carbon::parse($a->created_at)->format('Y-m-d')] = $a; 
                }
            }

            // --- Indexing Dispen ---
            $dispenIndexed = [];
            if ($mhs->dispensasis) {
                foreach($mhs->dispensasis->where('status', 'approved') as $d) {
                    if ($d->tanggal_mulai && $d->tanggal_selesai) {
                        $period = \Carbon\CarbonPeriod::create($d->tanggal_mulai, $d->tanggal_selesai);
                        foreach($period as $dt) {
                            $dispenIndexed[$dt->format('Y-m-d')] = true;
                        }
                    }
                }
            }

            foreach ($loopRange as $dateItem) {
                $dateLoop = ($dateItem instanceof Carbon) ? $dateItem : Carbon::parse($dateItem);
                $dateStr = $dateLoop->format('Y-m-d');
                
                // KUNCI FIX: Jika hari ini tidak ada jadwal di ruangan ini, ABAIKAN TOTAL!
                if (!isset($assignedDates[$dateStr])) {
                    continue; 
                }

                // =============================================================
                // CEK STATUS LIBUR (Akhir Pekan Reguler atau Shift Libur)
                // =============================================================
                $shiftType = $shiftMap[$dateStr] ?? 'Reguler';
                $isHoliday = false;

                if (strtolower($shiftType) === 'libur') {
                    $isHoliday = true; // Jika di tabel shift diset "Libur"
                } elseif ($shiftType === 'Reguler' && $dateLoop->isWeekend()) {
                    // Cek master mahasiswa, jika tidak ada izin weekend_aktif, berarti libur
                    if (!$mhs->weekend_aktif) {
                        $isHoliday = true;
                    }
                }

                $isDispen = isset($dispenIndexed[$dateStr]);
                $absen = $absenIndexed[$dateStr] ?? null;
// Hitung performa hanya untuk hari ini ke belakang
                if ($dateLoop->lte(now())) {
                    
                    // 1. Cek Kehadiran Aktual
                    if ($absen) {
                        $status = 'HADIR'; 
                        $countHadir++;
                        if (!$isHoliday) $countTarget++; // Kalau lembur di hari libur, target nggak nambah
                        $info = Carbon::parse($absen->jam_masuk)->format('H:i') . " - " . ($absen->jam_keluar ? Carbon::parse($absen->jam_keluar)->format('H:i') : 'Belum');
                    
                    // 2. CEK LIBUR DULU! (Ini yang memblokir dispen di hari libur)
                    } elseif ($isHoliday) {
                        $status = 'LIBUR';
                        $info = 'Hari Libur / Akhir Pekan'; // Alfa, Dispen, dan Target TIDAK bertambah
                    
                    // 3. Baru Cek Dispensasi
                    } elseif ($isDispen) {
                        $status = 'DISPEN'; 
                        $countDispen++;
                        $countTarget++; // Pasti nambah target karena ini bukan hari libur
                        $info = 'Izin / Dispensasi';
                    
                    // 4. Kalau gak ada absen, gak libur, gak izin = ALFA
                    //    KECUALI hari ini: hari berjalan belum berakhir, jangan langsung dihitung Alfa
                    //    (mahasiswa masih punya kesempatan tap sampai jam pulang).
                    } elseif ($dateLoop->isToday()) {
                        $status = 'BELUM';
                        $info = 'Belum absen (hari ini)';
                    } else {
                        $status = 'ALFA';
                        $countAlfa++;
                        $countTarget++;
                        $info = '-';
                    }

                } else {
                    $status = 'FUTURE';
                    $info = '-';
                }

                $logs[] = [
                    'date_obj' => $dateLoop,
                    'date_display' => $dateLoop->isoFormat('D MMM YYYY'),
                    'status' => $status,
                    'info' => $info
                ];
            }

            // Statistik SATU SUMBER dari model (scoped ke ruangan ini) agar
            // angka & bobot (terlambat 90%, hari ini belum=bukan alpha) konsisten
            // dengan dashboard mahasiswa & sertifikat.
            $scopeDates = array_keys($assignedDates);
            if ($filter != 'semua') {
                $scopeDates = array_values(array_filter($scopeDates, function ($d) use ($startDate, $endDate) {
                    return $d >= $startDate && $d <= $endDate;
                }));
            }
            $resModel = $mhs->hitungKehadiran($scopeDates);
            $sm = $resModel['stat'];
            $mhs->stat_hadir  = $sm['hadir_fisik'];
            $mhs->stat_alfa   = $sm['alpha'];
            $mhs->stat_dispen = $sm['dispensasi_biasa'] + $sm['dispensasi_terlambat'];
            $mhs->stat_lupa   = $sm['lupa_pulang'] ?? 0;
            $mhs->stat_target = $sm['target_sekarang'];
            $mhs->stat_persen = $sm['target_sekarang'] > 0 ? round(min($sm['hadir'] / $sm['target_sekarang'] * 100, 100), 1) : 0;
            
            $mhs->timeline_logs = collect($logs)->filter(function($l) {
                return in_array($l['status'], ['HADIR', 'ALFA', 'DISPEN']);
            })->sortByDesc('date_obj')->values();
            
            if($countHadir > 0) $globalHadir++;

            // Status Badge Operasional Hari Ini
            $todayStr = now()->format('Y-m-d');
            $todayLog = collect($logs)->first(function($l) use ($todayStr) {
                return $l['date_obj']->format('Y-m-d') === $todayStr;
            });
            
            if ($todayLog) {
                if ($todayLog['status'] == 'HADIR') {
                    $mhs->status_badge = (strpos($todayLog['info'], 'Belum') !== false ? 'Masuk' : 'Selesai');
                } elseif ($todayLog['status'] == 'DISPEN') {
                    $mhs->status_badge = 'Izin/Dispensasi';
                } elseif ($todayLog['status'] == 'LIBUR') {
                    $mhs->status_badge = 'Libur';
                } else {
                    $mhs->status_badge = 'Belum Hadir';
                }
            } else {
                $mhs->status_badge = 'Bukan Jadwal';
            }

            $processedData->push($mhs);
        }
        // =================================================================
        // FILTERING & SORTING MAHASISWA YANG LEBIH AKURAT
        // =================================================================
        
        // 1. Analisis & Tempelkan Status Prioritas ke Tiap Mahasiswa
        $processedData->transform(function($mhs) use ($ruangan) {
            // A. Cek Status Penilaian di Ruangan Ini
            $nilaiJson = $mhs->nilai_ruangan_json ?? [];
            if (is_string($nilaiJson)) {
                $nilaiJson = json_decode($nilaiJson, true) ?? [];
            }
            $mhs->is_graded_here = array_key_exists($ruangan->id, $nilaiJson) && $nilaiJson[$ruangan->id] !== null;

            // B. Cek Status Aktif Magang
            $endDate = $mhs->tanggal_berakhir ? \Carbon\Carbon::parse($mhs->tanggal_berakhir)->startOfDay() : now()->startOfDay();
            $today = now()->startOfDay();
            
            // True jika hari ini masih kurang dari atau sama dengan tanggal berakhir
            $mhs->is_active_now = $today->lte($endDate);

            // C. Tentukan Skala Prioritas (Tinggi ke Rendah)
            if ($mhs->is_active_now && !$mhs->is_graded_here) {
                $mhs->sort_priority = 4; // PALING ATAS: Sedang magang & Belum ada nilai (URGENT)
            } elseif ($mhs->is_active_now && $mhs->is_graded_here) {
                $mhs->sort_priority = 3; // URUTAN 2: Sedang magang & Sudah ada nilai
            } elseif (!$mhs->is_active_now && !$mhs->is_graded_here) {
                $mhs->sort_priority = 2; // URUTAN 3: Selesai magang & Belum ada nilai (HARUS DINILAI)
            } else {
                $mhs->sort_priority = 1; // PALING BAWAH: Selesai magang & Sudah dinilai
            }

            return $mhs;
        });

        // 2. Filter: Sembunyikan LANGSUNG semua mahasiswa yang SUDAH DINILAI
        // $filteredData = $processedData->filter(function($mhs) {
        //     // Jika sudah dinilai di ruangan ini, sembunyikan!
        //     if ($mhs->is_graded_here) {
        //         return false; 
        //     }
        //     return true; // Tampilkan sisanya (yang BELUM dinilai, baik aktif maupun non-aktif)
        // });

        // 3. Urutkan Mutlak Berdasarkan Atribut Prioritas 
        // (Urutannya nanti otomatis: Aktif & Belum Dinilai di atas, Non-Aktif & Belum Dinilai di bawahnya)
        // $sortedData = $filteredData->sortByDesc('sort_priority')->values();// // // // // // // // 
// 2. (Dihapus) Agar mahasiswa yang sudah selesai magang & sudah dinilai tetap tampil
// 2. Filter: Sembunyikan LANGSUNG jika sudah selesai magang DAN sudah dinilai
        $filteredData = $processedData->filter(function($mhs) {
            // Sembunyikan prioritas 1 seketika
            if ($mhs->sort_priority === 1) {
                return false; 
            }
            return true; // Tampilkan sisanya
        });

        // 3. Urutkan Mutlak Berdasarkan Atribut Prioritas (4, 3, 2, lalu 1)
        $sortedData = $filteredData->sortByDesc('sort_priority')->values();
        // 3. Urutkan Mutlak Berdasarkan Atribut Prioritas (4, 3, 2, lalu 1)
        // $sortedData = $processedData->sortByDesc('sort_priority')->values();
        // Target Global Stat
        $targetGlobal = ($filter != 'semua') ? collect(CarbonPeriod::create($startDate, $endDate))->filter(function($d) {
            return !$d->isWeekend();
        })->count() : '-';

        // 4. Manual Pagination
        $total = $sortedData->count();
        $currentItems = $sortedData->forPage($page, $perPage);

        $paginatedMahasiswas = new LengthAwarePaginator(
            $currentItems, $total, $perPage, $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Dispensasi menunggu persetujuan untuk mahasiswa di ruangan ini (semua, tanpa pagination)
        $mahasiswaIdsRuangan = Mahasiswa::where(function ($q) use ($ruangan) {
                $q->where('ruangan_id', $ruangan->id)
                  ->orWhereHas('roomSequences', function ($sq) use ($ruangan) {
                      $sq->where('ruangan_id', $ruangan->id);
                  })
                  ->orWhereHas('shiftSchedules', function ($sq) use ($ruangan) {
                      $sq->where('ruangan_id', $ruangan->id);
                  });
            })->pluck('id');

        $pendingDispensasi = \App\Models\Dispensasi::with('mahasiswa')
            ->whereIn('mahasiswa_id', $mahasiswaIdsRuangan)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ruangan_dashboard.index', [
            'ruangan' => $ruangan,
            'mahasiswas' => $paginatedMahasiswas,
            'pendingDispensasi' => $pendingDispensasi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filter' => $filter,
            'totalMahasiswa' => $total,
            'hadirCountGlobal' => $globalHadir,
            'targetGlobalFull' => $targetGlobal,
            'indikators' => $this->indikatorRuangan($ruangan->id), // indikator aktif untuk form evaluasi
            'exportData' => []
        ]);
    }

    /**
     * Ambil indikator penilaian ruangan. Bila belum ada, buat 5 default dulu.
     * $aktifSaja true -> hanya yang aktif (untuk form evaluasi).
     */
    private function indikatorRuangan($ruanganId, $aktifSaja = true)
    {
        $items = \App\Models\IndikatorPenilaian::where('ruangan_id', $ruanganId)
            ->orderBy('urutan')->orderBy('id')->get();

        if ($items->isEmpty()) {
            $defaults = [
                ['Sikap & Kedisiplinan', 'Kedisiplinan Waktu'],
                ['Sikap & Kedisiplinan', 'Komunikasi & Etika'],
                ['Sikap & Kedisiplinan', 'Kepatuhan SOP/PPI'],
                ['Keahlian & Kompetensi', 'Pemahaman Teoritis'],
                ['Keahlian & Kompetensi', 'Keterampilan Tindakan'],
            ];
            $u = 0;
            foreach ($defaults as [$kat, $nama]) {
                \App\Models\IndikatorPenilaian::create(['ruangan_id' => $ruanganId, 'nama' => $nama, 'kategori' => $kat, 'urutan' => $u++, 'aktif' => true]);
            }
            $items = \App\Models\IndikatorPenilaian::where('ruangan_id', $ruanganId)
                ->orderBy('urutan')->orderBy('id')->get();
        }

        return $aktifSaja ? $items->where('aktif', true)->values() : $items;
    }

    /** Halaman setup indikator penilaian ruangan. */
    public function setupPenilaian()
    {
        if (Auth::user()->role !== 'ruangan') abort(403, 'Akses Ditolak.');
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        if (!$ruangan) return abort(404, 'Akun Ruangan belum disetting.');

        $this->indikatorRuangan($ruangan->id); // pastikan default ada
        $indikators = \App\Models\IndikatorPenilaian::where('ruangan_id', $ruangan->id)
            ->orderBy('urutan')->orderBy('id')->get();

        $syncAction      = route('kepala_ruangan.penilaian.sync');
        $backUrl         = route('kepala_ruangan.dashboard');
        $ruanganSelector = null;            // Karu tidak perlu memilih ruangan
        $ruanganId       = $ruangan->id;

        return view('ruangan_dashboard.setup_penilaian', compact('ruangan', 'indikators', 'syncAction', 'backUrl', 'ruanganSelector', 'ruanganId'));
    }

    /** Simpan (sinkron) daftar indikator penilaian ruangan. */
    public function syncPenilaian(Request $request)
    {
        if (Auth::user()->role !== 'ruangan') abort(403, 'Akses Ditolak.');
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        if (!$ruangan) return abort(404);

        $rows = $request->input('indikator', []);
        $keepIds = [];
        $u = 0;
        foreach ($rows as $row) {
            $nama = trim($row['nama'] ?? '');
            if ($nama === '') continue;
            $kategori = trim($row['kategori'] ?? '') ?: null;
            if (!empty($row['id'])) {
                $ind = \App\Models\IndikatorPenilaian::where('ruangan_id', $ruangan->id)->find($row['id']);
                if ($ind) { $ind->update(['nama' => $nama, 'kategori' => $kategori, 'urutan' => $u++, 'aktif' => true]); $keepIds[] = $ind->id; }
            } else {
                $ind = \App\Models\IndikatorPenilaian::create(['ruangan_id' => $ruangan->id, 'nama' => $nama, 'kategori' => $kategori, 'urutan' => $u++, 'aktif' => true]);
                $keepIds[] = $ind->id;
            }
        }
        // Hapus indikator yang tidak ikut dikirim (dihapus Karu dari form)
        \App\Models\IndikatorPenilaian::where('ruangan_id', $ruangan->id)->whereNotIn('id', $keepIds)->delete();

        return redirect()->route('kepala_ruangan.penilaian.setup')->with('success', 'Indikator penilaian berhasil disimpan.');
    }

    /**
     * Laporan penilaian: tabel detail per mahasiswa dengan filter
     * (rentang periode / nama / instansi / prodi) + unduh CSV.
     */
    public function laporanPenilaian(Request $request)
    {
        if (Auth::user()->role !== 'ruangan') abort(403, 'Akses Ditolak.');
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        if (!$ruangan) return abort(404, 'Akun Ruangan belum disetting.');

        $indikators = $this->indikatorRuangan($ruangan->id);

        $mahasiswaIds = Mahasiswa::where(function ($qq) use ($ruangan) {
            $qq->where('ruangan_id', $ruangan->id)
               ->orWhereHas('roomSequences', function ($s) use ($ruangan) { $s->where('ruangan_id', $ruangan->id); })
               ->orWhereHas('shiftSchedules', function ($s) use ($ruangan) { $s->where('ruangan_id', $ruangan->id); });
        })->pluck('id');

        // Opsi dropdown (dari semua mahasiswa di ruangan ini)
        $allInRuangan   = Mahasiswa::with('mou')->whereIn('id', $mahasiswaIds)->get();
        $prodiOptions   = $allInRuangan->pluck('prodi')->filter()->unique()->sort()->values();
        $instansiOptions = $allInRuangan->map(function ($m) { return $m->mou->nama_universitas ?? $m->univ_asal; })->filter()->unique()->sort()->values();

        // Filter
        $search   = trim((string) $request->get('q', ''));
        $prodi    = $request->get('prodi');
        $instansi = $request->get('instansi');
        $start    = $request->get('start');
        $end      = $request->get('end');

        $q = Mahasiswa::with('mou')->whereIn('id', $mahasiswaIds);
        if ($search !== '') $q->where('nm_mahasiswa', 'like', "%{$search}%");
        if ($prodi) $q->where('prodi', $prodi);
        if ($instansi) {
            $q->where(function ($w) use ($instansi) {
                $w->where('univ_asal', $instansi)
                  ->orWhereHas('mou', function ($m) use ($instansi) { $m->where('nama_universitas', $instansi); });
            });
        }
        // Rentang: periode magang bersinggungan dengan [start, end]
        if ($start) $q->whereDate('tanggal_berakhir', '>=', $start);
        if ($end)   $q->whereDate('tanggal_mulai', '<=', $end);

        $list = $q->orderBy('nm_mahasiswa')->get();

        $rows = [];
        foreach ($list as $m) {
            $evalJson  = is_string($m->nilai_evaluasi) ? json_decode($m->nilai_evaluasi, true) : ($m->nilai_evaluasi ?? []);
            $nilaiJson = is_string($m->nilai_ruangan_json) ? json_decode($m->nilai_ruangan_json, true) : ($m->nilai_ruangan_json ?? []);
            $eval = $evalJson[$ruangan->id] ?? [];
            if (!is_array($eval)) $eval = [];

            $perInd = [];
            foreach ($indikators as $ind) $perInd[$ind->nama] = $eval[$ind->nama] ?? '';

            $rows[] = [
                'mhs'       => $m,
                'instansi'  => $m->mou->nama_universitas ?? ($m->univ_asal ?: '-'),
                'periode'   => optional($m->tanggal_mulai)->format('d/m/Y') . ' - ' . optional($m->tanggal_berakhir)->format('d/m/Y'),
                'nilai'     => $nilaiJson[$ruangan->id] ?? null,
                'indikator' => $perInd,
                'catatan'   => $eval['catatan'] ?? '',
            ];
        }

        // Unduh CSV
        if ($request->get('export') === 'csv') {
            $filename = 'Laporan_Penilaian_' . preg_replace('/\s+/', '_', $ruangan->nm_ruangan) . '_' . date('Ymd_His') . '.csv';
            return response()->stream(function () use ($rows, $indikators) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM agar Excel kenal UTF-8
                $head = ['No', 'Nama', 'Prodi', 'Instansi', 'Periode'];
                foreach ($indikators as $ind) $head[] = $ind->nama;
                $head[] = 'Nilai Akhir';
                $head[] = 'Catatan';
                fputcsv($out, $head);
                $no = 1;
                foreach ($rows as $r) {
                    $m = $r['mhs'];
                    $line = [$no++, $m->nm_mahasiswa, $m->prodi, $r['instansi'], $r['periode']];
                    foreach ($indikators as $ind) $line[] = $r['indikator'][$ind->nama];
                    $line[] = $r['nilai'];
                    $line[] = $r['catatan'];
                    fputcsv($out, $line);
                }
                fclose($out);
            }, 200, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        return view('ruangan_dashboard.laporan_penilaian', compact(
            'ruangan', 'rows', 'indikators', 'prodiOptions', 'instansiOptions',
            'search', 'prodi', 'instansi', 'start', 'end'
        ));
    }

    /**
     * Halaman khusus kelola dispensasi untuk kepala ruangan:
     * tab status + pencarian nama + filter rentang tanggal + pagination.
     */
    public function dispensasiIndex(Request $request)
    {
        if (Auth::user()->role !== 'ruangan') {
            abort(403, 'Akses Ditolak.');
        }
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        if (!$ruangan) {
            return abort(404, 'Akun Ruangan belum disetting.');
        }

        $status = $request->query('status', 'pending'); // pending | approved | rejected | all
        $q      = trim((string) $request->query('q', ''));
        $start  = $request->query('start');
        $end    = $request->query('end');

        $mahasiswaIds = Mahasiswa::where(function ($qq) use ($ruangan) {
            $qq->where('ruangan_id', $ruangan->id)
               ->orWhereHas('roomSequences', function ($s) use ($ruangan) { $s->where('ruangan_id', $ruangan->id); })
               ->orWhereHas('shiftSchedules', function ($s) use ($ruangan) { $s->where('ruangan_id', $ruangan->id); });
        })->pluck('id');

        $query = \App\Models\Dispensasi::with('mahasiswa')->whereIn('mahasiswa_id', $mahasiswaIds);

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }
        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($m) use ($q) { $m->where('nm_mahasiswa', 'like', "%{$q}%"); });
        }
        // Rentang: dispensasi yang bersinggungan dengan [start, end]
        if ($start) $query->whereDate('tanggal_selesai', '>=', $start);
        if ($end)   $query->whereDate('tanggal_mulai', '<=', $end);

        $dispensasis = $query->orderByRaw("FIELD(status,'pending','approved','rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        $base = \App\Models\Dispensasi::whereIn('mahasiswa_id', $mahasiswaIds);
        $counts = [
            'pending'  => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'rejected' => (clone $base)->where('status', 'rejected')->count(),
        ];

        return view('ruangan_dashboard.dispensasi', compact('ruangan', 'dispensasis', 'status', 'q', 'start', 'end', 'counts'));
    }

    /**
     * Kalender roster ruangan: tiap tanggal menampilkan mahasiswa + shift-nya.
     * Klik event -> biodata mahasiswa (TANPA nomor HP/WA).
     */
    public function kalender(Request $request)
    {
        if (Auth::user()->role !== 'ruangan') {
            abort(403, 'Akses Ditolak.');
        }
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        if (!$ruangan) {
            return abort(404, 'Akun Ruangan belum disetting.');
        }

        $mahasiswaIds = Mahasiswa::where(function ($qq) use ($ruangan) {
            $qq->where('ruangan_id', $ruangan->id)
               ->orWhereHas('roomSequences', function ($s) use ($ruangan) { $s->where('ruangan_id', $ruangan->id); })
               ->orWhereHas('shiftSchedules', function ($s) use ($ruangan) { $s->where('ruangan_id', $ruangan->id); });
        })->pluck('id');

        // Biodata (TANPA no_hp/WA)
        $bio = [];
        foreach (Mahasiswa::with(['mou', 'roomSequences'])->whereIn('id', $mahasiswaIds)->get() as $m) {
            $seqIni = $m->roomSequences->where('ruangan_id', $ruangan->id)->sortByDesc('start_date')->first();
            $keahlian = is_array($m->kompetensi_json) ? array_values(array_filter($m->kompetensi_json)) : [];
            $bio[$m->id] = [
                'nama'     => $m->nm_mahasiswa,
                'prodi'    => $m->prodi ?: '-',
                'instansi' => $m->mou->nama_universitas ?? ($m->univ_asal ?: '-'),
                'tipe'     => strtoupper($m->tipe_mahasiswa ?? 'MAGANG'),
                'status'   => ucfirst($m->status ?? '-'),
                'periode'  => optional($m->tanggal_mulai)->format('d/m/Y') . ' - ' . optional($m->tanggal_berakhir)->format('d/m/Y'),
                'foto'     => $m->foto_path ? asset($m->foto_path) : null,
                'keahlian' => $keahlian,
                'seq_id'   => $seqIni ? $seqIni->id : null,
                'edit_url' => route('kepala_ruangan.mahasiswa.edit', $m->id),
            ];
        }

        $warna = ['pagi' => '#0d6efd', 'siang' => '#fd7e14', 'malam' => '#6f42c1', 'reguler' => '#6610f2', 'jumat' => '#20c997'];
        $events = [];

        // Dari jadwal shift (ruangan shift)
        $liburDates = []; // [mahasiswa_id][tanggal] = true
        foreach (\App\Models\ShiftSchedule::where('ruangan_id', $ruangan->id)->whereIn('mahasiswa_id', $mahasiswaIds)->get() as $s) {
            if (strtolower($s->shift_type) === 'libur') { $liburDates[$s->mahasiswa_id][$s->tanggal] = true; continue; }
            $nama = $bio[$s->mahasiswa_id]['nama'] ?? 'Mahasiswa';
            $events[] = [
                'title' => $nama . ' • ' . $s->shift_type,
                'start' => $s->tanggal,
                'color' => $warna[strtolower($s->shift_type)] ?? '#7c1316',
                'extendedProps' => ['mid' => $s->mahasiswa_id, 'shift' => $s->shift_type],
            ];
        }

        // Ruangan non-shift: hasilkan dari rentang roomSequences (hari kerja)
        if (($ruangan->kategori ?? 'non_shift') === 'non_shift') {
            $seqs = \App\Models\RoomSequence::where('ruangan_id', $ruangan->id)->whereIn('mahasiswa_id', $mahasiswaIds)->get();
            foreach ($seqs as $seq) {
                if (!$seq->start_date || !$seq->end_date) continue;
                foreach (\Carbon\CarbonPeriod::create($seq->start_date, $seq->end_date) as $d) {
                    if ($d->isWeekend()) continue;
                    $tgl = $d->format('Y-m-d');
                    if (isset($liburDates[$seq->mahasiswa_id][$tgl])) continue;
                    $nama = $bio[$seq->mahasiswa_id]['nama'] ?? 'Mahasiswa';
                    $events[] = [
                        'title' => $nama . ' • ' . ($d->dayOfWeekIso == 5 ? 'Jumat' : 'Reguler'),
                        'start' => $tgl,
                        'color' => $d->dayOfWeekIso == 5 ? '#20c997' : '#6610f2',
                        'extendedProps' => ['mid' => $seq->mahasiswa_id, 'shift' => $d->dayOfWeekIso == 5 ? 'Jumat' : 'Reguler'],
                    ];
                }
            }
        }

        return view('ruangan_dashboard.kalender', compact('ruangan', 'events', 'bio'));
    }

    /** Apakah mahasiswa ini berada di bawah ruangan Karu ini. */
    private function karuBoleh($ruangan, $mahasiswa)
    {
        if (!$ruangan || !$mahasiswa) return false;
        if ((int) $mahasiswa->ruangan_id === (int) $ruangan->id) return true;
        if ($mahasiswa->roomSequences()->where('ruangan_id', $ruangan->id)->exists()) return true;
        if ($mahasiswa->shiftSchedules()->where('ruangan_id', $ruangan->id)->exists()) return true;
        return false;
    }

    /** Karu membuka form edit biodata mahasiswa di ruangannya (pakai view admin). */
    public function editMahasiswa($id)
    {
        if (Auth::user()->role !== 'ruangan') abort(403, 'Akses Ditolak.');
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        $mahasiswa = Mahasiswa::findOrFail($id);
        if (!$this->karuBoleh($ruangan, $mahasiswa)) abort(403, 'Mahasiswa ini bukan di ruangan Anda.');

        $ruangans = Ruangan::all();
        $mous = \App\Models\Mou::orderBy('nama_instansi', 'asc')->get();
        $updateAction = route('kepala_ruangan.mahasiswa.update', $mahasiswa->id);
        $backUrl = route('kepala_ruangan.dashboard');

        return view('mahasiswa.edit', compact('mahasiswa', 'ruangans', 'mous', 'updateAction', 'backUrl'));
    }

    /** Update biodata mahasiswa oleh Karu (scoped, tanpa utak-atik ruangan/kuota & tanpa kunci 1x edit). */
    public function updateMahasiswa(Request $request, $id)
    {
        if (Auth::user()->role !== 'ruangan') abort(403, 'Akses Ditolak.');
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);
        if (!$this->karuBoleh($ruangan, $mahasiswa)) abort(403, 'Mahasiswa ini bukan di ruangan Anda.');

        $data = $request->validate([
            'nm_mahasiswa'     => 'required|string|max:255',
            'prodi'            => 'nullable|string|max:255',
            'email'            => 'nullable|email|unique:users,email,' . ($mahasiswa->user ? $mahasiswa->user->id : 'NULL'),
            'mou_id'           => 'nullable|exists:mous,id',
            'tanggal_mulai'    => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'weekend_aktif'    => 'nullable|boolean',
            'no_hp'            => 'nullable|string|max:20',
            'status'           => 'nullable|in:aktif,nonaktif',
            'foto'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Pertahankan nilai lama jika tidak dikirim
        $data['status']           = $data['status'] ?? $mahasiswa->status;
        $data['tanggal_mulai']    = $data['tanggal_mulai'] ?? $mahasiswa->tanggal_mulai;
        $data['tanggal_berakhir'] = $data['tanggal_berakhir'] ?? $mahasiswa->tanggal_berakhir;
        $data['nm_mahasiswa']     = strtoupper($data['nm_mahasiswa']);
        if (!empty($data['prodi'])) $data['prodi'] = strtoupper($data['prodi']);
        $data['weekend_aktif']    = $request->boolean('weekend_aktif');

        // Update akun user (email & nama) bila ada
        if ($mahasiswa->user) {
            $mahasiswa->user->update([
                'email' => $data['email'] ?? $mahasiswa->user->email,
                'name'  => $data['nm_mahasiswa'],
            ]);
        }
        unset($data['email']);

        // Foto
        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto_path && \Illuminate\Support\Facades\File::exists(public_path($mahasiswa->foto_path))) {
                \Illuminate\Support\Facades\File::delete(public_path($mahasiswa->foto_path));
            }
            $file = $request->file('foto');
            $nama_file = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/pas_foto'), $nama_file);
            $data['foto_path'] = 'uploads/pas_foto/' . $nama_file;
        }
        unset($data['foto']);

        // Karu TIDAK mengubah penempatan ruangan/kuota -> abaikan ruangan_id
        unset($data['ruangan_id']);

        $mahasiswa->update($data);

        return redirect()->route('kepala_ruangan.dashboard')->with('success', 'Biodata mahasiswa berhasil diperbarui.');
    }

 public function simpanNilai(Request $request, $id)
    {
        // 1. Validasi Input (indikator dinamis per ruangan)
        $request->validate([
            'evaluasi' => 'nullable|array',
            'evaluasi.nilai.*' => 'nullable|numeric|min:0|max:100',
            'evaluasi.catatan' => 'nullable|string',
        ]);

        // 2. Cek Akses Ruangan
        $ruangan = Ruangan::where('user_id', Auth::id())->first();
        if (!$ruangan) {
            return abort(403, 'Anda tidak memiliki akses ruangan.');
        }

        $mahasiswa = Mahasiswa::findOrFail($id);

        // 3. Cek Otorisasi Mahasiswa
        $isAuthorized = ($mahasiswa->ruangan_id === $ruangan->id) ||
            $mahasiswa->roomSequences()->where('ruangan_id', $ruangan->id)->exists() ||
            $mahasiswa->shiftSchedules()->where('ruangan_id', $ruangan->id)->exists();

        if (!$isAuthorized) {
            return abort(403, 'Akses Ditolak. Mahasiswa ini tidak memiliki riwayat magang di ruangan Anda.');
        }

        // 4. Siapkan Array JSON Lama
        // Format nilai akhir (untuk sertifikat)
        $nilaiJson = is_string($mahasiswa->nilai_ruangan_json) 
            ? json_decode($mahasiswa->nilai_ruangan_json, true) 
            : ($mahasiswa->nilai_ruangan_json ?? []);
            
        // Format nilai evaluasi detail (untuk analitik admin)
        $evaluasiJson = is_string($mahasiswa->nilai_evaluasi) 
            ? json_decode($mahasiswa->nilai_evaluasi, true) 
            : ($mahasiswa->nilai_evaluasi ?? []);

        // 5. Hitung nilai akhir = RATA-RATA semua indikator yang diisi
        $evalInput  = $request->input('evaluasi', []);
        $nilaiItems = $evalInput['nilai'] ?? [];   // { "<nama indikator>": nilai }
        $catatan    = $evalInput['catatan'] ?? null;

        $numeric = [];
        foreach ($nilaiItems as $nama => $v) {
            if ($v !== null && $v !== '' && is_numeric($v)) {
                $numeric[$nama] = (float) $v;
            }
        }

        if (empty($numeric)) {
            // Tidak ada nilai terisi -> hapus evaluasi ruangan ini
            unset($nilaiJson[$ruangan->id]);
            unset($evaluasiJson[$ruangan->id]);
            $pesan = 'Evaluasi ruangan ini dihapus (tidak ada nilai terisi).';
        } else {
            $rata = round(array_sum($numeric) / count($numeric), 1);
            $nilaiJson[$ruangan->id] = $rata; // nilai akhir untuk sertifikat (rata-rata)

            $simpanEval = $numeric; // { nama: nilai }
            if ($catatan !== null && $catatan !== '') $simpanEval['catatan'] = $catatan;
            $evaluasiJson[$ruangan->id] = $simpanEval;

            $pesan = 'Evaluasi disimpan. Nilai akhir (rata-rata) = ' . $rata . '.';
        }

        // 6. Simpan Kembali ke Database
        $mahasiswa->nilai_ruangan_json = empty($nilaiJson) ? null : $nilaiJson;
        $mahasiswa->nilai_evaluasi = empty($evaluasiJson) ? null : $evaluasiJson;
        $mahasiswa->save();
        
        return back()->with('success', $pesan);
    }
}