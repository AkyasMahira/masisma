<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanPenilaianSetting;
use App\Models\KegiatanMateri;
use App\Models\ItemPenilaianSkill;
use App\Models\KegiatanFasilitator;
use Illuminate\Http\Request;

class PenilaianSettingController extends Controller
{
    // TAHAP 1: PERSIAPAN
    // Diklat memilih jenis penilaian (centang/skor), atur fasilitator,
    // lalu buat Materi -> tentukan fasilitator pengampu -> isi checklist tiap materi
    public function index($kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $setting = KegiatanPenilaianSetting::firstOrCreate(
            ['kegiatan_id' => $kegiatan_id],
            ['jenis_penilaian' => 'centang', 'batas_lulus_persen' => 80]
        );

        $fasilitatorList = KegiatanFasilitator::with('materi')->where('kegiatan_id', $kegiatan_id)->get();
        
        // Load materi beserta items-nya yang diurutkan berdasarkan kolom 'urutan'
        $materiList = KegiatanMateri::with(['items' => function($query) {
                $query->orderBy('urutan', 'asc');
            }, 'fasilitator'])
            ->where('kegiatan_id', $kegiatan_id)
            ->orderBy('urutan')
            ->get();

        return view('admin.kegiatan.penilaian.setting', compact('kegiatan', 'setting', 'fasilitatorList', 'materiList'));
    }

    public function storeSetting(Request $request, $kegiatan_id)
    {
        $request->validate([
            'jenis_penilaian' => 'required|in:centang,skor,custom',
            'batas_lulus_persen' => 'required|numeric|min:1|max:100',
            'skala' => 'required_if:jenis_penilaian,custom|array|min:2|max:10',
            'skala.*.nilai' => 'required_if:jenis_penilaian,custom|numeric|min:0|max:99',
            'skala.*.label' => 'required_if:jenis_penilaian,custom|string|max:60',
        ], [
            'skala.required_if' => 'Skala custom minimal berisi 2 pilihan.',
            'skala.min' => 'Skala custom minimal berisi 2 pilihan.',
            'skala.max' => 'Skala custom maksimal 10 pilihan.',
            'skala.*.nilai.required_if' => 'Setiap pilihan skala wajib diisi nilainya.',
            'skala.*.nilai.numeric' => 'Nilai skala harus berupa angka.',
            'skala.*.nilai.max' => 'Nilai skala maksimal 99.',
            'skala.*.label.required_if' => 'Setiap pilihan skala wajib diberi keterangan.',
        ]);

        $skala = null;
        if ($request->jenis_penilaian === 'custom') {
            $skala = [];
            foreach (array_values($request->skala) as $row) {
                $skala[] = ['nilai' => round((float) $row['nilai'], 1), 'label' => trim($row['label'])];
            }
            $nilaiList = array_column($skala, 'nilai');
            if (count(array_unique(array_map('strval', $nilaiList))) !== count($nilaiList)) {
                return back()->withInput()->withErrors(['skala' => 'Nilai pada skala custom tidak boleh ada yang sama.']);
            }
            if (max($nilaiList) <= 0) {
                return back()->withInput()->withErrors(['skala' => 'Minimal satu pilihan harus bernilai lebih dari 0.']);
            }
            usort($skala, function ($a, $b) { return $a['nilai'] <=> $b['nilai']; });
        }

        $setting = KegiatanPenilaianSetting::firstOrNew(['kegiatan_id' => $kegiatan_id]);
        $nilaiLama = $setting->exists ? $setting->nilaiSah() : [];

        $setting->jenis_penilaian = $request->jenis_penilaian;
        $setting->skala_json = $skala;
        $setting->batas_lulus_persen = $request->batas_lulus_persen;
        if ($request->has('pakai_posttest')) {
            $setting->pakai_posttest = $request->boolean('pakai_posttest');
        }
        if ($request->has('sertifikat_halaman_2')) {
            $setting->sertifikat_halaman_2 = $request->boolean('sertifikat_halaman_2');
        }
        $setting->save();

        $pesan = 'Format penilaian berhasil disimpan.';
        if ($nilaiLama && $nilaiLama != $setting->nilaiSah()) {
            $sudahDinilai = \App\Models\NilaiSkill::whereIn('item_penilaian_id',
                ItemPenilaianSkill::where('kegiatan_id', $kegiatan_id)->pluck('id'))->count();
            if ($sudahDinilai) {
                $pesan .= ' Catatan: sudah ada ' . $sudahDinilai . ' nilai yang diinput dengan skala lama — persentase kelulusan dihitung ulang memakai nilai maksimum skala baru, jadi minta fasilitator meninjau ulang bila perlu.';
            }
        }

        return redirect()->back()->with('success', $pesan);
    }

public function storeMateri(Request $request, $kegiatan_id)
    {
        $request->validate([
            'nama_materi' => 'required|string|max:255',
            'butuh_penilaian' => 'required|boolean',
            'tampil_di_sertifikat' => 'required|boolean', // Validasi baru
            'nilai_teori' => 'nullable|integer',
            'nilai_praktik' => 'nullable|integer',
        ]);

        $urutan = (KegiatanMateri::where('kegiatan_id', $kegiatan_id)->max('urutan') ?? 0) + 1;

        $materi = KegiatanMateri::create([
            'kegiatan_id' => $kegiatan_id,
            'nama_materi' => $request->nama_materi,
            'butuh_penilaian' => $request->butuh_penilaian,
            'tampil_di_sertifikat' => $request->tampil_di_sertifikat, // Simpan status
            'nilai_teori' => $request->nilai_teori,
            'nilai_praktik' => $request->nilai_praktik,
            'urutan' => $urutan,
        ]);

        return redirect()->back()->with('success', 'Materi berhasil ditambahkan.')->with('open_materi', $materi->id);
    }
public function updateMateri(Request $request, $kegiatan_id, KegiatanMateri $materi)
    {
        $this->pastikanMilikKegiatan($materi->kegiatan_id, $kegiatan_id);

        $request->validate([
            'nama_materi' => 'required|string|max:255',
            'butuh_penilaian' => 'required|boolean',
            'tampil_di_sertifikat' => 'required|boolean', // Validasi baru
            'nilai_teori' => 'nullable|integer',
            'nilai_praktik' => 'nullable|integer',
            'fasilitator_ids' => 'nullable|array',
            'fasilitator_ids.*' => 'integer',
        ]);

        $materi->update([
            'nama_materi' => $request->nama_materi,
            'butuh_penilaian' => $request->butuh_penilaian,
            'tampil_di_sertifikat' => $request->tampil_di_sertifikat, // Update status
            'nilai_teori' => $request->nilai_teori,
            'nilai_praktik' => $request->nilai_praktik,
        ]);

        $idValid = KegiatanFasilitator::where('kegiatan_id', $kegiatan_id)->whereIn('id', $request->fasilitator_ids ?? [])->pluck('id')->all();
        $materi->fasilitator()->sync($idValid);

        return redirect()->back()->with('success', 'Materi "' . $materi->nama_materi . '" berhasil diperbarui.')->with('open_materi', $materi->id);
    }
    public function destroyMateri($kegiatan_id, KegiatanMateri $materi)
    {
        $this->pastikanMilikKegiatan($materi->kegiatan_id, $kegiatan_id);

        $materi->delete(); // item, pivot fasilitator & ttd ikut terhapus (cascade)

        return redirect()->back()->with('success', 'Materi berhasil dihapus beserta checklist-nya.');
    }

