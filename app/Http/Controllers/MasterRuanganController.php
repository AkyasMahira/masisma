<?php

namespace App\Http\Controllers;

use App\Models\MasterRuangan;
use App\Models\MasterInstansi;
use Illuminate\Http\Request;

class MasterRuanganController extends Controller
{
    public function index()
    {
        // Tarik data ruangan beserta nama instansinya (Relasi Eager Loading)
        $ruangan = MasterRuangan::with('instansi')->orderBy('id', 'desc')->get();
        return view('admin.master_ruangan.index', compact('ruangan'));
    }

    public function create()
    {
        $instansi = MasterInstansi::orderBy('nama_instansi', 'asc')->get();
        return view('admin.master_ruangan.create', compact('instansi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instansi_id' => 'required|exists:master_instansi,id',
            'nama_ruangan' => 'required|string|max:255',
        ], [
            'instansi_id.required' => 'Asal Instansi harus dipilih.',
            'nama_ruangan.required' => 'Nama ruangan/unit tidak boleh kosong.',
        ]);

        MasterRuangan::create([
            'instansi_id' => $request->instansi_id,
            'nama_ruangan' => $request->nama_ruangan,
        ]);

        return redirect()->route('admin.master_ruangan.index')->with('success', 'Data Ruangan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $ruangan = MasterRuangan::findOrFail($id);
        $instansi = MasterInstansi::orderBy('nama_instansi', 'asc')->get();
        return view('admin.master_ruangan.edit', compact('ruangan', 'instansi'));
    }

    public function update(Request $request, $id)
    {
        $ruangan = MasterRuangan::findOrFail($id);

        $request->validate([
            'instansi_id' => 'required|exists:master_instansi,id',
            'nama_ruangan' => 'required|string|max:255',
        ]);

        $ruangan->update([
            'instansi_id' => $request->instansi_id,
            'nama_ruangan' => $request->nama_ruangan,
        ]);

        return redirect()->route('admin.master_ruangan.index')->with('success', 'Data Ruangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MasterRuangan::findOrFail($id)->delete();
        return redirect()->route('admin.master_ruangan.index')->with('success', 'Data Ruangan berhasil dihapus.');
    }

    // Fitur Import Canggih: Otomatis deteksi & hubungkan Instansi
    public function import(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.nama_instansi' => 'required|string',
            'data.*.nama_ruangan' => 'required|string',
        ]);

        $inserted = 0;
        foreach ($request->data as $row) {
            // Cari Instansi, kalau tidak ada, buat otomatis
            $instansi = MasterInstansi::firstOrCreate([
                'nama_instansi' => trim($row['nama_instansi'])
            ]);

            // Masukkan Ruangan
            $ruangan = MasterRuangan::firstOrCreate([
                'instansi_id' => $instansi->id,
                'nama_ruangan' => trim($row['nama_ruangan'])
            ]);

            if ($ruangan->wasRecentlyCreated) {
                $inserted++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor $inserted data ruangan baru."
        ]);
    }
}