<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\EvaluasiJawaban;
use App\Models\Kegiatan;
use App\Models\MasterEvaluasi;
use App\Models\Mou;
use App\Models\PraPenelitian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

/**
 * Evaluasi layanan (IKM) terpisah per jenis:
 *  - pelatihan  : per kegiatan pelatihan (pertanyaan khusus per pelatihan, default dari master)
 *  - pendidikan : mahasiswa magang/PKL (syarat unduh sertifikat), rekap per bulan/tahun/kampus
 *  - penelitian : per penelitian atau keseluruhan
 */
class EvaluasiController extends Controller
{
    const JENIS = ['pelatihan', 'pendidikan', 'penelitian'];

    /* ===================== PUBLIK ===================== */

    /** Halaman pilih pelatihan yang akan dievaluasi. */
    public function publicForm(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $kegiatan = Kegiatan::query()
            ->when($q !== '', function ($w) use ($q) { $w->where('nama_kegiatan', 'like', "%{$q}%"); })
            ->when($q === '', function ($w) { $w->where('tanggal_mulai', '>=', now()->subDays(120)->toDateString()); })
            ->orderBy('tanggal_mulai', 'desc')->limit(60)->get(['id', 'nama_kegiatan', 'tanggal_mulai', 'tanggal_selesai']);
        $kegiatan->each(function ($k) { $k->token_eval = Evaluasi::token('pelatihan', $k->id); });
        return view('public.evaluasi.pilih', compact('kegiatan', 'q'));
    }

    /** Form umum lama (tanpa memilih pelatihan) — disimpan sebagai evaluasi pelatihan umum. */
    public function publicStore(Request $request)
    {
        $unsur = MasterEvaluasi::untukForm('pelatihan');
        $this->validasi($request, $unsur);
        $this->simpan($request, $unsur, ['jenis' => 'pelatihan', 'nama_kegiatan' => $request->nama_kegiatan]);
        return redirect()->route('evaluasi.public.terimakasih');
    }

    public function formPelatihan($kegiatanId, $token)
    {
        $kegiatan = $this->cekToken('pelatihan', $kegiatanId, $token, Kegiatan::class);
        return view('public.evaluasi.form', [
            'unsur'     => MasterEvaluasi::untukForm('pelatihan', $kegiatan->id),
            'actionUrl' => route('evaluasi.pelatihan.store', [$kegiatan->id, $token]),
            'jenis'     => 'pelatihan',
            'konteks'   => ['label' => 'Pelatihan', 'nilai' => $kegiatan->nama_kegiatan],
        ]);
    }

    public function storePelatihan(Request $request, $kegiatanId, $token)
    {
        $kegiatan = $this->cekToken('pelatihan', $kegiatanId, $token, Kegiatan::class);
        $unsur = MasterEvaluasi::untukForm('pelatihan', $kegiatan->id);
        $this->validasi($request, $unsur);
        $this->simpan($request, $unsur, ['jenis' => 'pelatihan', 'kegiatan_id' => $kegiatan->id, 'nama_kegiatan' => $kegiatan->nama_kegiatan]);
        return redirect()->route('evaluasi.public.terimakasih');
    }

    public function formPenelitian($praId, $token)
    {
        $pra = $this->cekToken('penelitian', $praId, $token, PraPenelitian::class);
        return view('public.evaluasi.form', [
            'unsur'     => MasterEvaluasi::untukForm('penelitian'),
            'actionUrl' => route('evaluasi.penelitian.store', [$pra->id, $token]),
            'jenis'     => 'penelitian',
            'konteks'   => ['label' => 'Penelitian', 'nilai' => $pra->judul],
            'prefill'   => ['instansi' => optional($pra->mou)->nama_instansi],
        ]);
    }

    public function storePenelitian(Request $request, $praId, $token)
    {
        $pra = $this->cekToken('penelitian', $praId, $token, PraPenelitian::class);
        $unsur = MasterEvaluasi::untukForm('penelitian');
        $this->validasi($request, $unsur);
        $this->simpan($request, $unsur, [
            'jenis' => 'penelitian', 'pra_penelitian_id' => $pra->id, 'mou_id' => $pra->mou_id,
            'nama_kegiatan' => 'Penelitian: ' . \Illuminate\Support\Str::limit((string) $pra->judul, 200),
        ]);
        return redirect()->route('evaluasi.public.terimakasih');
    }

    public function terimaKasih()
    {
        return view('public.evaluasi.success');
    }

    /* ============ EVALUASI PENDIDIKAN / MAGANG (syarat unduh sertifikat) ============ */

