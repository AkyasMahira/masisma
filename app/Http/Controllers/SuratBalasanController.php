<?php

namespace App\Http\Controllers;

use App\Models\SuratBalasan;
use App\Models\Mou;
use App\Models\MahasiswaPenelitian;
use Illuminate\Http\Request;
use PDF;

class SuratBalasanController extends Controller
{
    /* ============================================================
        INDEX + SEARCH
    ============================================================ */
    public function index(Request $request)
    {
        $search = $request->search;

        $data = SuratBalasan::with(['mou', 'mahasiswaPenelitian'])
            ->when($search, function ($q) use ($search) {
                $q->where('nama_mahasiswa', 'like', "%$search%")
                  ->orWhere('nim', 'like', "%$search%")
                  ->orWhere('prodi', 'like', "%$search%")
                  ->orWhere('keperluan', 'like', "%$search%")
                  ->orWhere('data_dibutuhkan', 'like', "%$search%");
            })
            ->latest()
            ->paginate(10);

        return view('surat_balasan.index', compact('data'));
    }

    /* ============================================================
        CREATE
    ============================================================ */
    public function create()
    {
        $mous = Mou::all();
        $mahasiswa = MahasiswaPenelitian::all();

        return view('surat_balasan.create', compact('mous', 'mahasiswa'));
    }

    /* ============================================================
        STORE
    ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'mou_id' => 'required',
            'nama_mahasiswa' => 'required|string',
            'nim' => 'required|string',
            'wa_mahasiswa' => 'required|string',
            'keperluan' => 'required|string',
            'prodi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'data_dibutuhkan' => 'required|array', // <--- ARRAY
        ]);

        // Format tanggal
        $request->merge([
            'lama_berlaku' => $request->tanggal_mulai . ' s/d ' . $request->tanggal_selesai,
            'data_dibutuhkan' => json_encode($request->data_dibutuhkan), // <--- SIMPAN JSON
        ]);

        SuratBalasan::create($request->except(['tanggal_mulai', 'tanggal_selesai']));

        return redirect()->route('surat-balasan.index')
            ->with('success', 'Surat Balasan berhasil ditambahkan.');
    }

    /* ============================================================
        EDIT
    ============================================================ */
    public function edit(SuratBalasan $suratBalasan)
    {
        $mous = Mou::all();
        $mahasiswa = MahasiswaPenelitian::all();

        // Decode JSON agar bisa ditampilkan sebagai array
        $suratBalasan->data_dibutuhkan = json_decode($suratBalasan->data_dibutuhkan, true);

        return view('surat_balasan.edit', compact('suratBalasan', 'mous', 'mahasiswa'));
    }

    /* ============================================================
        UPDATE
    ============================================================ */
    public function update(Request $request, SuratBalasan $suratBalasan)
    {
        $request->validate([
            'mou_id' => 'required',
            'nama_mahasiswa' => 'required|string',
            'nim' => 'required|string',
            'wa_mahasiswa' => 'required|string',
            'keperluan' => 'required|string',
            'prodi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'data_dibutuhkan' => 'required|array', // <--- ARRAY
        ]);

        $request->merge([
            'lama_berlaku' => $request->tanggal_mulai . ' s/d ' . $request->tanggal_selesai,
            'data_dibutuhkan' => json_encode($request->data_dibutuhkan), // <--- UPDATE JSON
        ]);

        $suratBalasan->update($request->except(['tanggal_mulai', 'tanggal_selesai']));

        return redirect()->route('surat-balasan.index')
            ->with('success', 'Surat Balasan berhasil diperbarui.');
    }

    /* ============================================================
        DELETE
    ============================================================ */
    public function destroy(SuratBalasan $suratBalasan)
    {
        $suratBalasan->delete();

        return redirect()->route('surat-balasan.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    /* ============================================================
        PDF
    ============================================================ */
    public function generatePdf($id)
    {
        $data = SuratBalasan::with(['mou', 'mahasiswaPenelitian'])->findOrFail($id);

        $pdf = PDF::loadView('surat_balasan.pdf', compact('data'));

        return $pdf->download('surat-balasan-' . str_pad($data->id, 3, '0', STR_PAD_LEFT) . '.pdf');
    }
}
