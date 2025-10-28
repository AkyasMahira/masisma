<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function __construct()
    {
        // Restrict Mahasiswa CRUD to admin users only
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mahasiswas = Mahasiswa::orderBy('created_at', 'desc')->get();
        return view('mahasiswa.index', compact('mahasiswas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ruangans = Ruangan::all();
        return view('mahasiswa.create', compact('ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nm_mahasiswa' => 'required|string|max:255',
            'univ_asal' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'nm_ruangan' => 'nullable|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // handle ruangan quota if ruangan_id provided
        if (!empty($data['ruangan_id'])) {
            $ruangan = Ruangan::find($data['ruangan_id']);
            if (!$ruangan) {
                return redirect()->back()->withErrors(['ruangan_id' => 'Ruangan tidak ditemukan'])->withInput();
            }
            if ($ruangan->kuota_ruangan <= 0) {
                return redirect()->back()->withErrors(['ruangan_id' => 'Kuota ruangan penuh'])->withInput();
            }
            $ruangan->decrement('kuota_ruangan');
            $data['nm_ruangan'] = $ruangan->nm_ruangan;
        }

        Mahasiswa::create($data);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $today = now()->toDateString();
        $last = $mahasiswa->absensis()->whereDate('created_at', $today)->latest()->first();
        $lastStatus = $last ? $last->type : null;
        return view('mahasiswa.show', compact('mahasiswa', 'lastStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $ruangans = Ruangan::all();
        return view('mahasiswa.edit', compact('mahasiswa', 'ruangans'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $data = $request->validate([
            'nm_mahasiswa' => 'required|string|max:255',
            'univ_asal' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'nm_ruangan' => 'nullable|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $newRuanganId = $data['ruangan_id'] ?? null;
        $oldRuanganId = $mahasiswa->ruangan_id;

        if ($newRuanganId && $newRuanganId != $oldRuanganId) {
            $new = Ruangan::find($newRuanganId);
            if ($new->kuota_ruangan <= 0) {
                return redirect()->back()->withErrors(['ruangan_id' => 'Kuota ruangan penuh'])->withInput();
            }
            $new->decrement('kuota_ruangan');
            $data['nm_ruangan'] = $new->nm_ruangan;

            if ($oldRuanganId) {
                $old = Ruangan::find($oldRuanganId);
                if ($old) $old->increment('kuota_ruangan');
            }
        }

        if (!$newRuanganId && $oldRuanganId) {
            $old = Ruangan::find($oldRuanganId);
            if ($old) $old->increment('kuota_ruangan');
        }

        $mahasiswa->update($data);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        if ($mahasiswa->ruangan_id) {
            $ruangan = Ruangan::find($mahasiswa->ruangan_id);
            if ($ruangan) $ruangan->increment('kuota_ruangan');
        }

        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
