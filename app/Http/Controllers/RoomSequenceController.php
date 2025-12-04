<?php

namespace App\Http\Controllers;

use App\Models\RoomSequence;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RoomSequenceController extends Controller
{
    public function index()
    {
        // Urutkan berdasarkan tanggal rencana dimulai
        $sequences = RoomSequence::with(['mahasiswa', 'ruangan'])
                    ->orderBy('start_date', 'asc')
                    ->get();

        return view('admin.room_sequences.index', compact('sequences'));
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::all();
        $ruangans = Ruangan::all();
        return view('admin.room_sequences.create', compact('mahasiswas', 'ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'ruangan_id'   => 'required|exists:ruangans,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
        ]);

        // VALIDASI ANTI-BENTROK
        // Cek apakah mahasiswa ini sudah punya rencana lain di rentang tanggal tersebut?
        $bentrok = RoomSequence::where('mahasiswa_id', $request->mahasiswa_id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_date', '<', $request->start_date)
                            ->where('end_date', '>', $request->end_date);
                      });
            })
            ->exists();

        if ($bentrok) {
            return back()->withErrors(['start_date' => 'Mahasiswa ini sudah memiliki jadwal di ruangan lain pada tanggal tersebut!'])->withInput();
        }

        RoomSequence::create($request->all());

        return redirect()->route('room_sequences.index')->with('success', 'Rencana jadwal berhasil disimpan.');
    }

    public function edit($id)
    {
        $sequence = RoomSequence::findOrFail($id);
        $mahasiswas = Mahasiswa::all();
        $ruangans = Ruangan::all();
        return view('admin.room_sequences.edit', compact('sequence', 'mahasiswas', 'ruangans'));
    }

public function update(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'ruangan_id'   => 'required|exists:ruangans,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
        ]);

        // VALIDASI ANTI-BENTROK (VERSI UPDATE)
        $bentrok = RoomSequence::where('mahasiswa_id', $request->mahasiswa_id)
            ->where('id', '!=', $id) // <--- PENTING! Abaikan data yang sedang diedit ini
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_date', '<', $request->start_date)
                            ->where('end_date', '>', $request->end_date);
                      });
            })
            ->exists();

        if ($bentrok) {
            return back()->withErrors(['start_date' => 'Mahasiswa ini sudah memiliki jadwal di ruangan lain pada tanggal tersebut!'])->withInput();
        }

        $sequence = RoomSequence::findOrFail($id);
        $sequence->update($request->all());

        return redirect()->route('room_sequences.index')->with('success', 'Rencana jadwal berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        RoomSequence::findOrFail($id)->delete();
        return back()->with('success', 'Dihapus.');
    }
}