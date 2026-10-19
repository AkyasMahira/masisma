<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\EvaluasiNarasumber;
use App\Models\EvaluasiNarasumberNilai;
use App\Models\Kegiatan;
use App\Models\KegiatanFasilitator;
use App\Models\MasterEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

/**
 * Evaluasi narasumber (fasilitator) oleh peserta di akhir pelatihan.
 * Tiap aspek (master_evaluasi jenis 'narasumber') dinilai per narasumber dengan skala
 * Kurang (45-55) / Sedang (56-75) / Baik (76-85) / Sangat Baik (86+).
 */
class EvaluasiNarasumberController extends Controller
{
    private function aspek($kegiatanId = null)
    {
        if ($kegiatanId) {
            $khusus = MasterEvaluasi::aktif()->where('jenis', 'narasumber')->where('kegiatan_id', $kegiatanId)->orderBy('urutan')->orderBy('id')->get();
            if ($khusus->count()) return $khusus;
        }
        return MasterEvaluasi::aktif()->where('jenis', 'narasumber')->whereNull('kegiatan_id')->orderBy('urutan')->orderBy('id')->get();
    }

    private function kegiatanDariToken($kegiatanId, $token)
    {
        abort_unless(hash_equals(Evaluasi::token('narasumber', $kegiatanId), (string) $token), 404, 'Link evaluasi tidak valid.');
        return Kegiatan::with(['fasilitator' => function ($q) { $q->orderBy('id'); }])->findOrFail($kegiatanId);
    }

    public static function link(Kegiatan $kegiatan)
    {
        return route('evaluasi.narasumber.form', [$kegiatan->id, Evaluasi::token('narasumber', $kegiatan->id)]);
    }

    /* ===================== PUBLIK ===================== */

    public function form($kegiatanId, $token)
    {
        $kegiatan = $this->kegiatanDariToken($kegiatanId, $token);
        return view('public.evaluasi.narasumber', [
            'kegiatan' => $kegiatan,
            'narasumber' => $kegiatan->fasilitator,
            'aspek' => $this->aspek($kegiatan->id),
            'actionUrl' => route('evaluasi.narasumber.store', [$kegiatan->id, $token]),
        ]);
    }

    public function store(Request $request, $kegiatanId, $token)
    {
        $kegiatan = $this->kegiatanDariToken($kegiatanId, $token);
        $narasumber = $kegiatan->fasilitator;
        $aspek = $this->aspek($kegiatan->id);
        abort_if($narasumber->isEmpty() || $aspek->isEmpty(), 404, 'Belum ada narasumber untuk pelatihan ini.');

        $rules = [
            'tanggal_pengisian' => 'required|date',
            'profesi' => 'required|string|max:100',
            'nama' => 'nullable|string|max:255',
            'saran' => 'nullable|string|max:5000',
        ];
        foreach ($aspek as $a) {
            foreach ($narasumber as $n) {
                $rules["nilai.{$a->id}.{$n->id}"] = 'required|integer|min:1|max:4';
            }
        }
        $request->validate($rules, [
            'nilai.*.*.required' => 'Mohon lengkapi penilaian untuk semua narasumber di setiap aspek.',
            'tanggal_pengisian.required' => 'Tanggal pengisian wajib diisi.',
            'profesi.required' => 'Profesi wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $kegiatan, $narasumber, $aspek) {
            $ev = EvaluasiNarasumber::create([
                'kegiatan_id' => $kegiatan->id,
                'nama' => $request->nama,
                'profesi' => $request->profesi,
                'tanggal_pengisian' => $request->tanggal_pengisian,
                'saran' => $request->saran,
            ]);
            foreach ($aspek as $a) {
                foreach ($narasumber as $n) {
                    EvaluasiNarasumberNilai::create([
                        'evaluasi_narasumber_id' => $ev->id,
                        'fasilitator_id' => $n->id,
                        'nama_narasumber' => $n->nama_fasilitator,
                        'master_evaluasi_id' => $a->id,
                        'aspek' => $a->pertanyaan,
                        'nilai' => (int) $request->input("nilai.{$a->id}.{$n->id}"),
                    ]);
                }
            }
        });

        return redirect()->route('evaluasi.public.terimakasih')->with('evaluasi_selesai', 'narasumber');
    }

    /* ===================== ADMIN ===================== */

