<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;

class RuanganController extends Controller
{
    public function __construct()
    {
        // Restrict Ruangan CRUD to admin users only
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ruangan::with('mahasiswa')
            ->withCount('mahasiswa');

        // Fitur pencarian
        if ($request->filled('search')) {
            $query->where('nm_ruangan', 'like', '%' . $request->search . '%');
        }
        $ruangan = $query->paginate(6)->appends($request->query());

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
        // Handle Excel import
        if ($request->has('data')) {
            try {
                $data = json_decode($request->data, true);
                $success = 0;
                $errors = [];

                foreach ($data as $row) {
                    // Validate each row
                    if (empty($row['Nama Ruangan']) || empty($row['Kuota Ruangan'])) {
                        continue;
                    }

                    try {
                        Ruangan::create([
                            'nm_ruangan' => $row['Nama Ruangan'],
                            'kuota_ruangan' => (int)$row['Kuota Ruangan']
                        ]);
                        $success++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$row['Nama Ruangan']}: " . $e->getMessage();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil import $success data ruangan",
                    'errors' => $errors
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }

        // Handle normal form submission
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

        Ruangan::create([
            'nm_ruangan' => $request->nm_ruangan,
            'kuota_ruangan' => $request->kuota_ruangan,
        ]);

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
