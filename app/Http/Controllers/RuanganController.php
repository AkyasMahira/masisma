<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Variabel harus 'ruangan' (plural) agar cocok dengan compact
        $ruangan = Ruangan::all();
        return view('ruangan.index', compact('ruangan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ruangan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nm_ruangan' => 'required|string|max:255|unique:ruangans',
            'kuota_ruangan' => 'required|integer|min:1',
        ], [
            'nm_ruangan.required' => 'Nama ruangan wajib diisi.',
            'nm_ruangan.unique' => 'Nama ruangan sudah ada.',
            'kuota_ruangan.required' => 'Kuota ruangan wajib diisi.',
            'kuota_ruangan.integer' => 'Kuota harus berupa angka.',
            'kuota_ruangan.min' => 'Kuota minimal adalah 1.',
        ]);

        // 2. Simpan data
        Ruangan::create([
            'nm_ruangan' => $request->nm_ruangan,
            'kuota_ruangan' => $request->kuota_ruangan,
        ]);

        // 3. Redirect
        return redirect()->route('ruangan.index')
                         ->with('success', 'Ruangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('ruangan.show', compact('ruangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        // Validasi data
        $validated = $request->validate([
            // PERBAIKAN: 'unique:ruangan' diubah menjadi 'unique:ruangans'
            'nm_ruangan' => 'required|string|max:255|unique:ruangans,nm_ruangan,' . $ruangan->id,
            'kuota_ruangan' => 'required|integer|min:1'
        ]);

        // Update data
        $ruangan->update($validated);

        return redirect()->route('ruangan.index')
            ->with('success', 'Ruangan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('ruangan.index')
            ->with('success', 'Ruangan berhasil dihapus!');
    }
}
