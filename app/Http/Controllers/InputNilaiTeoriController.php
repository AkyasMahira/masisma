<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use App\Models\NilaiTeori;
use App\Models\MasterInstansi;
use Illuminate\Http\Request;

class InputNilaiTeoriController extends Controller
{
    // TAHAP 2 (bagian Tim Diklat): input nilai Pre-Test & Post-Test
    public function index(Request $request, $kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);

        $query = KegiatanPeserta::with(['instansi', 'nilaiTeori'])
            ->where('kegiatan_id', $kegiatan_id);

        if ($request->search) {
            $query->where('nama_lengkap_gelar', 'like', '%' . $request->search . '%');
        }
        if ($request->instansi_id) {
            $query->where('instansi_id', $request->instansi_id);
        }

        $peserta = $query->orderBy('nama_lengkap_gelar', 'asc')->paginate(15)->withQueryString();

        // Data lengkap (tanpa paginate) khusus untuk export Excel sesuai filter yang aktif
        $semuaPesertaFilter = (clone $query)->orderBy('nama_lengkap_gelar', 'asc')->get();

        $instansi = MasterInstansi::orderBy('nama_instansi', 'asc')->get();

        return view('admin.kegiatan.penilaian.nilai_teori.index', compact(
            'kegiatan', 'peserta', 'instansi', 'semuaPesertaFilter'
        ));
    }

    public function updateNilai(Request $request, $kegiatan_id, $peserta_id)
    {
        $request->validate([
            'nilai_pretest' => 'nullable|numeric|min:0|max:100',
            'nilai_posttest' => 'nullable|numeric|min:0|max:100',
        ]);

        NilaiTeori::updateOrCreate(
            ['kegiatan_peserta_id' => $peserta_id],
            [
                'nilai_pretest' => $request->nilai_pretest,
                'nilai_posttest' => $request->nilai_posttest,
            ]
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Nilai tersimpan.']);
        }

        return redirect()->back()->with('success', 'Nilai teori berhasil disimpan.');
    }
}