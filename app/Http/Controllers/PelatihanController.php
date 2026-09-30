<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use App\Models\Kegiatan; // Memanggil model Kegiatan untuk saran pelatihan

class PelatihanController extends Controller
{
    private $baseUrl = 'http://192.168.244.104/masdayat/api/v1';
    private $headers = [
        'X-API-KEY' => 'MASDAYAT749',
        'Accept'    => 'application/json'
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        try {
            $response = Http::withHeaders($this->headers)->get("{$this->baseUrl}/pegawai");
            
            if ($response->failed()) {
                Log::error('API Error: ' . $response->body());
                return view('pelatihan.index', ['pelatihans' => new LengthAwarePaginator([], 0, 10)])
                    ->with('error', 'Koneksi ke API gagal atau API Key salah.');
            }

            $body = $response->json();
            $apiData = $body['data'] ?? []; 
            
            if (!is_array($apiData)) {
                $apiData = [];
            }

            $collection = collect($apiData);

            // Filter Pencarian Lokal
            if ($request->filled('search')) {
                $collection = $collection->filter(function($item) use ($request) {
                    return isset($item['nama']) && stripos($item['nama'], $request->search) !== false;
                });
            }
            if ($request->filled('unit')) {
                $collection = $collection->filter(function($item) use ($request) {
                    return isset($item['unit_sekarang']) && stripos($item['unit_sekarang'], $request->unit) !== false;
                });
            }

            $currentYear = date('Y');

            // Ambil saran pelatihan (3 kegiatan terbaru dari database lokal)
            // Bisa disesuaikan kondisinya (misal hanya yang aktif)
            $saranPelatihan = Kegiatan::select('id', 'nama_kegiatan', 'jpl')
                                ->orderBy('id', 'desc')
                                ->take(3)
                                ->get();

            // Mapping Data & Hitung JPL
            $mappedData = $collection->map(function($item) use ($currentYear) {
                $pelatihanList = [];
                $jplTahunIni = 0;
                
                if (isset($item['pelatihan']) && is_array($item['pelatihan'])) {
                    foreach ($item['pelatihan'] as $tahunGrup => $pelatihans) {
                        if (is_array($pelatihans)) {
                            foreach ($pelatihans as $p) {
                                if (is_array($p)) {
                                    $jpl = (isset($p['jpl']) && is_numeric($p['jpl'])) ? (int) $p['jpl'] : 0;
                                    
                                    $pelatihanList[] = [
                                        'no_index' => $p['no'] ?? 0,
                                        'nama'     => $p['nama_pelatihan'] ?? $p['pelatihan'] ?? '-',
                                        'jpl'      => $jpl,
                                        'tahun'    => $tahunGrup,
                                        'file'     => null 
                                    ];

                                    // Akumulasi JPL khusus tahun berjalan
                                    if ((string)$tahunGrup === (string)$currentYear) {
                                        $jplTahunIni += $jpl;
                                    }
                                }
                            }
                        }
                    }
                }

                // Grouping riwayat per tahun agar rapi saat dikirim ke modal accordion
                $riwayatGrouped = collect($pelatihanList)->groupBy('tahun')->sortKeysDesc()->toArray();

                return (object) [
                    'id'               => $item['id'] ?? null,
                    'nama'             => $item['nama'] ?? '-',
                    'email'            => $item['email'] ?? '-',
                    'nip'              => (!empty($item['nip']) && $item['nip'] !== '-') ? $item['nip'] : ($item['nik'] ?? '-'),
                    'status_pegawai'   => $item['status_kepegawaian'] ?? '-',
                    'unit'             => $item['unit_sekarang'] ?? '-',
                    'pelatihan_dasar'  => $pelatihanList, // Dipertahankan untuk method show/kompatibilitas
                    'riwayat_grouped'  => $riwayatGrouped, // Untuk tampilan accordion
                    'jpl_tahun_ini'    => $jplTahunIni,
                    'target_terpenuhi' => $jplTahunIni >= 20,
                ];
            })->values();

            $perPage = 10;
            $currentPage = Paginator::resolveCurrentPage();
            $currentItems = $mappedData->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();
            
            $pelatihans = new LengthAwarePaginator(
                $currentItems, 
                $mappedData->count(), 
                $perPage, 
                $currentPage, 
                ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
            );

            return view('pelatihan.index', compact('pelatihans', 'currentYear', 'saranPelatihan'));

        } catch (\Exception $e) {
            Log::error('PelatihanController Index Error: ' . $e->getMessage());
            return view('pelatihan.index', ['pelatihans' => new LengthAwarePaginator([], 0, 10)])
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function showPelatihanAPI($id, $index)
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get("{$this->baseUrl}/pegawai/{$id}/pelatihan/{$index}");
                
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal mengambil data pelatihan dari API.'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $response = Http::withHeaders($this->headers)->get("{$this->baseUrl}/pegawai");

            if ($response->failed()) {
                Log::error('API Error (show): ' . $response->body());
                return redirect()->route('pelatihan.index')->with('error', 'Koneksi ke API gagal atau API Key salah.');
            }

            $body = $response->json();
            $apiData = $body['data'] ?? [];
            if (!is_array($apiData)) {
                $apiData = [];
            }

            // Cari item yang id-nya cocok
            $item = collect($apiData)->first(function ($row) use ($id) {
                return isset($row['id']) && (string) $row['id'] === (string) $id;
            });

            if (!$item) {
                return redirect()->route('pelatihan.index')->with('error', 'Data pegawai dengan ID tersebut tidak ditemukan.');
            }

            $dasar = [];
            if (isset($item['pelatihan']) && is_array($item['pelatihan'])) {
                foreach ($item['pelatihan'] as $tahunGrup => $pelatihans) {
                    if (is_array($pelatihans)) {
                        foreach ($pelatihans as $p) {
                            if (is_array($p)) {
                                $dasar[] = [
                                    'nama'  => $p['nama_pelatihan'] ?? $p['pelatihan'] ?? '-',
                                    'jpl'   => is_numeric($p['jpl'] ?? null) ? (int) $p['jpl'] : 0,
                                    'tahun' => $tahunGrup,
                                    'file'  => $p['file'] ?? null,
                                ];
                            }
                        }
                    }
                }
            }

            $currentYear = date('Y');
            $totalJpl = collect($dasar)
                ->filter(function ($p) use ($currentYear) {
                    return (string) $p['tahun'] === (string) $currentYear;
                })
                ->sum('jpl');

            $pelatihan = (object) [
                'id'                               => $item['id'] ?? $id,
                'nama'                             => $item['nama'] ?? '-',
                'nik'                              => $item['nik'] ?? null,
                'jabatan'                          => $item['jabatan'] ?? null,
                'bidang'                           => $item['bidang'] ?? '-',
                'unit'                             => $item['unit_sekarang'] ?? '-',
                'status_pegawai'                   => $item['status_kepegawaian'] ?? '-',
                'nip'                              => (!empty($item['nip']) && $item['nip'] !== '-') ? $item['nip'] : ($item['nik'] ?? '-'),
                'nirp'                             => $item['nirp'] ?? null,
                'lms_status'                       => $item['lms_status'] ?? 'Tidak',
                'lms_email'                        => $item['lms_email'] ?? null,
                'pelatihan_dasar'                  => $dasar,
                'pelatihan_peningkatan_kompetensi' => [],
            ];

            return view('pelatihan.show', compact('pelatihan', 'totalJpl', 'currentYear'));

        } catch (\Exception $e) {
            Log::error('PelatihanController Show Error: ' . $e->getMessage());
            return redirect()->route('pelatihan.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function exportData()
    {
        try {
            $response = Http::withHeaders($this->headers)->get("{$this->baseUrl}/pegawai");

            if ($response->failed()) {
                Log::error('Export API Error: ' . $response->body());
                return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data dari API.'], 500);
            }

            $body = $response->json();
            $apiData = $body['data'] ?? [];
            if (!is_array($apiData)) {
                $apiData = [];
            }

            $rows = [];

            foreach ($apiData as $item) {
                $nama   = $item['nama'] ?? '-';
                $email  = $item['email'] ?? '-';
                $nip    = (!empty($item['nip']) && $item['nip'] !== '-') ? $item['nip'] : ($item['nik'] ?? '-');
                $status = $item['status_kepegawaian'] ?? '-';
                $unit   = $item['unit_sekarang'] ?? '-';

                $adaPelatihan = false;

                if (isset($item['pelatihan']) && is_array($item['pelatihan'])) {
                    foreach ($item['pelatihan'] as $tahunGrup => $pelatihans) {
                        if (is_array($pelatihans)) {
                            foreach ($pelatihans as $p) {
                                if (is_array($p)) {
                                    $adaPelatihan = true;
                                    $rows[] = [
                                        'nama'      => $nama,
                                        'email'     => $email,
                                        'nip'       => $nip,
                                        'status'    => $status,
                                        'unit'      => $unit,
                                        'tahun'     => $tahunGrup,
                                        'pelatihan' => $p['nama_pelatihan'] ?? $p['pelatihan'] ?? '-',
                                        'jpl'       => is_numeric($p['jpl'] ?? null) ? (int) $p['jpl'] : 0,
                                    ];
                                }
                            }
                        }
                    }
                }

                if (!$adaPelatihan) {
                    $rows[] = [
                        'nama'      => $nama,
                        'email'     => $email,
                        'nip'       => $nip,
                        'status'    => $status,
                        'unit'      => $unit,
                        'tahun'     => '-',
                        'pelatihan' => '-',
                        'jpl'       => 0,
                    ];
                }
            }

            return response()->json(['status' => 'success', 'data' => $rows]);

        } catch (\Exception $e) {
            Log::error('PelatihanController ExportData Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function storePelatihan(Request $request, $id)
    {
        $request->validate([
            'pelatihan'       => 'required|string',
            'jpl'             => 'required|numeric',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date'
        ]);

        try {
            $response = Http::withHeaders($this->headers)
                ->post("{$this->baseUrl}/pegawai/{$id}/pelatihan", [
                    'pelatihan'       => $request->pelatihan,
                    'jpl'             => $request->jpl,
                    'tanggal_mulai'   => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai
                ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Pelatihan berhasil ditambahkan via API.');
            }

            return redirect()->back()->with('error', 'Gagal menambahkan pelatihan: ' . $response->json('message', 'Unknown Error'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatePelatihan(Request $request, $id, $index)
    {
        $request->validate([
            'pelatihan'       => 'required|string',
            'jpl'             => 'required|numeric',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date'
        ]);

        try {
            $response = Http::withHeaders($this->headers)
                ->put("{$this->baseUrl}/pegawai/{$id}/pelatihan/{$index}", [
                    'pelatihan'       => $request->pelatihan,
                    'jpl'             => $request->jpl,
                    'tanggal_mulai'   => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai
                ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Pelatihan berhasil diperbarui via API.');
            }

            return redirect()->back()->with('error', 'Gagal memperbarui pelatihan: ' . $response->json('message', 'Unknown Error'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyPelatihan($id, $index)
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->delete("{$this->baseUrl}/pegawai/{$id}/pelatihan/{$index}");

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Pelatihan berhasil dihapus via API.');
            }

            return redirect()->back()->with('error', 'Gagal menghapus pelatihan: ' . $response->json('message', 'Unknown Error'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}