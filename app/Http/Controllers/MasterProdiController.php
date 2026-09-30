<?php

namespace App\Http\Controllers;

use App\Models\MasterProdi;
use Illuminate\Http\Request;

class MasterProdiController extends Controller
{
    public function index()
    {
        $prodis = MasterProdi::orderBy('kategori')->orderBy('nama_prodi')->get();
        return view('admin.master_prodi.index', compact('prodis'));
    }

    public function create()
    {
        return view('admin.master_prodi.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        MasterProdi::create($data);
        return redirect()->route('admin.master_prodi.index')->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $prodi = MasterProdi::findOrFail($id);
        return view('admin.master_prodi.edit', compact('prodi'));
    }

    public function update(Request $request, $id)
    {
        $prodi = MasterProdi::findOrFail($id);
        $prodi->update($this->validated($request));
        return redirect()->route('admin.master_prodi.index')->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MasterProdi::findOrFail($id)->delete();
        return redirect()->route('admin.master_prodi.index')->with('success', 'Program studi dihapus.');
    }

    private function validated(Request $request)
    {
        $data = $request->validate([
            'kategori'   => 'nullable|string|max:255',
            'nama_prodi' => 'required|string|max:255',
            'jenjang'    => 'nullable|string|max:20',
            'aktif'      => 'nullable|boolean',
        ], [
            'nama_prodi.required' => 'Nama program studi wajib diisi.',
        ]);
        $data['nama_prodi'] = strtoupper(trim($data['nama_prodi']));
        $data['jenjang'] = $data['jenjang'] ? strtoupper(trim($data['jenjang'])) : null;
        $data['aktif'] = $request->boolean('aktif', true);
        return $data;
    }
}