    private function mahasiswaLogin()
    {
        return \App\Models\Mahasiswa::where('user_id', auth()->id())->latest()->first();
    }

    public function magangForm()
    {
        $mhs = $this->mahasiswaLogin();
        if (!$mhs) return redirect()->route('dashboard')->with('error', 'Data magang tidak ditemukan.');
        if ($mhs->evaluasi_at) return redirect()->route('sertifikat.download', $mhs->share_token);

        return view('public.evaluasi.form', [
            'unsur'     => MasterEvaluasi::untukForm('pendidikan'),
            'actionUrl' => route('evaluasi.magang.store'),
            'jenis'     => 'pendidikan',
            'konteks'   => ['label' => 'Praktik / Magang', 'nilai' => optional($mhs->mou)->nama_instansi ?: 'Magang/PKL'],
            'prefill'   => ['nama' => $mhs->nm_mahasiswa, 'instansi' => optional($mhs->mou)->nama_instansi ?? $mhs->univ_asal],
            'gateNote'  => 'Isi evaluasi ini terlebih dahulu untuk dapat mengunduh sertifikat magang Anda.',
        ]);
    }

    public function magangStore(Request $request)
    {
        $mhs = $this->mahasiswaLogin();
        if (!$mhs) return redirect()->route('dashboard')->with('error', 'Data magang tidak ditemukan.');
        if ($mhs->evaluasi_at) return redirect()->route('sertifikat.download', $mhs->share_token);

        $unsur = MasterEvaluasi::untukForm('pendidikan');
        $this->validasi($request, $unsur);
        DB::transaction(function () use ($request, $unsur, $mhs) {
            $this->simpan($request, $unsur, [
                'jenis' => 'pendidikan', 'mahasiswa_id' => $mhs->id, 'mou_id' => $mhs->mou_id,
                'nama' => $request->nama ?: $mhs->nm_mahasiswa, 'nama_kegiatan' => 'Magang/PKL',
            ]);
            $mhs->evaluasi_at = now();
            $mhs->save();
        });

        return redirect()->route('sertifikat.download', $mhs->share_token)
            ->with('success', 'Terima kasih! Evaluasi tersimpan. Sertifikat Anda siap diunduh.');
    }

    /* ---------- helper simpan ---------- */

    private function cekToken($jenis, $id, $token, $model)
    {
        abort_unless(hash_equals(Evaluasi::token($jenis, $id), (string) $token), 404, 'Link evaluasi tidak valid.');
        return $model::findOrFail($id);
    }

    private function validasi(Request $request, $unsur)
    {
        $rules = [
            'nama' => 'nullable|string|max:255', 'instansi' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:255', 'jenis_kelamin' => 'nullable|in:L,P',
            'pendidikan' => 'nullable|string|max:255', 'umur' => 'nullable|string|max:10',
            'nama_kegiatan' => 'nullable|string|max:255', 'kritik' => 'nullable|string',
            'saran' => 'nullable|string', 'jawaban' => 'required|array',
        ];
        foreach ($unsur as $u) {
            $rules['jawaban.' . $u->id] = $u->tipe === 'rating' ? 'required|integer|min:1|max:4' : 'nullable|string';
        }
        $request->validate($rules, ['jawaban.*.required' => 'Mohon lengkapi semua penilaian.']);
    }

    private function simpan(Request $request, $unsur, array $attr)
    {
        return DB::transaction(function () use ($request, $unsur, $attr) {
            $evaluasi = Evaluasi::create(array_merge([
                'nama' => $request->nama, 'instansi' => $request->instansi, 'kontak' => $request->kontak,
                'jenis_kelamin' => $request->jenis_kelamin, 'pendidikan' => $request->pendidikan,
                'umur' => $request->umur, 'kritik' => $request->kritik, 'saran' => $request->saran,
            ], $attr));
            $sum = 0; $n = 0;
            foreach ($unsur as $u) {
                $jwb = $request->input('jawaban.' . $u->id);
                $row = ['evaluasi_id' => $evaluasi->id, 'master_evaluasi_id' => $u->id];
                if ($u->tipe === 'rating') { $row['nilai'] = (int) $jwb; $sum += (int) $jwb; $n++; }
                else { $row['jawaban_text'] = $jwb; }
                EvaluasiJawaban::create($row);
            }
            // IKM tanggapan ini = rata-rata nilai * 25 (skala 0-100)
            if ($n > 0) { $evaluasi->nilai_ikm = round(($sum / $n) * 25, 2); $evaluasi->save(); }
            return $evaluasi;
        });
    }

    /* ===================== ADMIN ===================== */

