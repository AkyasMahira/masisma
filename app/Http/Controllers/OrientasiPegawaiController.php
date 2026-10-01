<?php

namespace App\Http\Controllers;

use App\Models\OrientasiPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class OrientasiPegawaiController extends Controller
{
    private $baseUrl = 'http://192.168.244.104/masdayat/api/v1';
    private $headers = [
        'X-API-KEY' => 'MASDAYAT749',
        'Accept'    => 'application/json',
    ];

    public function index(Request $request)
    {
        $error = null;
        $apiData = [];
        try {
            $response = Http::withHeaders($this->headers)->timeout(15)->get("{$this->baseUrl}/pegawai");
            if ($response->successful()) {
                $body = $response->json();
                $apiData = is_array($body['data'] ?? null) ? $body['data'] : [];
            } else {
                Log::error('Orientasi Pegawai API Error: ' . $response->body());
                $error = 'Koneksi ke API SDM gagal atau API Key salah.';
            }
        } catch (\Exception $e) {
            Log::error('Orientasi Pegawai API Exception: ' . $e->getMessage());
            $error = 'Tidak dapat terhubung ke server SDM (IT). Coba lagi nanti.';
        }

        // Status orientasi lokal, dipetakan per NIP
        $statusMap = OrientasiPegawai::get()->keyBy(function ($o) {
            return (string) $o->nip;
        });

        $collection = collect($apiData)->map(function ($item) use ($statusMap) {
            $nip = (!empty($item['nip']) && $item['nip'] !== '-') ? $item['nip'] : ($item['nik'] ?? '-');
            $rec = $statusMap->get((string) $nip);
            return (object) [
                'id'      => $item['id'] ?? null,
                'nama'    => $item['nama'] ?? '-',
                'nip'     => $nip,
                'unit'    => $item['unit_sekarang'] ?? '-',
                'status_pegawai' => $item['status_kepegawaian'] ?? '-',
                'orientasi'      => $rec,                       // null = belum
                'sudah'          => $rec && in_array($rec->status, ['sudah', 'lulus']),
            ];
        });

        // Filter
        if ($request->filled('search')) {
            $q = strtolower($request->search);
            $collection = $collection->filter(function ($p) use ($q) {
                return strpos(strtolower($p->nama), $q) !== false || strpos(strtolower((string) $p->nip), $q) !== false;
            });
        }
        if ($request->filled('status')) {
            $collection = $collection->filter(function ($p) use ($request) {
                return $request->status === 'sudah' ? $p->sudah : !$p->sudah;
            });
        }
        $collection = $collection->values();

        // Statistik (dari seluruh data API sebelum filter status, tapi setelah search)
        $total = $collection->count();
        $sudah = $collection->where('sudah', true)->count();
        $belum = $total - $sudah;

        // Paginate manual
        $perPage = 15;
        $page = Paginator::resolveCurrentPage();
        $items = $collection->slice(($page - 1) * $perPage, $perPage)->values()->all();
        $pegawai = new LengthAwarePaginator($items, $collection->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(), 'query' => $request->query(),
        ]);

        return view('admin.orientasi_pegawai.index', compact('pegawai', 'total', 'sudah', 'belum', 'error'));
    }

    /** Tandai / catat orientasi seorang pegawai (key: nip). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nip'             => 'required|string',
            'nama'            => 'nullable|string',
            'unit'            => 'nullable|string',
            'pegawai_id'      => 'nullable|string',
            'status'          => 'required|in:sudah,lulus',
            'pre_test_score'  => 'nullable|integer|min:0|max:100',
            'post_test_score' => 'nullable|integer|min:0|max:100',
            'tahun'           => 'nullable|integer',
            'keterangan'      => 'nullable|string',
        ]);
        $data['tahun'] = $data['tahun'] ?? now()->year;

        OrientasiPegawai::updateOrCreate(['nip' => $data['nip']], $data);

        return back()->with('success', 'Status orientasi pegawai disimpan.');
    }

    /** Batalkan status (kembalikan ke BELUM) */
    public function destroy($nip)
    {
        OrientasiPegawai::where('nip', $nip)->delete();
        return back()->with('success', 'Status orientasi pegawai dikembalikan ke "Belum".');
    }
}
