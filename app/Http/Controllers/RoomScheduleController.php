<?php
namespace App\Http\Controllers;

use App\Models\RoomSchedule;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RoomScheduleController extends Controller
{
    // List all schedules
    public function index()
    {
        $schedules = RoomSchedule::with(['mahasiswa', 'ruangan'])->orderBy('start_date')->get();
        return view('admin.room_schedules.index', compact('schedules'));
    }

    // Show create form
    public function create()
    {
        $mahasiswas = Mahasiswa::all();
        $ruangans = Ruangan::all();
        return view('admin.room_schedules.create', compact('mahasiswas', 'ruangans'));
    }

    // Store new schedule
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        RoomSchedule::create($request->all());
        return redirect()->route('admin.room_schedules.index')->with('success', 'Jadwal berhasil dibuat');
    }

    // Show edit form
    public function edit($id)
    {
        $schedule = RoomSchedule::findOrFail($id);
        $mahasiswas = Mahasiswa::all();
        $ruangans = Ruangan::all();
        return view('admin.room_schedules.edit', compact('schedule', 'mahasiswas', 'ruangans'));
    }

    // Update schedule
    public function update(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $schedule = RoomSchedule::findOrFail($id);
        $schedule->update($request->all());
        return redirect()->route('admin.room_schedules.index')->with('success', 'Jadwal berhasil diupdate');
    }

    // Delete schedule
    public function destroy($id)
    {
        $schedule = RoomSchedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('admin.room_schedules.index')->with('success', 'Jadwal berhasil dihapus');
    }
}
