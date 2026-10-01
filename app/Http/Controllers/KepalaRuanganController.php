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
        $query = Mahasiswa::with(['roomSequences', 'shiftSchedules', 'absensis', 'dispensasis'])
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
            'exportData' => []
        ]);
    }
 public function simpanNilai(Request $request, $id)
    {
        // 1. Validasi Input (Termasuk Array Evaluasi Baru)
        $request->validate([
            'nilai_karu' => 'nullable|numeric|min:0|max:100',
            'evaluasi' => 'nullable|array',
            'evaluasi.sikap.kedisiplinan' => 'nullable|numeric|min:0|max:100',
            'evaluasi.sikap.komunikasi' => 'nullable|numeric|min:0|max:100',
            'evaluasi.sikap.kepatuhan' => 'nullable|numeric|min:0|max:100',
            'evaluasi.keahlian.pemahaman' => 'nullable|numeric|min:0|max:100',
            'evaluasi.keahlian.keterampilan' => 'nullable|numeric|min:0|max:100',
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

        // 5. Eksekusi Simpan atau Hapus
        if (is_null($request->nilai_karu)) {
            // Jika input dikosongkan, hapus data nilai dari ruangan ini
            unset($nilaiJson[$ruangan->id]);
            unset($evaluasiJson[$ruangan->id]);
            $pesan = 'Data evaluasi dan nilai berhasil dihapus!';
        } else {
            // Set/Update nilai utama
            $nilaiJson[$ruangan->id] = (float) $request->nilai_karu;
            
            // Set/Update data evaluasi detail jika dikirim dari form
            if ($request->has('evaluasi')) {
                // Memastikan nilai string angka di-casting jadi int/float agar seragam
                $evalArray = $request->evaluasi;
                array_walk_recursive($evalArray, function(&$item, $key) {
                    if (is_numeric($item) && $key !== 'catatan') {
                        $item = (float) $item;
                    }
                });
                
                $evaluasiJson[$ruangan->id] = $evalArray;
            }
            
            $pesan = 'Evaluasi dan nilai berhasil disimpan!';
        }

        // 6. Simpan Kembali ke Database
        $mahasiswa->nilai_ruangan_json = empty($nilaiJson) ? null : $nilaiJson;
        $mahasiswa->nilai_evaluasi = empty($evaluasiJson) ? null : $evaluasiJson;
        $mahasiswa->save();
        
        return back()->with('success', $pesan);
    }
}