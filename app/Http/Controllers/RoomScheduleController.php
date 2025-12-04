<?php

namespace App\Http\Controllers;

use App\Models\RoomSchedule;
use Illuminate\Http\Request;

class RoomScheduleController extends Controller
{
    public function index()
    {
        // Urutkan berdasarkan tanggal mulai terbaru
        $schedules = RoomSchedule::with(['mahasiswa', 'ruangan'])
                                 ->orderBy('start_date', 'desc')
                                 ->get();
                                 
        return view('admin.room_schedules.index', compact('schedules'));
    }

    public function destroy($id)
    {
        RoomSchedule::findOrFail($id)->delete();
        return back()->with('success', 'Data hunian berhasil dihapus.');
    }
}