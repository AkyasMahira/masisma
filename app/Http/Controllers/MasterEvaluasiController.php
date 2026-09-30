<?php

namespace App\Http\Controllers;

use App\Models\MasterEvaluasi;
use Illuminate\Http\Request;

class MasterEvaluasiController extends Controller
{
    public function index()
    {
        $unsur = MasterEvaluasi::orderBy('urutan')->orderBy('id')->get();
        return view('admin.master_evaluasi.index', compact('unsur'));
    }

    public function create()
    {
        return view('admin.master_evaluasi.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'       => 'nullable|string|max:20',
            'pertanyaan' => 'required|string',
            'tipe'       => 'required|in:rating,text',
            'urutan'     => 'nullable|integer|min:0',
            'aktif'      => 'nullable|boolean',
        ], [
            'pertanyaan.required' => 'Pertanyaan tidak boleh kosong.',
        ]);

        MasterEvaluasi::create([
            'kode'       => $data['kode'] ?? null,
            'pertanyaan' => $data['pertanyaan'],
            'tipe'       => $data['tipe'],
            'urutan'     => $data['urutan'] ?? 0,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.master_evaluasi.index')->with('success', 'Unsur evaluasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $unsur = MasterEvaluasi::findOrFail($id);
        return view('admin.master_evaluasi.edit', compact('unsur'));
    }

    public function update(Request $request, $id)
    {
        $unsur = MasterEvaluasi::findOrFail($id);

        $data = $request->validate([
            'kode'       => 'nullable|string|max:20',
            'pertanyaan' => 'required|string',
            'tipe'       => 'required|in:rating,text',
            'urutan'     => 'nullable|integer|min:0',
            'aktif'      => 'nullable|boolean',
        ]);

        $unsur->update([
            'kode'       => $data['kode'] ?? null,
            'pertanyaan' => $data['pertanyaan'],
            'tipe'       => $data['tipe'],
            'urutan'     => $data['urutan'] ?? 0,
            'aktif'      => $request->boolean('aktif', false),
        ]);

        return redirect()->route('admin.master_evaluasi.index')->with('success', 'Unsur evaluasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unsur = MasterEvaluasi::findOrFail($id);
        $unsur->delete();
        return redirect()->route('admin.master_evaluasi.index')->with('success', 'Unsur evaluasi berhasil dihapus.');
    }
}
