<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\IndikatorPenilaian;
use Illuminate\Http\Request;

class LaporanPenilaianController extends Controller
{
    /**
     * Laporan penilaian ADMIN lintas ruangan.
     * Satu baris per (mahasiswa x ruangan yang sudah dinilai).
     * Filter: ruangan / prodi / instansi / rentang / nama. Unduh CSV.
     */
    public function laporan(Request $request)
    {
        $ruanganMap = Ruangan::pluck('nm_ruangan', 'id')->toArray();

        // Opsi dropdown
        $semuaMhs = Mahasiswa::with('mou')->get();
        $prodiOptions = $semuaMhs->pluck('prodi')->filter()->unique()->sort()->values();
        $instansiOptions = $semuaMhs->map(function ($m) { return $m->mou->nama_universitas ?? $m->univ_asal; })->filter()->unique()->sort()->values();
        $ruanganOptions = $ruanganMap;

        // Filter
        $search    = trim((string) $request->get('q', ''));
        $prodi     = $request->get('prodi');
        $instansi  = $request->get('instansi');
        $ruanganId = $request->get('ruangan');
        $start     = $request->get('start');
        $end       = $request->get('end');

        $q = Mahasiswa::with('mou');
        if ($search !== '') $q->where('nm_mahasiswa', 'like', "%{$search}%");
        if ($prodi) $q->where('prodi', $prodi);
        if ($instansi) {
            $q->where(function ($w) use ($instansi) {
                $w->where('univ_asal', $instansi)
                  ->orWhereHas('mou', function ($m) use ($instansi) { $m->where('nama_universitas', $instansi); });
            });
        }
        if ($start) $q->whereDate('tanggal_berakhir', '>=', $start);
        if ($end)   $q->whereDate('tanggal_mulai', '<=', $end);

        $list = $q->orderBy('nm_mahasiswa')->get();

        // Jika difilter 1 ruangan -> tampilkan kolom indikator ruangan itu
        $indikators = collect();
        if ($ruanganId) {
            $indikators = IndikatorPenilaian::where('ruangan_id', $ruanganId)
                ->where('aktif', true)->orderBy('urutan')->orderBy('id')->get();
        }

        $rows = [];
        foreach ($list as $m) {
            $nilaiJson = is_string($m->nilai_ruangan_json) ? json_decode($m->nilai_ruangan_json, true) : ($m->nilai_ruangan_json ?? []);
            $evalJson  = is_string($m->nilai_evaluasi) ? json_decode($m->nilai_evaluasi, true) : ($m->nilai_evaluasi ?? []);
            if (!is_array($nilaiJson)) $nilaiJson = [];
            if (!is_array($evalJson)) $evalJson = [];

            foreach ($nilaiJson as $rid => $nilaiAkhir) {
                if ($ruanganId && (int) $rid !== (int) $ruanganId) continue;

                $eval = $evalJson[$rid] ?? [];
                if (!is_array($eval)) $eval = [];

                $perInd = [];
                foreach ($indikators as $ind) $perInd[$ind->nama] = $eval[$ind->nama] ?? '';

                $rows[] = [
                    'mhs'        => $m,
                    'instansi'   => $m->mou->nama_universitas ?? ($m->univ_asal ?: '-'),
                    'periode'    => optional($m->tanggal_mulai)->format('d/m/Y') . ' - ' . optional($m->tanggal_berakhir)->format('d/m/Y'),
                    'ruangan'    => $ruanganMap[$rid] ?? ('Ruang #' . $rid),
                    'nilai'      => $nilaiAkhir,
                    'indikator'  => $perInd,
                    'catatan'    => $eval['catatan'] ?? '',
                ];
            }
        }

        // Urutkan: nama lalu ruangan
        usort($rows, function ($a, $b) {
            $c = strcmp($a['mhs']->nm_mahasiswa, $b['mhs']->nm_mahasiswa);
            return $c !== 0 ? $c : strcmp($a['ruangan'], $b['ruangan']);
        });

        // Unduh CSV
        if ($request->get('export') === 'csv') {
            $filename = 'Laporan_Penilaian_Admin_' . date('Ymd_His') . '.csv';
            return response()->stream(function () use ($rows, $indikators) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
                $head = ['No', 'Nama', 'Prodi', 'Instansi', 'Periode', 'Ruangan'];
                foreach ($indikators as $ind) $head[] = $ind->nama;
                $head[] = 'Nilai Akhir';
                $head[] = 'Catatan';
                fputcsv($out, $head);
                $no = 1;
                foreach ($rows as $r) {
                    $m = $r['mhs'];
                    $line = [$no++, $m->nm_mahasiswa, $m->prodi, $r['instansi'], $r['periode'], $r['ruangan']];
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

        return view('admin.laporan_penilaian', compact(
            'rows', 'indikators', 'prodiOptions', 'instansiOptions', 'ruanganOptions',
            'search', 'prodi', 'instansi', 'ruanganId', 'start', 'end'
        ));
    }
}