    // ----- Checklist item (per materi) -----
    public function storeItem(Request $request, $kegiatan_id, KegiatanMateri $materi)
    {
        $this->pastikanMilikKegiatan($materi->kegiatan_id, $kegiatan_id);

        $request->validate([
            'aspek_tindakan' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer', // Validasi urutan
        ]);

        $urutanTerakhir = ItemPenilaianSkill::where('materi_id', $materi->id)->max('urutan') ?? 0;
        
        // Jika admin mengosongkan urutan, otomatis lanjut dari angka terakhir + 1
        $urutan = $request->urutan ?? ($urutanTerakhir + 1);

        ItemPenilaianSkill::create([
            'kegiatan_id' => $kegiatan_id,
            'materi_id' => $materi->id,
            'kategori' => $request->kategori,
            'aspek_tindakan' => $request->aspek_tindakan,
            'urutan' => $urutan, // Simpan ke database
        ]);

        return redirect()->back()
            ->with('success', 'Item checklist berhasil ditambahkan.')
            ->with('open_materi', $materi->id);
    }

    public function updateItem(Request $request, $kegiatan_id, ItemPenilaianSkill $item)
    {
        $this->pastikanMilikKegiatan($item->kegiatan_id, $kegiatan_id);

        $request->validate([
            'aspek_tindakan' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'required|integer', // Validasi urutan saat update
        ]);

        $item->update([
            'aspek_tindakan' => $request->aspek_tindakan,
            'kategori' => $request->kategori,
            'urutan' => $request->urutan, // Update kolom urutan
        ]);

        return redirect()->back()
            ->with('success', 'Item checklist berhasil diperbarui.')
            ->with('open_materi', $item->materi_id);
    }

    public function destroyItem($kegiatan_id, ItemPenilaianSkill $item)
    {
        $this->pastikanMilikKegiatan($item->kegiatan_id, $kegiatan_id);

        $materiId = $item->materi_id;
        $item->delete();

        return redirect()->back()
            ->with('success', 'Item checklist berhasil dihapus.')
            ->with('open_materi', $materiId);
    }

    // ----- Fasilitator (master per kegiatan; penugasan ke materi diatur di form Materi) -----
    public function storeFasilitator(Request $request, $kegiatan_id)
    {
        $request->validate([
            'nama_fasilitator' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:30',
        ]);

        KegiatanFasilitator::create([
            'kegiatan_id' => $kegiatan_id,
            'nama_fasilitator' => $request->nama_fasilitator,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->back()->with('success', 'Fasilitator berhasil ditambahkan. Tugaskan ke materi lewat panel Materi di sebelah kanan.');
    }

    public function destroyFasilitator($kegiatan_id, KegiatanFasilitator $fasilitator)
    {
        $this->pastikanMilikKegiatan($fasilitator->kegiatan_id, $kegiatan_id);

        $fasilitator->delete();
        return redirect()->back()->with('success', 'Fasilitator berhasil dihapus.');
    }

    private function pastikanMilikKegiatan($kegiatanIdRecord, $kegiatan_id)
    {
        abort_if((int) $kegiatanIdRecord !== (int) $kegiatan_id, 404);
    }
}