    private function jenisDari(Request $request)
    {
        $j = $request->get('jenis', 'pelatihan');
        return in_array($j, self::JENIS, true) ? $j : 'pelatihan';
    }

    /** Query tanggapan sesuai jenis + filter (kegiatan/kampus/penelitian, bulan, tahun, cari). */
    private function query(Request $request, $jenis)
    {
        $q = Evaluasi::query()->where('jenis', $jenis);
        if ($request->filled('kegiatan_id'))       $q->where('kegiatan_id', $request->kegiatan_id);
        if ($request->filled('mou_id'))            $q->where('mou_id', $request->mou_id);
        if ($request->filled('pra_penelitian_id')) $q->where('pra_penelitian_id', $request->pra_penelitian_id);
        if ($request->filled('bulan') && preg_match('/^(\d{4})-(\d{2})$/', $request->bulan, $m)) {
            $q->whereYear('created_at', $m[1])->whereMonth('created_at', $m[2]);
        } elseif ($request->filled('tahun')) {
            $q->whereYear('created_at', (int) $request->tahun);
        }
        if ($request->filled('q')) {
            $s = $request->q;
            $q->where(function ($w) use ($s) {
                $w->where('nama', 'like', "%$s%")->orWhere('instansi', 'like', "%$s%")->orWhere('nama_kegiatan', 'like', "%$s%");
            });
        }
        return $q;
    }

    /** Teks ringkas filter aktif (untuk judul laporan). */
    private function labelFilter(Request $request)
    {
        $p = [];
        if ($request->filled('kegiatan_id'))       $p[] = 'Pelatihan: ' . optional(Kegiatan::find($request->kegiatan_id))->nama_kegiatan;
        if ($request->filled('mou_id'))            $p[] = 'Kampus: ' . optional(Mou::find($request->mou_id))->nama_instansi;
        if ($request->filled('pra_penelitian_id')) $p[] = 'Penelitian: ' . optional(PraPenelitian::find($request->pra_penelitian_id))->judul;
        if ($request->filled('bulan') && preg_match('/^(\d{4})-(\d{2})$/', $request->bulan, $m)) {
            $p[] = 'Bulan: ' . \Carbon\Carbon::create($m[1], $m[2], 1)->isoFormat('MMMM YYYY');
        } elseif ($request->filled('tahun')) {
            $p[] = 'Tahun: ' . (int) $request->tahun;
        }
        return $p ? implode(' · ', $p) : 'Keseluruhan';
    }

    public function index(Request $request)
    {
        $jenis = $this->jenisDari($request);
        $base = $this->query($request, $jenis);

        $ikm = $this->hitungIkm((clone $base)->select('evaluasis.id'));
        $evaluasi = (clone $base)->with(['kegiatan:id,nama_kegiatan', 'mou:id,nama_instansi,nama_universitas', 'praPenelitian:id,judul'])
            ->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Rekap per grup (pelatihan / kampus / penelitian) dengan filter periode yang sama
        $grupKol = ['pelatihan' => 'kegiatan_id', 'pendidikan' => 'mou_id', 'penelitian' => 'pra_penelitian_id'][$jenis];
        $rq = $request->duplicate($request->except(['kegiatan_id', 'mou_id', 'pra_penelitian_id', 'page']));
        $grup = $this->rekapGrup($this->query($rq, $jenis), $grupKol, $jenis);

        // Opsi filter
        $tahunList = Evaluasi::where('jenis', $jenis)->selectRaw('YEAR(created_at) as y')->distinct()->orderByDesc('y')->pluck('y');
        $opsi = [];
        if ($jenis === 'pelatihan') {
            $opsi = Kegiatan::orderBy('tanggal_mulai', 'desc')->get(['id', 'nama_kegiatan', 'tanggal_mulai'])
                ->mapWithKeys(function ($k) { return [$k->id => $k->nama_kegiatan . ($k->tanggal_mulai ? ' (' . \Carbon\Carbon::parse($k->tanggal_mulai)->format('d/m/Y') . ')' : '')]; });
        } elseif ($jenis === 'pendidikan') {
            $ids = Evaluasi::where('jenis', 'pendidikan')->whereNotNull('mou_id')->distinct()->pluck('mou_id');
            $opsi = Mou::whereIn('id', $ids)->get()->mapWithKeys(function ($m) { return [$m->id => ($m->nama_instansi ?: $m->nama_universitas)]; })->sort();
        } else {
            $ids = Evaluasi::where('jenis', 'penelitian')->whereNotNull('pra_penelitian_id')->distinct()->pluck('pra_penelitian_id');
            $opsi = PraPenelitian::whereIn('id', $ids)->pluck('judul', 'id');
        }

        $labelFilter = $this->labelFilter($request);
        return view('admin.evaluasi_diklat.index', compact('jenis', 'ikm', 'evaluasi', 'grup', 'tahunList', 'opsi', 'labelFilter'));
    }

