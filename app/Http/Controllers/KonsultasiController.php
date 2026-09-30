<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use App\Models\PraPenelitian;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KonsultasiController extends Controller
{
    /**
     * Tampilkan halaman konsultasi
     */
    public function index()
    {
        // 1. Ambil data pra penelitian TERBARU milik user
        $praPenelitian = PraPenelitian::where('user_id', auth()->id())
            ->latest() 
            ->firstOrFail();

        // 2. Ambil data pengajuan terakhir (untuk cek CI & Ruangan)
        $pengajuan = Pengajuan::with(['ci', 'dataRuangan'])
            ->where('user_id', auth()->id())
            ->where('jenis', 'pra_penelitian')
            ->latest()
            ->first();

        // 3. Validasi: Jika belum di-assign CI oleh admin, lempar balik
        if (!$pengajuan || !$pengajuan->ci_id) { 
            return redirect()->route('pengajuan.index')
                ->with('error', 'Anda belum mendapatkan Pembimbing Lapangan (CI). Silakan hubungi admin.');
        }

        // 4. Ambil history konsultasi khusus untuk pengajuan terbaru ini saja
        $konsultasi = Konsultasi::where('pra_penelitian_id', $praPenelitian->id)
            ->orderBy('tanggal_konsul', 'desc')
            ->get();

        $totalKonsul = $konsultasi->count();
        $minKonsul = 2; // Target minimal bimbingan

        return view('konsultasi.index', compact(
            'praPenelitian', 
            'pengajuan', 
            'konsultasi', 
            'totalKonsul', 
            'minKonsul'
        ));
    }

    /**
     * Simpan hasil konsultasi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_konsul' => 'required|date',
            'hasil_konsul' => 'required|string|min:10',
        ], [
            'hasil_konsul.min' => 'Catatan konsultasi terlalu pendek, minimal 10 karakter.'
        ]);

        // Cari ID Pra Penelitian terbaru untuk dikaitkan dengan konsultasi ini
        $praPenelitian = PraPenelitian::where('user_id', auth()->id())
            ->latest()
            ->firstOrFail();

        Konsultasi::create([
            'pra_penelitian_id' => $praPenelitian->id,
            'user_id'           => auth()->id(),
            'tanggal_konsul'    => $request->tanggal_konsul,
            'hasil_konsul'      => $request->hasil_konsul,
        ]);

        return back()->with('success', 'Hasil konsultasi berhasil disimpan ke sistem!');
    }

    /**
     * Edit konsultasi (Halaman Edit)
     */
    public function edit($id)
    {
        // Pastikan data yang diedit adalah milik user yang sedang login
        $konsultasi = Konsultasi::where('user_id', auth()->id())->findOrFail($id);
        
        // Ambil konteks data terbaru agar sidebar/info tetap sinkron
        $praPenelitian = PraPenelitian::where('user_id', auth()->id())->latest()->firstOrFail();
        
        $pengajuan = Pengajuan::with(['ci', 'dataRuangan'])
            ->where('user_id', auth()->id())
            ->where('jenis', 'pra_penelitian')
            ->latest()
            ->first();

        // Data history untuk ditampilkan sebagai referensi di halaman edit jika perlu
        $allKonsultasi = Konsultasi::where('pra_penelitian_id', $praPenelitian->id)
            ->orderBy('tanggal_konsul', 'desc')
            ->get();

        $totalKonsul = $allKonsultasi->count();
        $minKonsul = 2;

        return view('konsultasi.edit', compact(
            'konsultasi', 
            'praPenelitian', 
            'pengajuan', 
            'allKonsultasi', 
            'totalKonsul', 
            'minKonsul'
        ));
    }

    /**
     * Update data konsultasi
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_konsul' => 'required|date',
            'hasil_konsul'   => 'required|string|min:10',
        ]);

        $konsultasi = Konsultasi::where('user_id', auth()->id())->findOrFail($id);

        $konsultasi->update([
            'tanggal_konsul' => $request->tanggal_konsul,
            'hasil_konsul'   => $request->hasil_konsul,
        ]);

        return redirect()->route('konsultasi.index')->with('success', 'Catatan konsultasi berhasil diperbarui!');
    }

    /**
     * Hapus data konsultasi
     */
    public function destroy($id)
    {
        $konsultasi = Konsultasi::where('user_id', auth()->id())->findOrFail($id);
        $konsultasi->delete();

        return back()->with('success', 'Catatan konsultasi telah dihapus.');
    }
}