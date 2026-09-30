<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Kegiatan; // <-- Menggunakan model Kegiatan yang sebenarnya

class RekomendasiPelatihanController extends Controller
{
    // Menggunakan konsep Kamus Kata Kunci (Disesuaikan dengan pertanyaan U1 - U11 dari Database Survei)
    protected $kamusTNA = [
        'U1'  => "syarat administrasi standar layanan sda bpjs rujukan persyaratan",
        'U2'  => "sop prosedur tata laksana alur pendaftaran birokrasi kemudahan regulasi",
        'U3'  => "waktu triage alur lean efisiensi antrian response time cepat tanggap tunggu",
        'U4'  => "tarif biaya kasir asuransi klaim billing wajar gratis",
        'U5'  => "mutu standar pelayanan produk output hasil asuhan",
        'U6'  => "klinis asuhan bcls ppgd btcls medis keperawatan kebidanan teknis keterampilan nakes dokter perawat",
        'U7'  => "service excellence komunikasi pelayanan prima sikap empati hospitality etiket ramah sopan santun",
        'U8'  => "komplain handling complaint public relations humas resolusi konflik advokasi masalah pengaduan",
        'U9'  => "fasilitas sarpras sarana prasarana alat medis kebersihan kenyamanan gedung parkir",
        'U10' => "informasi publikasi edukasi ketersediaan transparan transparansi",
        'U11' => "etika integritas anti korupsi budaya kerja gratifikasi disiplin hukum sdm motivasi"
    ];

    public function generateRekomendasi(Request $request)
    {
        $apiUrl = 'http://103.139.47.173/slgsurvei/tna-ikm';
        
        try {
            $queryParams = [];
            if ($request->filled('start_date')) $queryParams['start_date'] = $request->start_date;
            if ($request->filled('end_date')) $queryParams['end_date'] = $request->end_date;

            $response = Http::withHeaders([
                'X-API-KEY' => 'sindikat-rsud-slg-2026'
            ])->timeout(15)->get($apiUrl, $queryParams);
            
            if (!$response->successful()) {
                Log::error('API Survei Error: HTTP Status ' . $response->status());
                return back()->with('error', 'Gagal terhubung ke API SLG Survei.');
            }

            $skorUnsur = $response->json()['data'] ?? [];

            if (empty($skorUnsur)) {
                return back()->with('info', 'Data survei kosong untuk periode tersebut.');
            }
        } catch (\Exception $e) {
            Log::error('Koneksi API Error: ' . $e->getMessage());
            return back()->with('error', 'Koneksi API Error. Pastikan server survei menyala.');
        }

        // AMBIL DATA SELURUH KEGIATAN YANG ADA (BESERTA RELASI TUJUAN & KOMPETENSI)
        $listPelatihan = Kegiatan::with(['tujuan', 'kompetensi'])->get();
        
        $rekomendasiPerUnit = [];
        $semuaGap = []; 
        $rataUnit = []; 

        // 1. Grouping & Formatting Nama Unit
        $groupedByUnit = collect($skorUnsur)->groupBy(function ($item) {
            $rawUnit = $item['nama_unit'] ?? ($item['kode_unit'] ?? 'Lainnya');
            return ucwords(str_replace('_', ' ', $rawUnit)); 
        });

        // 2. Processing Data (Hitung Gap & Cari Rekomendasi Pelatihan)
        foreach ($groupedByUnit as $unitName => $items) {
            
            $avgUnit = collect($items)->avg('avg_score');
            $rataUnit[$unitName] = round($avgUnit, 2);

            // Ambil 3 unsur dengan nilai terendah (yang paling butuh perbaikan)
            $unsurPrioritas = collect($items)
                ->filter(function ($item) {
                    return array_key_exists($item['question_code'], $this->kamusTNA);
                })
                ->sortBy('avg_score')
                ->take(3);

            $unitData = [];

            foreach ($unsurPrioritas as $unsur) {
                $code  = $unsur['question_code'];
                $kamusTarget = $this->kamusTNA[$code];
                $pelatihanCocok = [];

                $semuaGap[] = [
                    'label' => $unitName . ' (' . $code . ')',
                    'skor'  => $unsur['avg_score']
                ];

                // LOOPING KE SELURUH DATA KEGIATAN
                foreach ($listPelatihan as $pl) {
                    
                    // Kumpulkan semua informasi pelatihan menjadi 1 teks besar untuk di-scan
                    $teksPelatihan = $pl->nama_kegiatan . ' ' . $pl->deskripsi;
                    
                    // Tambahkan keahlian (jika ada)
                    if (is_array($pl->keahlian)) {
                        $teksPelatihan .= ' ' . implode(' ', $pl->keahlian);
                    }

                    // Tambahkan Tujuan
                    foreach ($pl->tujuan as $t) {
                        $teksPelatihan .= ' ' . $t->tujuan;
                    }

                    // Tambahkan Kompetensi Dasar & Indikator
                    foreach ($pl->kompetensi as $k) {
                        $teksPelatihan .= ' ' . $k->nama_kompetensi . ' ' . ($k->pivot->indikator_keberhasilan ?? '');
                    }
                    
                    // Hitung skor kemiripan teks menggunakan fungsi pintar di bawah
                    $matchScore = $this->hitungKemiripanTeks($kamusTarget, strtolower($teksPelatihan));

                    // Jika tingkat kecocokannya layak (threshold bisa disesuaikan, di sini > 30 poin)
                    if ($matchScore > 30) { 
                        $pelatihanCocok[] = [
                            'judul'      => $pl->nama_kegiatan,
                            'kategori'   => strtoupper($pl->jenis_kegiatan) . ' / ' . ($pl->jpl ?? 0) . ' JPL',
                            'skor_cocok' => $matchScore
                        ];
                    }
                }

                // Urutkan pelatihan dari skor kecocokan tertinggi ke terendah (Gaya PHP klasik)
                usort($pelatihanCocok, function($a, $b) {
                    return $b['skor_cocok'] <=> $a['skor_cocok'];
                });
                
                $unitData[] = [
                    'kode_unsur'          => $code,
                    'pertanyaan'          => $unsur['question_text'],
                    'skor_rata2'          => round($unsur['avg_score'], 2),
                    'kebutuhan_pelatihan' => array_slice($pelatihanCocok, 0, 3) 
                ];
            }

            if (count($unitData) > 0) {
                $rekomendasiPerUnit[$unitName] = $unitData;
            }
        }

        // --- DATA UNTUK CHART DASHBOARD ---
        $top5Global = collect($semuaGap)->sortBy('skor')->take(5);
        $chartLabels = $top5Global->pluck('label')->toArray();
        $chartScores = $top5Global->pluck('skor')->toArray();

        $unitLabels = array_keys($rataUnit);
        $unitScores = array_values($rataUnit);

        return view('admin.master_pelatihan.rekomendasi', [
            'rekomendasiPerUnit' => $rekomendasiPerUnit,
            'chartLabels'        => $chartLabels,
            'chartScores'        => $chartScores,
            'unitLabels'         => $unitLabels,
            'unitScores'         => $unitScores,
        ]);
    }

