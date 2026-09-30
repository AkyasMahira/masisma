<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanMateri;
use App\Models\KegiatanPeserta;
use App\Models\KegiatanFasilitator;
use App\Models\MasterInstansi;
use App\Models\NilaiSkill;
use App\Models\TtdPenilaian;
use Illuminate\Http\Request;

class RekapPenilaianController extends Controller
{
    // Nilai Post-Test minimal agar peserta boleh LULUS. Pre-Test tidak dipakai.
    const BATAS_POSTTEST = 80;

    // TAHAP 3: REKAP & KELULUSAN (otomatis oleh aplikasi)
    //
    // Aturan:
    //  - % skill & status Kompeten dihitung PER MATERI (rumus sama seperti sebelumnya)
    //  - LULUS jika: semua materi Kompeten DAN Post-Test >= BATAS_POSTTEST
    //  - Rata-rata skill hanya informasi (dari materi yang sudah selesai dinilai)
    public function index(Request $request, $kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $setting = $kegiatan->penilaianSetting;
        $nilaiMaksItem = $setting ? $setting->nilaiMaksPerItem() : 1;
        $batasLulus = $setting ? (float) $setting->batas_lulus_persen : 80;

        // Materi yang sudah punya checklist saja (materi kosong tidak bisa dinilai)
        $materiList = KegiatanMateri::with('items')
            ->where('kegiatan_id', $kegiatan_id)
            ->orderBy('urutan')
            ->get()
            ->filter(function ($m) {
                return $m->items->count() > 0;
            })
            ->values();

        $itemKeMateri = [];
        foreach ($materiList as $m) {
            foreach ($m->items as $it) {
                $itemKeMateri[$it->id] = $m->id;
            }
        }

        $query = KegiatanPeserta::with(['instansi', 'nilaiTeori', 'nilaiSkill'])
            ->where('kegiatan_id', $kegiatan_id);

        if ($request->search) {
            $query->where('nama_lengkap_gelar', 'like', '%' . $request->search . '%');
        }
        if ($request->instansi_id) {
            $query->where('instansi_id', $request->instansi_id);
        }
        // status_kelulusan difilter setelah dihitung (lihat di bawah)

        $pesertaList = $query->orderBy('nama_lengkap_gelar', 'asc')->get();

        $rekap = $pesertaList->map(function ($p) use ($materiList, $itemKeMateri, $nilaiMaksItem, $batasLulus) {
            $nilaiPerMateri = $p->nilaiSkill->groupBy(function ($n) use ($itemKeMateri) {
                return $itemKeMateri[$n->item_penilaian_id] ?? 0;
            });

            $perMateri = [];
            $semuaLengkap = $materiList->count() > 0;
            $semuaKompeten = true;
            $persenLengkap = [];

            foreach ($materiList as $m) {
                $rows = $nilaiPerMateri->get($m->id, collect());
                $totalItem = $m->items->count();
                $maksimal = $totalItem * $nilaiMaksItem;
                $persen = $maksimal > 0 ? round($rows->sum('nilai') / $maksimal * 100, 2) : 0;
                $lengkap = $rows->count() >= $totalItem;
                $kompeten = $lengkap && $persen >= $batasLulus;

                $perMateri[$m->id] = [
                    'persen' => $persen,
                    'terisi' => $rows->count(),
                    'lengkap' => $lengkap,
                    'kompeten' => $kompeten,
                ];

                if ($lengkap) {
                    $persenLengkap[] = $persen;
                } else {
                    $semuaLengkap = false;
                }
                if (!$kompeten) {
                    $semuaKompeten = false;
                }
            }

            $posttest = optional($p->nilaiTeori)->nilai_posttest;
            $posttestLulus = $posttest !== null && $posttest >= self::BATAS_POSTTEST;

            if (!$semuaLengkap || $posttest === null) {
                $status = 'Belum Selesai Dinilai';
            } else {
                $status = ($semuaKompeten && $posttestLulus) ? 'Lulus' : 'Tidak Lulus';
            }

            return [
                'id' => $p->id,
                'nama' => $p->nama_lengkap_gelar,
                'instansi' => $p->instansi->nama_instansi ?? 'Internal RS',
                'nilai_posttest' => $posttest,
                'posttest_lulus' => $posttestLulus,
                'per_materi' => $perMateri,
                'rata_rata' => count($persenLengkap) ? round(array_sum($persenLengkap) / count($persenLengkap), 2) : null,
                'status_kelulusan' => $status,
            ];
        });

        if ($request->status_kelulusan) {
            $rekap = $rekap->where('status_kelulusan', $request->status_kelulusan);
        }
        $rekap = $rekap->values();

        // Data untuk tombol "Download Excel sesuai filter aktif" (SheetJS di sisi client)
        $exportHeader = ['No', 'Nama Peserta', 'Instansi', 'Post-Test'];
        foreach ($materiList as $m) {
            $exportHeader[] = $m->nama_materi . ' (%)';
        }
        $exportHeader[] = 'Rata-rata Skill (%)';
        $exportHeader[] = 'Status Kelulusan';

        $exportRows = [];
        foreach ($rekap as $i => $r) {
            $row = [$i + 1, $r['nama'], $r['instansi'], $r['nilai_posttest'] !== null ? (float) $r['nilai_posttest'] : '-'];
            foreach ($materiList as $m) {
                $pm = $r['per_materi'][$m->id];
                $row[] = $pm['lengkap'] ? $pm['persen'] : '-';
            }
            $row[] = $r['rata_rata'] !== null ? $r['rata_rata'] : '-';
            $row[] = $r['status_kelulusan'];
            $exportRows[] = $row;
        }

        $instansi = MasterInstansi::orderBy('nama_instansi', 'asc')->get();
        $batasPosttest = self::BATAS_POSTTEST;

        return view('admin.kegiatan.penilaian.rekap', compact(
            'kegiatan', 'setting', 'rekap', 'instansi', 'batasLulus', 'batasPosttest',
            'materiList', 'exportHeader', 'exportRows'
        ));
    }

