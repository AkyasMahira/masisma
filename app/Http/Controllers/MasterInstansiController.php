<?php

namespace App\Http\Controllers;

use App\Models\MasterInstansi;
use Illuminate\Http\Request;

class MasterInstansiController extends Controller
{
    public function index()
    {
        $instansi = MasterInstansi::orderBy('id', 'desc')->get();
        return view('admin.master_instansi.index', compact('instansi'));
    }

    public function create()
    {
        return view('admin.master_instansi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:255|unique:master_instansi,nama_instansi',
        ], [
            'nama_instansi.required' => 'Nama instansi tidak boleh kosong.',
            'nama_instansi.unique' => 'Nama instansi ini sudah terdaftar.',
        ]);

        MasterInstansi::create([
            'nama_instansi' => $request->nama_instansi,
        ]);

        return redirect()->route('admin.master_instansi.index')->with('success', 'Data Instansi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $instansi = MasterInstansi::findOrFail($id);
        return view('admin.master_instansi.edit', compact('instansi'));
    }
public function import(Request $request)
{
    $request->validate([
        'data' => 'required|array',
        'data.*.nama_instansi' => 'required|string|max:255'
    ]);

    $inserted = 0;
    foreach ($request->data as $row) {
        // firstOrCreate otomatis mencegah duplikasi jika nama instansi sudah ada
        $instansi = MasterInstansi::firstOrCreate([
            'nama_instansi' => trim($row['nama_instansi'])
        ]);
        
        if ($instansi->wasRecentlyCreated) {
            $inserted++;
        }
    }

    return response()->json([
        'success' => true,
        'message' => "Berhasil mengimpor $inserted data instansi baru."
    ]);
}
    public function update(Request $request, $id)
    {
        $instansi = MasterInstansi::findOrFail($id);

        $request->validate([
            'nama_instansi' => 'required|string|max:255|unique:master_instansi,nama_instansi,' . $id,
        ], [
            'nama_instansi.required' => 'Nama instansi tidak boleh kosong.',
            'nama_instansi.unique' => 'Nama instansi ini sudah terdaftar.',
        ]);

        $instansi->update([
            'nama_instansi' => $request->nama_instansi,
        ]);

        return redirect()->route('admin.master_instansi.index')->with('success', 'Data Instansi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $instansi = MasterInstansi::findOrFail($id);
        $instansi->delete();

        return redirect()->route('admin.master_instansi.index')->with('success', 'Data Instansi berhasil dihapus.');
    }
}