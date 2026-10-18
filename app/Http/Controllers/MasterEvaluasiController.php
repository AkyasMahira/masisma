<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\Kegiatan;
use App\Models\MasterEvaluasi;
use Illuminate\Http\Request;

/**
 * Master pertanyaan evaluasi per jenis (pelatihan / pendidikan / penelitian).
 * Pelatihan: pertanyaan default (kegiatan_id NULL) + pertanyaan khusus per pelatihan.
 */
class MasterEvaluasiController extends Controller
{
    private function konteks(Request $request)
    {
        $jenis = $request->get('jenis', 'pelatihan');
        if (!array_key_exists($jenis, MasterEvaluasi::JENIS)) $jenis = 'pelatihan';
        $kegiatan = ($jenis === 'pelatihan' && $request->filled('kegiatan_id')) ? Kegiatan::findOrFail($request->kegiatan_id) : null;
        return [$jenis, $kegiatan];
    }

    private function kembali($jenis, $kegiatan)
    {
        return redirect()->route('admin.master_evaluasi.index', array_filter(['jenis' => $jenis, 'kegiatan_id' => optional($kegiatan)->id]));
    }

    public function index(Request $request)
    {
        list($jenis, $kegiatan) = $this->konteks($request);
        $unsur = MasterEvaluasi::where('jenis', $jenis)
            ->when($kegiatan, function ($q) use ($kegiatan) { $q->where('kegiatan_id', $kegiatan->id); },
                             function ($q) { $q->whereNull('kegiatan_id'); })
            ->withCount('jawaban')->orderBy('urutan')->orderBy('id')->get();

        // Pelatihan yang punya pertanyaan khusus
        $kegiatanKhusus = MasterEvaluasi::where('jenis', 'pelatihan')->whereNotNull('kegiatan_id')->where('aktif', true)
            ->selectRaw('kegiatan_id, COUNT(*) as n')->groupBy('kegiatan_id')->pluck('n', 'kegiatan_id');
        $daftarKegiatan = Kegiatan::orderBy('tanggal_mulai', 'desc')->get(['id', 'nama_kegiatan', 'tanggal_mulai']);
        $linkForm = $kegiatan ? route('evaluasi.pelatihan.form', [$kegiatan->id, Evaluasi::token('pelatihan', $kegiatan->id)]) : null;

        return view('admin.master_evaluasi.index', compact('unsur', 'jenis', 'kegiatan', 'kegiatanKhusus', 'daftarKegiatan', 'linkForm'));
    }

    public function create(Request $request)
    {
        list($jenis, $kegiatan) = $this->konteks($request);
        return view('admin.master_evaluasi.create', compact('jenis', 'kegiatan'));
    }

    private function rules()
    {
        return [
            'kode'       => 'nullable|string|max:20',
            'pertanyaan' => 'required|string',
            'tipe'       => 'required|in:rating,text',
            'urutan'     => 'nullable|integer|min:0',
            'aktif'      => 'nullable|boolean',
        ];
    }

    public function store(Request $request)
    {
        list($jenis, $kegiatan) = $this->konteks($request);
        $data = $request->validate($this->rules(), ['pertanyaan.required' => 'Pertanyaan tidak boleh kosong.']);

        MasterEvaluasi::create([
            'jenis'       => $jenis,
            'kegiatan_id' => optional($kegiatan)->id,
            'kode'        => $data['kode'] ?? null,
            'pertanyaan'  => $data['pertanyaan'],
            'tipe'        => $data['tipe'],
            'urutan'      => $data['urutan'] ?? 0,
            'aktif'       => $request->boolean('aktif', true),
        ]);

        return $this->kembali($jenis, $kegiatan)->with('success', 'Unsur evaluasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $unsur = MasterEvaluasi::findOrFail($id);
        $jenis = $unsur->jenis;
        $kegiatan = $unsur->kegiatan;
        return view('admin.master_evaluasi.edit', compact('unsur', 'jenis', 'kegiatan'));
    }

    public function update(Request $request, $id)
    {
        $unsur = MasterEvaluasi::findOrFail($id);
        $data = $request->validate($this->rules());

        $unsur->update([
            'kode'       => $data['kode'] ?? null,
            'pertanyaan' => $data['pertanyaan'],
            'tipe'       => $data['tipe'],
            'urutan'     => $data['urutan'] ?? 0,
            'aktif'      => $request->boolean('aktif', false),
        ]);

        return $this->kembali($unsur->jenis, $unsur->kegiatan)->with('success', 'Unsur evaluasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unsur = MasterEvaluasi::findOrFail($id);
        $jenis = $unsur->jenis; $kegiatan = $unsur->kegiatan;
        // Unsur yang sudah dijawab tidak dihapus agar jawaban & IKM lama tetap utuh -> dinonaktifkan
        if ($unsur->jawaban()->exists()) {
            $unsur->update(['aktif' => false]);
            return $this->kembali($jenis, $kegiatan)->with('success', 'Unsur sudah punya jawaban, jadi dinonaktifkan (tidak dihapus) agar data IKM lama tetap utuh.');
        }
        $unsur->delete();
        return $this->kembali($jenis, $kegiatan)->with('success', 'Unsur evaluasi berhasil dihapus.');
    }

    /** Pelatihan: salin pertanyaan default menjadi pertanyaan khusus pelatihan ini (lalu bisa diubah). */
    public function salinDefault($kegiatanId)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatanId);
        if (MasterEvaluasi::where('jenis', 'pelatihan')->where('kegiatan_id', $kegiatan->id)->where('aktif', true)->exists()) {
            return $this->kembali('pelatihan', $kegiatan)->with('success', 'Pelatihan ini sudah punya pertanyaan khusus.');
        }
        $default = MasterEvaluasi::where('jenis', 'pelatihan')->whereNull('kegiatan_id')->aktif()->orderBy('urutan')->get();
        foreach ($default as $u) {
            MasterEvaluasi::create([
                'jenis' => 'pelatihan', 'kegiatan_id' => $kegiatan->id, 'kode' => $u->kode,
                'pertanyaan' => $u->pertanyaan, 'tipe' => $u->tipe, 'urutan' => $u->urutan, 'aktif' => true,
            ]);
        }
        return $this->kembali('pelatihan', $kegiatan)->with('success', count($default) . ' pertanyaan default disalin. Silakan ubah / tambah sesuai pelatihan ini.');
    }

    /** Pelatihan: kembali memakai pertanyaan default (pertanyaan khusus yang belum dijawab dihapus, sisanya dinonaktifkan). */
    public function pakaiDefault($kegiatanId)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatanId);
        $hapus = 0; $nonaktif = 0;
        foreach (MasterEvaluasi::where('jenis', 'pelatihan')->where('kegiatan_id', $kegiatan->id)->get() as $u) {
            if ($u->jawaban()->exists()) { $u->update(['aktif' => false]); $nonaktif++; }
            else { $u->delete(); $hapus++; }
        }
        return redirect()->route('admin.master_evaluasi.index', ['jenis' => 'pelatihan'])
            ->with('success', "Pelatihan \"{$kegiatan->nama_kegiatan}\" kembali memakai pertanyaan default ({$hapus} dihapus, {$nonaktif} dinonaktifkan karena sudah dijawab).");
    }
}