    // Lembar observasi PDF: satu peserta, satu materi
    public function cetakPdf($kegiatan_id, $peserta_id, $materi_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $setting = $kegiatan->penilaianSetting;
        $peserta = KegiatanPeserta::with(['instansi'])->where('kegiatan_id', $kegiatan_id)->findOrFail($peserta_id);
        $materi = KegiatanMateri::where('kegiatan_id', $kegiatan_id)->findOrFail($materi_id);

        $items = $materi->items()->get();

        $nilaiData = NilaiSkill::where('kegiatan_peserta_id', $peserta_id)
            ->whereIn('item_penilaian_id', $items->pluck('id'))
            ->get();
        $nilaiTersimpan = $nilaiData->pluck('nilai', 'item_penilaian_id');

        // Fasilitator yang menilai & menandatangani materi ini
        $ttd = TtdPenilaian::where('kegiatan_peserta_id', $peserta_id)
            ->where('materi_id', $materi->id)
            ->first();
        $fasilitator = $ttd ? KegiatanFasilitator::find($ttd->fasilitator_id) : null;

        // Kalkulasi total materi ini
        $nilaiMaksItem = $setting ? $setting->nilaiMaksPerItem() : 1;
        $totalItem = $items->count();
        $totalNilaiSkill = $nilaiData->sum('nilai');
        $maksimalSkill = $totalItem * $nilaiMaksItem;
        $persenSkill = $maksimalSkill > 0 ? round(($totalNilaiSkill / $maksimalSkill) * 100, 2) : 0;

        $batasLulus = $setting ? (float) $setting->batas_lulus_persen : 80;
        $statusLulus = $persenSkill >= $batasLulus ? 'Kompeten' : 'Tidak Kompeten';

        return view('admin.kegiatan.penilaian.cetak_pdf', compact(
            'kegiatan', 'materi', 'peserta', 'items', 'nilaiTersimpan',
            'setting', 'persenSkill', 'statusLulus', 'fasilitator', 'ttd'
        ));
    }
}