<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\RoomShift;

class RoomShiftController extends Controller
{
    // Pastikan hanya admin yang bisa akses
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    // Tampilkan halaman daftar shift per ruangan
    public function index($ruangan_id)
    {
        $ruangan = Ruangan::with('roomShifts')->findOrFail($ruangan_id);
        return view('ruangan.shifts', compact('ruangan'));
    }

    // Simpan shift baru
    public function store(Request $request, $ruangan_id)
    {
        $request->validate([
            'nama_shift'  => 'required|string|max:255',
            'jam_masuk'   => 'required',
            'jam_keluar'  => 'required',
        ]);

        // Cek duplikat nama shift di ruangan yang sama
        $exists = RoomShift::where('ruangan_id', $ruangan_id)
                           ->where('nama_shift', $request->nama_shift)
                           ->exists();

        if ($exists) {
            return back()->with('error', 'Nama shift tersebut sudah ada di ruangan ini.');
        }

        RoomShift::create([
            'ruangan_id'  => $ruangan_id,
            'nama_shift'  => $request->nama_shift,
            'jam_masuk'   => $request->jam_masuk,
            'jam_keluar'  => $request->jam_keluar,
            // Jika checkbox dicentang, nilainya true. Jika tidak, false.
            'lintas_hari' => $request->has('lintas_hari') ? true : false, 
        ]);

        return back()->with('success', 'Jam Kerja/Shift berhasil ditambahkan!');
    }

    // Hapus shift
    public function destroy($id)
    {
        $shift = RoomShift::findOrFail($id);
        $shift->delete();
        return back()->with('success', 'Jam Kerja/Shift berhasil dihapus!');
    }
}