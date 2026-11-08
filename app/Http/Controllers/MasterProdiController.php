<?php

namespace App\Http\Controllers;

use App\Models\MasterProdi;
use Illuminate\Http\Request;

class MasterProdiController extends Controller
{
public function index(Request $request)
{
    $q        = trim((string) $request->query('q', ''));
    $kategori = $request->query('kategori') ?: null;
    $jenjang  = $request->query('jenjang') ?: null;
    $status   = in_array($request->query('status'), ['0', '1'], true) ? $request->query('status') : null;
    $perPage  = in_array((int) $request->query('per_page'), [10, 25, 50, 100], true)
                    ? (int) $request->query('per_page') : 10;

    $prodis = MasterProdi::query()
        ->when($q !== '', function ($x) use ($q) {
            $x->where(function ($w) use ($q) {
                $w->where('nama_prodi', 'like', "%{$q}%")
                  ->orWhere('kategori', 'like', "%{$q}%");
            });
        })
        ->when($kategori, function ($x) use ($kategori) {
            $x->where('kategori', $kategori);
        })
        ->when($jenjang, function ($x) use ($jenjang) {
            $x->where('jenjang', $jenjang);
        })
        ->when($status !== null, function ($x) use ($status) {
            $x->where('aktif', (int) $status);
        })
        ->orderBy('kategori')->orderBy('nama_prodi')
        ->paginate($perPage)
        ->appends($request->query());

    $kategoris = MasterProdi::whereNotNull('kategori')->where('kategori', '!=', '')
        ->distinct()->orderBy('kategori')->pluck('kategori');
    $jenjangs = MasterProdi::whereNotNull('jenjang')->where('jenjang', '!=', '')
        ->distinct()->orderBy('jenjang')->pluck('jenjang');

    $stat = ['total' => MasterProdi::count(), 'aktif' => MasterProdi::where('aktif', 1)->count()];
    $stat['nonaktif'] = $stat['total'] - $stat['aktif'];

    return view('admin.master_prodi.index', compact(
        'prodis', 'kategoris', 'jenjangs', 'stat', 'q', 'kategori', 'jenjang', 'status', 'perPage'
    ));
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
