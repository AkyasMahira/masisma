<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // public card (no auth) to show mahasiswa info and buttons
    public function card($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();
        return view('absensi.card', compact('mahasiswa'));
    }

    public function masuk(Request $request, $token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'type' => 'masuk',
        ]);

        return redirect()->back()->with('success', 'Absensi masuk tercatat.');
    }

    public function keluar(Request $request, $token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'type' => 'keluar',
        ]);

        return redirect()->back()->with('success', 'Absensi keluar tercatat.');
    }
}
