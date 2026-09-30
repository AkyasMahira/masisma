<?php

namespace App\Http\Controllers;

use App\Models\MasterKompetensi;
use Illuminate\Http\Request;

class MasterKompetensiController extends Controller
{
    public function index()
    {
        $kompetensi = MasterKompetensi::orderBy('id', 'desc')->get();
        return view('admin.master_kompetensi.index', compact('kompetensi'));
    }

    public function create()
    {
        return view('admin.master_kompetensi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kompetensi' => 'required|string|max:255',
            'deskripsi_default' => 'nullable|string',
        ], [
            'nama_kompetensi.required' => 'Nama kompetensi tidak boleh kosong.',
        ]);

        MasterKompetensi::create([
            'nama_kompetensi' => $request->nama_kompetensi,
            'deskripsi_default' => $request->deskripsi_default,
        ]);

        return redirect()->route('admin.master_kompetensi.index')->with('success', 'Data Kompetensi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kompetensi = MasterKompetensi::findOrFail($id);
        return view('admin.master_kompetensi.edit', compact('kompetensi'));
    }

    public function update(Request $request, $id)
    {
        $kompetensi = MasterKompetensi::findOrFail($id);

        $request->validate([
            'nama_kompetensi' => 'required|string|max:255',
            'deskripsi_default' => 'nullable|string',
        ]);

        $kompetensi->update([
            'nama_kompetensi' => $request->nama_kompetensi,
            'deskripsi_default' => $request->deskripsi_default,
        ]);

        return redirect()->route('admin.master_kompetensi.index')->with('success', 'Data Kompetensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MasterKompetensi::findOrFail($id)->delete();
        return redirect()->route('admin.master_kompetensi.index')->with('success', 'Data Kompetensi berhasil dihapus.');
    }

    // Fitur Import Canggih: Otomatis deteksi & buat baru
    public function import(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.nama_kompetensi' => 'required|string',
        ]);

        $inserted = 0;
        foreach ($request->data as $row) {
            $kompetensi = MasterKompetensi::firstOrCreate([
                'nama_kompetensi' => trim($row['nama_kompetensi'])
            ], [
                'deskripsi_default' => isset($row['deskripsi_default']) ? trim($row['deskripsi_default']) : null
            ]);

            if ($kompetensi->wasRecentlyCreated) {
                $inserted++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor $inserted data kompetensi baru."
        ]);
    }
}