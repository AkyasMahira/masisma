<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\EvaluasiJawaban;
use App\Models\MasterEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class EvaluasiController extends Controller
{
    /* ===================== PUBLIK (Landing) ===================== */

    public function publicForm()
    {
        $unsur = MasterEvaluasi::aktif()->orderBy('urutan')->orderBy('id')->get();
        return view('public.evaluasi.form', compact('unsur'));
    }

    public function publicStore(Request $request)
    {
        $unsur = MasterEvaluasi::aktif()->orderBy('urutan')->get();

        $rules = [
            'nama'          => 'nullable|string|max:255',
            'instansi'      => 'nullable|string|max:255',
            'kontak'        => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'pendidikan'    => 'nullable|string|max:255',
            'umur'          => 'nullable|string|max:10',
            'nama_kegiatan' => 'nullable|string|max:255',
            'kritik'        => 'nullable|string',
            'saran'         => 'nullable|string',
            'jawaban'       => 'required|array',
        ];
        foreach ($unsur as $u) {
            if ($u->tipe === 'rating') {
                $rules['jawaban.' . $u->id] = 'required|integer|min:1|max:4';
            } else {
                $rules['jawaban.' . $u->id] = 'nullable|string';
            }
        }
        $validated = $request->validate($rules, [
            'jawaban.*.required' => 'Mohon lengkapi semua penilaian.',
        ]);

        DB::transaction(function () use ($request, $unsur) {
            $evaluasi = Evaluasi::create([
                'nama'          => $request->nama,
                'instansi'      => $request->instansi,
                'kontak'        => $request->kontak,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pendidikan'    => $request->pendidikan,
                'umur'          => $request->umur,
                'nama_kegiatan' => $request->nama_kegiatan,
                'kritik'        => $request->kritik,
                'saran'         => $request->saran,
            ]);

            $sumRating = 0;
            $countRating = 0;
            foreach ($unsur as $u) {
                $jwb = $request->input('jawaban.' . $u->id);
                $row = [
                    'evaluasi_id'        => $evaluasi->id,
                    'master_evaluasi_id' => $u->id,
                ];
                if ($u->tipe === 'rating') {
                    $row['nilai'] = (int) $jwb;
                    $sumRating += (int) $jwb;
                    $countRating++;
                } else {
                    $row['jawaban_text'] = $jwb;
                }
                EvaluasiJawaban::create($row);
            }

            // IKM tanggapan ini = rata-rata nilai * 25 (skala 0-100)
            if ($countRating > 0) {
                $evaluasi->nilai_ikm = round(($sumRating / $countRating) * 25, 2);
                $evaluasi->save();
            }
        });

        return redirect()->route('evaluasi.public.terimakasih');
    }

    public function terimaKasih()
    {
        return view('public.evaluasi.success');
    }

    /* ===================== ADMIN ===================== */

    public function index(Request $request)
    {
        $query = Evaluasi::query()->orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%$q%")
                  ->orWhere('instansi', 'like', "%$q%")
                  ->orWhere('nama_kegiatan', 'like', "%$q%");
            });
        }

        $evaluasi = $query->paginate(15)->withQueryString();
        $ikm = $this->hitungIkm();

        return view('admin.evaluasi_diklat.index', compact('evaluasi', 'ikm'));
    }

    public function show($id)
    {
        $evaluasi = Evaluasi::with(['jawaban.unsur'])->findOrFail($id);
        return view('admin.evaluasi_diklat.show', compact('evaluasi'));
    }

    public function destroy($id)
    {
        $evaluasi = Evaluasi::findOrFail($id);
        $evaluasi->delete(); // cascade menghapus jawaban
        return redirect()->route('admin.evaluasi.index')->with('success', 'Tanggapan evaluasi berhasil dihapus.');
    }

    public function exportCsv()
    {
        $unsur = MasterEvaluasi::orderBy('urutan')->get();
        $data = Evaluasi::with('jawaban')->orderBy('created_at')->get();

        $filename = 'evaluasi-diklat-' . date('Ymd-His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($unsur, $data) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM utf-8 (Excel)

            $head = ['No', 'Tanggal', 'Nama', 'Instansi', 'Kontak', 'JK', 'Pendidikan', 'Umur', 'Kegiatan'];
            foreach ($unsur as $u) {
                $head[] = $u->kode ?: ('U' . $u->id);
            }
            $head[] = 'IKM Responden';
            $head[] = 'Kritik';
            $head[] = 'Saran';
            fputcsv($out, $head);

            foreach ($data as $i => $ev) {
                $byUnsur = $ev->jawaban->keyBy('master_evaluasi_id');
                $line = [
                    $i + 1,
                    optional($ev->created_at)->format('Y-m-d H:i'),
                    $ev->nama, $ev->instansi, $ev->kontak, $ev->jenis_kelamin,
                    $ev->pendidikan, $ev->umur, $ev->nama_kegiatan,
                ];
                foreach ($unsur as $u) {
                    $j = $byUnsur->get($u->id);
                    $line[] = $j ? ($u->tipe === 'rating' ? $j->nilai : $j->jawaban_text) : '';
                }
                $line[] = $ev->nilai_ikm;
                $line[] = $ev->kritik;
                $line[] = $ev->saran;
                fputcsv($out, $line);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $ikm = $this->hitungIkm();
        $total = Evaluasi::count();
        $pdf = PDF::loadView('admin.evaluasi_diklat.pdf', compact('ikm', 'total'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('laporan-ikm-diklat-' . date('Ymd') . '.pdf');
    }

    /**
     * Hitung IKM sesuai Permenpan RB No. 14 Tahun 2017.
     * IKM = (Σ NRR tertimbang) × 25 ; bobot = 1 / jumlah unsur.
     */
    private function hitungIkm()
    {
        $unsur = MasterEvaluasi::where('tipe', 'rating')->where('aktif', true)
            ->orderBy('urutan')->get();

        $totalResponden = Evaluasi::count();
        $jumlahUnsur = $unsur->count();
        $bobot = $jumlahUnsur > 0 ? (1 / $jumlahUnsur) : 0;

        $perUnsur = [];
        $ikmTertimbang = 0;

        foreach ($unsur as $u) {
            $agg = EvaluasiJawaban::where('master_evaluasi_id', $u->id)
                ->whereNotNull('nilai')
                ->selectRaw('AVG(nilai) as rata, COUNT(*) as jml')
                ->first();

            $nrr = $agg && $agg->jml > 0 ? (float) $agg->rata : 0;
            $ikmTertimbang += $nrr * $bobot;

            $perUnsur[] = [
                'kode'       => $u->kode ?: ('U' . $u->id),
                'pertanyaan' => $u->pertanyaan,
                'nrr'        => round($nrr, 3),
                'nilai'      => round($nrr * 25, 2),
                'mutu'       => $this->mutu($nrr * 25),
                'responden'  => $agg ? (int) $agg->jml : 0,
            ];
        }

        $nilaiIkm = round($ikmTertimbang * 25, 2);

        return [
            'nilai'           => $nilaiIkm,
            'mutu'            => $this->mutu($nilaiIkm),
            'kategori'        => $this->kategori($nilaiIkm),
            'total_responden' => $totalResponden,
            'jumlah_unsur'    => $jumlahUnsur,
            'per_unsur'       => $perUnsur,
        ];
    }

    private function mutu($nilai)
    {
        if ($nilai >= 88.31) return 'A';
        if ($nilai >= 76.61) return 'B';
        if ($nilai >= 65.00) return 'C';
        return 'D';
    }

    private function kategori($nilai)
    {
        if ($nilai >= 88.31) return 'Sangat Baik';
        if ($nilai >= 76.61) return 'Baik';
        if ($nilai >= 65.00) return 'Kurang Baik';
        return 'Tidak Baik';
    }
}