    public function show($id)
    {
        $evaluasi = Evaluasi::with(['jawaban.unsur', 'kegiatan', 'mou', 'praPenelitian'])->findOrFail($id);
        return view('admin.evaluasi_diklat.show', compact('evaluasi'));
    }

    public function destroy($id)
    {
        $evaluasi = Evaluasi::findOrFail($id);
        $jenis = $evaluasi->jenis;
        $evaluasi->delete(); // cascade menghapus jawaban
        return redirect()->route('admin.evaluasi.index', ['jenis' => $jenis])->with('success', 'Tanggapan evaluasi berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $jenis = $this->jenisDari($request);
        $data = $this->query($request, $jenis)->with(['jawaban', 'kegiatan:id,nama_kegiatan', 'mou:id,nama_instansi,nama_universitas', 'praPenelitian:id,judul'])
            ->orderBy('created_at')->get();
        $unsurIds = EvaluasiJawaban::whereIn('evaluasi_id', $data->pluck('id'))->distinct()->pluck('master_evaluasi_id');
        $unsur = MasterEvaluasi::whereIn('id', $unsurIds)->orderBy('kegiatan_id')->orderBy('urutan')->orderBy('id')->get();

        $filename = 'evaluasi-' . $jenis . '-' . date('Ymd-His') . '.csv';
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => "attachment; filename=\"$filename\""];

        $callback = function () use ($unsur, $data, $jenis) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM utf-8 (Excel)
            $grupHead = ['pelatihan' => 'Pelatihan', 'pendidikan' => 'Kampus', 'penelitian' => 'Penelitian'][$jenis];
            $head = ['No', 'Tanggal', 'Nama', 'Instansi', 'Kontak', 'JK', 'Pendidikan', 'Umur', $grupHead];
            foreach ($unsur as $u) $head[] = ($u->kode ?: ('U' . $u->id)) . ($u->kegiatan_id ? '*' : '');
            array_push($head, 'IKM Responden', 'Kritik', 'Saran');
            fputcsv($out, $head);

            foreach ($data as $i => $ev) {
                $byUnsur = $ev->jawaban->keyBy('master_evaluasi_id');
                $grup = $jenis === 'pelatihan' ? (optional($ev->kegiatan)->nama_kegiatan ?: $ev->nama_kegiatan)
                      : ($jenis === 'pendidikan' ? (optional($ev->mou)->nama_instansi ?: $ev->instansi) : optional($ev->praPenelitian)->judul);
                $line = [$i + 1, optional($ev->created_at)->format('Y-m-d H:i'), $ev->nama, $ev->instansi, $ev->kontak,
                         $ev->jenis_kelamin, $ev->pendidikan, $ev->umur, $grup];
                foreach ($unsur as $u) {
                    $j = $byUnsur->get($u->id);
                    $line[] = $j ? ($u->tipe === 'rating' ? $j->nilai : $j->jawaban_text) : '';
                }
                array_push($line, $ev->nilai_ikm, $ev->kritik, $ev->saran);
                fputcsv($out, $line);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $jenis = $this->jenisDari($request);
        $base = $this->query($request, $jenis);
        $ikm = $this->hitungIkm((clone $base)->select('evaluasis.id'));
        $total = (clone $base)->count();
        $grupKol = ['pelatihan' => 'kegiatan_id', 'pendidikan' => 'mou_id', 'penelitian' => 'pra_penelitian_id'][$jenis];
        $grup = $this->rekapGrup(clone $base, $grupKol, $jenis);
        $labelFilter = $this->labelFilter($request);
        $judulJenis = MasterEvaluasi::JENIS[$jenis];

        $pdf = PDF::loadView('admin.evaluasi_diklat.pdf', compact('ikm', 'total', 'grup', 'labelFilter', 'judulJenis', 'jenis'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('laporan-ikm-' . $jenis . '-' . date('Ymd') . '.pdf');
    }

    /**
     * Hitung IKM sesuai Permenpan RB No. 14 Tahun 2017.
     * NRR per unsur = rata-rata nilai; IKM = Σ(NRR × bobot) × 25, bobot = 1 / jumlah unsur.
     * Unsur = unsur rating yang benar-benar dijawab pada tanggapan terpilih
     * (mendukung pertanyaan khusus per pelatihan).
     * @param \Illuminate\Database\Eloquent\Builder $idsQuery query yang memilih evaluasis.id
     */
    private function hitungIkm($idsQuery)
    {
        $totalResponden = (clone $idsQuery)->count();

        $agg = EvaluasiJawaban::query()
            ->join('master_evaluasi as m', 'm.id', '=', 'evaluasi_jawaban.master_evaluasi_id')
            ->where('m.tipe', 'rating')->whereNotNull('evaluasi_jawaban.nilai')
            ->whereIn('evaluasi_jawaban.evaluasi_id', $idsQuery->getQuery())
            ->groupBy('m.id', 'm.kode', 'm.pertanyaan', 'm.urutan', 'm.kegiatan_id')
            ->orderByRaw('m.kegiatan_id IS NOT NULL')->orderBy('m.urutan')->orderBy('m.id')
            ->selectRaw('m.id, m.kode, m.pertanyaan, m.kegiatan_id, AVG(evaluasi_jawaban.nilai) as rata, COUNT(*) as jml')
            ->get();

        $jumlahUnsur = $agg->count();
        $bobot = $jumlahUnsur > 0 ? (1 / $jumlahUnsur) : 0;
        $perUnsur = []; $tertimbang = 0;
        foreach ($agg as $a) {
            $nrr = (float) $a->rata;
            $tertimbang += $nrr * $bobot;
            $perUnsur[] = [
                'kode' => $a->kode ?: ('U' . $a->id), 'pertanyaan' => $a->pertanyaan, 'khusus' => (bool) $a->kegiatan_id,
                'nrr' => round($nrr, 3), 'nilai' => round($nrr * 25, 2), 'mutu' => $this->mutu($nrr * 25), 'responden' => (int) $a->jml,
            ];
        }
        $nilai = round($tertimbang * 25, 2);

        return [
            'nilai' => $nilai, 'mutu' => $this->mutu($nilai), 'kategori' => $this->kategori($nilai),
            'total_responden' => $totalResponden, 'jumlah_unsur' => $jumlahUnsur, 'per_unsur' => $perUnsur,
        ];
    }

    /** Rekap IKM per grup (kegiatan / kampus / penelitian). */
    private function rekapGrup($query, $kolom, $jenis)
    {
        $ids = (clone $query)->select('evaluasis.id');
        $rows = EvaluasiJawaban::query()
            ->join('evaluasis as e', 'e.id', '=', 'evaluasi_jawaban.evaluasi_id')
            ->join('master_evaluasi as m', 'm.id', '=', 'evaluasi_jawaban.master_evaluasi_id')
            ->where('m.tipe', 'rating')->whereNotNull('evaluasi_jawaban.nilai')
            ->whereIn('evaluasi_jawaban.evaluasi_id', $ids->getQuery())
            ->groupBy('e.' . $kolom, 'm.id')
            ->selectRaw('e.' . $kolom . ' as gid, m.id as uid, AVG(evaluasi_jawaban.nilai) as rata')
            ->get()->groupBy('gid');
        $jml = (clone $query)->selectRaw($kolom . ' as gid, COUNT(*) as n, MAX(created_at) as terakhir')->groupBy($kolom)->get()->keyBy('gid');

        $nama = [];
        $gids = $jml->keys()->filter()->all();
        if ($jenis === 'pelatihan')  $nama = Kegiatan::whereIn('id', $gids)->pluck('nama_kegiatan', 'id')->all();
        if ($jenis === 'pendidikan') $nama = Mou::whereIn('id', $gids)->get()->mapWithKeys(function ($m) { return [$m->id => ($m->nama_instansi ?: $m->nama_universitas)]; })->all();
        if ($jenis === 'penelitian') $nama = PraPenelitian::whereIn('id', $gids)->pluck('judul', 'id')->all();

        $out = [];
        foreach ($jml as $gid => $j) {
            $r = $rows->get($gid);
            $ikm = $r && $r->count() ? round($r->avg('rata') * 25, 2) : null;
            $out[] = [
                'id' => $gid ?: null,
                'nama' => $gid ? ($nama[$gid] ?? '(data terhapus #' . $gid . ')') : ($jenis === 'pelatihan' ? '(Tanpa pelatihan — form umum)' : '(Tidak tercatat)'),
                'responden' => (int) $j->n, 'ikm' => $ikm, 'mutu' => $ikm !== null ? $this->mutu($ikm) : '-',
                'terakhir' => $j->terakhir,
                'token' => ($gid && $jenis !== 'pendidikan') ? Evaluasi::token($jenis, $gid) : null,
            ];
        }
        usort($out, function ($a, $b) { return $b['responden'] <=> $a['responden']; });
        return $out;
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
