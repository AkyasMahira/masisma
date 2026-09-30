<?php

namespace App\Http\Controllers;

use App\Models\KegiatanFasilitator;
use App\Models\KegiatanPeserta;
use App\Models\NilaiSkill;
use App\Models\TtdPenilaian;
use App\Models\KegiatanPenilaianSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FasilitatorPenilaianController extends Controller
{
    private function fasilitatorByToken($token)
    {
        return KegiatanFasilitator::where('token_fasilitator', $token)->firstOrFail();
    }

    // Materi hanya bisa dibuka kalau fasilitator ini memang ditugaskan di materi tsb
private function materiMilik(KegiatanFasilitator $fasilitator, $materi_id)
    {
        return $fasilitator->materi()
            ->where('kegiatan_materi.id', $materi_id)
            ->where('kegiatan_materi.butuh_penilaian', 1) // <--- TAMBAHKAN INI: Cegah dibuka jika tidak butuh dinilai
            ->firstOrFail();
    }

    private function settingKegiatan($kegiatan_id)
    {
        return KegiatanPenilaianSetting::firstOrCreate(
            ['kegiatan_id' => $kegiatan_id],
            ['jenis_penilaian' => 'centang', 'batas_lulus_persen' => 80]
        );
    }

    // LANGKAH 1: daftar materi yang diampu fasilitator
 // LANGKAH 1: daftar materi yang diampu fasilitator
    public function formPenilaian($token)
    {
        $fasilitator = $this->fasilitatorByToken($token);
        $kegiatan = $fasilitator->kegiatan;

        $totalPeserta = KegiatanPeserta::where('kegiatan_id', $kegiatan->id)->count();

        $materiList = $fasilitator->materi()
            ->where('kegiatan_materi.butuh_penilaian', 1) // HANYA TAMPILKAN MATERI YANG BUTUH DINILAI
            ->withCount('items')
            ->orderBy('urutan')
            ->get()
            ->map(function ($m) use ($fasilitator) {
                $m->sudah_dinilai = TtdPenilaian::where('fasilitator_id', $fasilitator->id)
                    ->where('materi_id', $m->id)
                    ->distinct()
                    ->count('kegiatan_peserta_id');
                return $m;
            });

        return view('public.penilaian_fasilitator.daftar_materi', compact('fasilitator', 'kegiatan', 'materiList', 'totalPeserta'));
    }

    // LANGKAH 2: daftar peserta untuk satu materi
    public function daftarPeserta($token, $materi_id)
    {
        $fasilitator = $this->fasilitatorByToken($token);
        $kegiatan = $fasilitator->kegiatan;
        $materi = $this->materiMilik($fasilitator, $materi_id);

        $daftarPeserta = KegiatanPeserta::where('kegiatan_id', $kegiatan->id)
            ->orderBy('nama_lengkap_gelar', 'asc')
            ->get()
            ->map(function ($p) use ($fasilitator, $materi) {
                $p->sudah_dinilai = TtdPenilaian::where('kegiatan_peserta_id', $p->id)
                    ->where('fasilitator_id', $fasilitator->id)
                    ->where('materi_id', $materi->id)
                    ->exists();
                return $p;
            });

        return view('public.penilaian_fasilitator.daftar_peserta', compact('fasilitator', 'kegiatan', 'materi', 'daftarPeserta'));
    }

    // LANGKAH 3: form checklist satu peserta pada satu materi
    public function showFormPeserta($token, $materi_id, $peserta_id)
    {
        $fasilitator = $this->fasilitatorByToken($token);
        $kegiatan = $fasilitator->kegiatan;
        $materi = $this->materiMilik($fasilitator, $materi_id);
        $peserta = KegiatanPeserta::where('kegiatan_id', $kegiatan->id)->findOrFail($peserta_id);

        $setting = $this->settingKegiatan($kegiatan->id);
        
     // KITA PAKAI SORTBY COLLECTION AGAR MURNI MENGURUTKAN BERDASARKAN ANGKA
$items = $materi->items()->get()->sortBy('urutan')->values();

        // (float) supaya perbandingan === 0.00 / 1.00 / 2.00 di blade selalu cocok
        $nilaiTersimpan = NilaiSkill::where('kegiatan_peserta_id', $peserta->id)
            ->whereIn('item_penilaian_id', $items->pluck('id'))
            ->pluck('nilai', 'item_penilaian_id')
            ->map(function ($v) {
                return (float) $v;
            });

        return view('public.penilaian_fasilitator.form_penilaian', compact(
            'fasilitator', 'kegiatan', 'materi', 'peserta', 'setting', 'items', 'nilaiTersimpan'
        ));
    }

    public function submitPenilaian(Request $request, $token, $materi_id, $peserta_id)
    {
        $fasilitator = $this->fasilitatorByToken($token);
        $kegiatan = $fasilitator->kegiatan;
        $materi = $this->materiMilik($fasilitator, $materi_id);
        $peserta = KegiatanPeserta::where('kegiatan_id', $kegiatan->id)->findOrFail($peserta_id);
        $setting = $this->settingKegiatan($kegiatan->id);

        $request->validate([
            'nilai' => 'required|array|min:1',
            'nilai.*' => 'required|numeric',
            'tanda_tangan' => 'required|string',
        ]);

        // Hanya item milik materi ini yang boleh disimpan
        $idItemValid = $materi->items()->pluck('id')->map(function ($id) {
            return (int) $id;
        })->all();

        DB::transaction(function () use ($request, $peserta, $fasilitator, $materi, $setting, $idItemValid) {
            foreach ($request->nilai as $itemId => $nilai) {
                if (!in_array((int) $itemId, $idItemValid, true)) {
                    continue;
                }

                $nilaiAman = max(0, min((float) $nilai, $setting->nilaiMaksPerItem()));

                NilaiSkill::updateOrCreate(
                    [
                        'kegiatan_peserta_id' => $peserta->id,
                        'item_penilaian_id' => $itemId,
                    ],
                    [
                        'fasilitator_id' => $fasilitator->id,
                        'nilai' => $nilaiAman,
                        'catatan' => $request->catatan[$itemId] ?? null,
                    ]
                );
            }

            TtdPenilaian::updateOrCreate(
                [
                    'kegiatan_peserta_id' => $peserta->id,
                    'fasilitator_id' => $fasilitator->id,
                    'materi_id' => $materi->id,
                ],
                [
                    'tanda_tangan' => $request->tanda_tangan,
                    'waktu_ttd' => now(),
                ]
            );
        });

        return redirect()
            ->route('fasilitator.penilaian.materi', [$fasilitator->token_fasilitator, $materi->id])
            ->with('success', 'Penilaian ' . $materi->nama_materi . ' untuk ' . $peserta->nama_lengkap_gelar . ' berhasil disimpan.');
    }
}