    /** Rekap per narasumber x aspek untuk satu pelatihan. */
    private function rekap($kegiatanId)
    {
        $rows = EvaluasiNarasumberNilai::query()
            ->join('evaluasi_narasumber as e', 'e.id', '=', 'evaluasi_narasumber_nilai.evaluasi_narasumber_id')
            ->where('e.kegiatan_id', $kegiatanId)
            ->selectRaw('evaluasi_narasumber_nilai.nama_narasumber as nama, evaluasi_narasumber_nilai.fasilitator_id as fid,
                         evaluasi_narasumber_nilai.aspek as aspek, MIN(evaluasi_narasumber_nilai.master_evaluasi_id) as aid,
                         evaluasi_narasumber_nilai.nilai as pilihan, COUNT(*) as n')
            ->groupBy('evaluasi_narasumber_nilai.nama_narasumber', 'evaluasi_narasumber_nilai.fasilitator_id', 'evaluasi_narasumber_nilai.aspek', 'evaluasi_narasumber_nilai.nilai')
            ->get();

        // urutan aspek mengikuti master
        $urutAspek = MasterEvaluasi::where('jenis', 'narasumber')->pluck('urutan', 'pertanyaan')->all();
        $aspekList = $rows->pluck('aspek')->unique()->sortBy(function ($a) use ($urutAspek) { return $urutAspek[$a] ?? 999; })->values()->all();

        $narasumber = [];
        foreach ($rows as $r) {
            $key = $r->fid ?: $r->nama;
            if (!isset($narasumber[$key])) $narasumber[$key] = ['nama' => $r->nama, 'aspek' => [], 'total' => 0, 'jml' => 0];
            $skor = EvaluasiNarasumber::skor($r->pilihan) * $r->n;
            $a = &$narasumber[$key]['aspek'][$r->aspek];
            $a = $a ?: ['total' => 0, 'jml' => 0, 'sebaran' => [1 => 0, 2 => 0, 3 => 0, 4 => 0]];
            $a['total'] += $skor; $a['jml'] += $r->n; $a['sebaran'][(int) $r->pilihan] += $r->n;
            unset($a);
            $narasumber[$key]['total'] += $skor; $narasumber[$key]['jml'] += $r->n;
        }
        foreach ($narasumber as &$n) {
            foreach ($n['aspek'] as &$a) { $a['skor'] = $a['jml'] ? round($a['total'] / $a['jml'], 2) : null; }
            unset($a);
            $n['skor'] = $n['jml'] ? round($n['total'] / $n['jml'], 2) : null;
            $n['kategori'] = $n['skor'] !== null ? EvaluasiNarasumber::kategori($n['skor']) : '-';
        }
        unset($n);
        uasort($narasumber, function ($x, $y) { return ($y['skor'] ?? 0) <=> ($x['skor'] ?? 0); });

        // rata-rata per aspek (semua narasumber)
        $perAspek = [];
        foreach ($aspekList as $asp) {
            $t = 0; $j = 0;
            foreach ($narasumber as $n) { if (isset($n['aspek'][$asp])) { $t += $n['aspek'][$asp]['total']; $j += $n['aspek'][$asp]['jml']; } }
            $perAspek[$asp] = $j ? round($t / $j, 2) : null;
        }
        return [$narasumber, $aspekList, $perAspek];
    }

    public function index(Request $request)
    {
        // daftar pelatihan + jumlah responden
        $jumlah = EvaluasiNarasumber::selectRaw('kegiatan_id, COUNT(*) as n, MAX(created_at) as terakhir')->groupBy('kegiatan_id')->get()->keyBy('kegiatan_id');
        $daftar = Kegiatan::withCount('fasilitator')->orderBy('tanggal_mulai', 'desc')->get(['id', 'nama_kegiatan', 'tanggal_mulai']);

        $kegiatan = $request->filled('kegiatan_id') ? Kegiatan::with('fasilitator')->find($request->kegiatan_id) : null;
        $data = null;
        if ($kegiatan) {
            list($narasumber, $aspekList, $perAspek) = $this->rekap($kegiatan->id);
            $responden = EvaluasiNarasumber::where('kegiatan_id', $kegiatan->id)->orderByDesc('created_at')->get();
            $data = compact('narasumber', 'aspekList', 'perAspek', 'responden');
            $data['link'] = self::link($kegiatan);
        }
        return view('admin.evaluasi_narasumber.index', compact('daftar', 'jumlah', 'kegiatan', 'data'));
    }

    public function exportPdf($kegiatanId)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatanId);
        list($narasumber, $aspekList, $perAspek) = $this->rekap($kegiatan->id);
        $total = EvaluasiNarasumber::where('kegiatan_id', $kegiatan->id)->count();
        $saran = EvaluasiNarasumber::where('kegiatan_id', $kegiatan->id)->whereNotNull('saran')->where('saran', '!=', '')->pluck('saran');
        $pdf = PDF::loadView('admin.evaluasi_narasumber.pdf', compact('kegiatan', 'narasumber', 'aspekList', 'perAspek', 'total', 'saran'))
            ->setPaper('a4', count($narasumber) > 4 ? 'landscape' : 'portrait');
        return $pdf->download('evaluasi-narasumber-' . $kegiatan->id . '-' . date('Ymd') . '.pdf');
    }

    public function exportCsv($kegiatanId)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatanId);
        $data = EvaluasiNarasumber::with('nilai')->where('kegiatan_id', $kegiatan->id)->orderBy('created_at')->get();
        $kolom = EvaluasiNarasumberNilai::whereIn('evaluasi_narasumber_id', $data->pluck('id'))
            ->select('nama_narasumber', 'aspek')->distinct()->get()->map(function ($r) { return $r->nama_narasumber . ' | ' . $r->aspek; })->values()->all();

        $filename = 'evaluasi-narasumber-' . $kegiatan->id . '-' . date('Ymd-His') . '.csv';
        return response()->stream(function () use ($data, $kolom) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, array_merge(['No', 'Tanggal Pengisian', 'Profesi', 'Nama'], $kolom, ['Saran']));
            foreach ($data as $i => $ev) {
                $map = [];
                foreach ($ev->nilai as $v) $map[$v->nama_narasumber . ' | ' . $v->aspek] = EvaluasiNarasumber::SKALA[$v->nilai][0] ?? $v->nilai;
                $line = [$i + 1, optional($ev->tanggal_pengisian)->format('Y-m-d'), $ev->profesi, $ev->nama];
                foreach ($kolom as $k) $line[] = $map[$k] ?? '';
                $line[] = $ev->saran;
                fputcsv($out, $line);
            }
            fclose($out);
        }, 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => "attachment; filename=\"$filename\""]);
    }

    public function destroy($id)
    {
        $ev = EvaluasiNarasumber::findOrFail($id);
        $kid = $ev->kegiatan_id;
        $ev->delete();
        return redirect()->route('admin.evaluasi_narasumber.index', ['kegiatan_id' => $kid])->with('success', 'Tanggapan dihapus.');
    }
}