    /**
     * Fungsi Pintar Pengganti Strict Regex (Natural Word Matching)
     * Telah dioptimasi agar memproses string super panjang dari relasi dengan cepat.
     */
    private function hitungKemiripanTeks($kamusTarget, $teksPelatihan)
    {
        $targetWords = explode(' ', strtolower($kamusTarget));
        
        // Bersihkan tanda baca khusus dari string (hanya sisakan huruf & angka)
        $cleanTeksPelatihan = preg_replace('/[^a-z0-9 ]/', '', strtolower($teksPelatihan));
        
        // OPTIMASI: Hapus kata yang duplikat di deskripsi/tujuan agar pencocokan jauh lebih cepat
        $pelatihanWords = array_unique(explode(' ', $cleanTeksPelatihan));
        
        $skorTotal = 0;

        // Iterasi berdasarkan kata target TNA (Kamus)
        foreach ($targetWords as $kataKamus) {
            if (strlen($kataKamus) < 3) continue; // Abaikan kata hubung (di, ke, dari)
            
            $skorTertinggiPerKata = 0;

            foreach ($pelatihanWords as $kataPelatihan) {
                if (strlen($kataPelatihan) < 3) continue; 
                
                // Shortcut: Jika katanya sama persis, langsung beri skor 100 dan lanjut ke target berikutnya
                if ($kataPelatihan === $kataKamus) {
                    $skorTertinggiPerKata = 100;
                    break;
                }

                // Fungsi bawaan PHP: Menganalisa % kemiripan typo/penulisan (contoh: klinis vs klinik)
                similar_text($kataPelatihan, $kataKamus, $persentaseMirip);
                
                if ($persentaseMirip > $skorTertinggiPerKata) {
                    $skorTertinggiPerKata = $persentaseMirip;
                }
            }

            // Kata dianggap "berhubungan" jika tingkat kemiripannya di atas 75%.
            if ($skorTertinggiPerKata > 75) {
                $skorTotal += $skorTertinggiPerKata;
            }
        }

        return round($skorTotal);
    }
}