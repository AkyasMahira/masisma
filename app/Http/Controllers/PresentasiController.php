<?php

namespace App\Http\Controllers;

use App\Models\Presentasi;
use App\Models\PraPenelitian;
use App\Models\Pengajuan;
use App\Models\Konsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PresentasiController extends Controller
{
    /**
     * Admin: Set Jadwal Presentasi
     */
    public function create($pengajuanId)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($pengajuanId);

        // ✅ Load pra penelitian dengan anggota
        $praPenelitian = PraPenelitian::with(['anggotas', 'mou'])
            ->where('user_id', $pengajuan->user_id)
            ->first();

        if (!$praPenelitian) {
            return back()->with('error', 'Data pra penelitian tidak ditemukan.');
        }

        $totalKonsul = Konsultasi::where('pra_penelitian_id', $praPenelitian->id)->count();

        if ($totalKonsul < 2) {
            return back()->with('error', 'Mahasiswa belum melakukan konsultasi minimal 2x. Total konsul: ' . $totalKonsul);
        }

        return view('admin.presentasi.create', compact('pengajuan', 'praPenelitian'));
    }

    /**
     * Admin: Store Jadwal Presentasi
     */
    public function store(Request $request, $pengajuanId)
    {
        $request->validate([
            'tanggal_presentasi' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'tempat' => 'required|string',
            'keterangan_admin' => 'nullable|string',
        ]);

        $pengajuan = Pengajuan::findOrFail($pengajuanId);
        $praPenelitian = PraPenelitian::where('user_id', $pengajuan->user_id)->firstOrFail();

        Presentasi::create([
            'pra_penelitian_id' => $praPenelitian->id,
            'user_id' => $pengajuan->user_id,
            'pengajuan_id' => $pengajuan->id,
            'tanggal_presentasi' => $request->tanggal_presentasi,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'keterangan_admin' => $request->keterangan_admin,
        ]);

        return redirect()->route('admin.pengajuan.index')->with('success', 'Jadwal presentasi berhasil dibuat!');
    }

    /**
     * Admin: Lihat detail presentasi
     */
    public function detail($id)
    {
        // ✅ BENAR: Load semua relasi yang ada
        $presentasi = Presentasi::with(['user', 'praPenelitian', 'pengajuan'])
            ->findOrFail($id);

        return view('admin.presentasi.detail', compact('presentasi'));
    }

    /**
     * Mahasiswa: Lihat Detail & Upload PPT
     */
    public function show()
    {
        $praPenelitian = PraPenelitian::where('user_id', auth()->id())->firstOrFail();
        $presentasi = Presentasi::where('pra_penelitian_id', $praPenelitian->id)->firstOrFail();
        $pengajuan = Pengajuan::find($presentasi->pengajuan_id);

        return view('presentasi.show', compact('presentasi', 'praPenelitian', 'pengajuan'));
    }

    /**
     * Mahasiswa: Upload File PPT
     */
    public function uploadPpt(Request $request, $id)
    {
        $request->validate([
            'file_ppt' => 'required|file|mimes:ppt,pptx,pdf|max:10240', // Max 10MB
        ]);

        $presentasi = Presentasi::where('user_id', auth()->id())->findOrFail($id);

        // Cek apakah sudah upload (hanya bisa sekali, kecuali revisi)
        if ($presentasi->file_ppt && $presentasi->nilai !== 'C') {
            return back()->with('error', 'File presentasi sudah diupload sebelumnya.');
        }

        // Hapus file lama jika ada
        if ($presentasi->file_ppt && Storage::exists($presentasi->file_ppt)) {
            Storage::delete($presentasi->file_ppt);
        }

        $path = $request->file('file_ppt')->store('presentasi', 'public');

        $presentasi->update([
            'file_ppt' => $path,
            'uploaded_at' => now(),
            'status_penilaian' => 'pending',
            'nilai' => null, // Reset nilai jika revisi
        ]);

        return back()->with('success', 'File presentasi berhasil diupload! Menunggu penilaian dari CI.');
    }

    /**
     * Mahasiswa: Upload Laporan (setelah nilai A/B)
     */
    public function uploadLaporan(Request $request, $id)
    {
        $request->validate([
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $presentasi = Presentasi::where('user_id', auth()->id())->findOrFail($id);

        if (!in_array($presentasi->nilai, ['A', 'B'])) {
            return back()->with('error', 'Anda belum bisa upload laporan.');
        }

        // Hapus file lama
        if ($presentasi->file_laporan && Storage::exists($presentasi->file_laporan)) {
            Storage::delete($presentasi->file_laporan);
        }

        $path = $request->file('file_laporan')->store('laporan', 'public');

        $presentasi->update([
            'file_laporan' => $path,
            'laporan_uploaded_at' => now(),
            'status_laporan' => 'pending',
        ]);

        return back()->with('success', 'Laporan berhasil diupload! Menunggu review dari admin.');
    }

    public function formPenilaian($token)
    {
        $presentasi = Presentasi::with([
            'praPenelitian.anggotas',
            'user',
            'pengajuan'
        ])->findOrFail($token);

        if (!$presentasi->file_ppt) {
            return view('ci.belum-upload', compact('presentasi'));
        }

        return view('ci.penilaian', [
            'presentasi' => $presentasi,
            'praPenelitian' => $presentasi->praPenelitian,
        ]);
    }

    /**
     * CI: Submit Penilaian
     */
    public function submitPenilaian(Request $request, $token)
    {
        $request->validate([
            'nilai' => 'required|in:A,B,C,D',
            'penilaian' => 'required|array|min:1',
            'penilaian.*.judul' => 'required|string',
            'penilaian.*.keterangan' => 'required|string',
        ]);

        $presentasi = Presentasi::findOrFail($token);

        $presentasi->update([
            'status_penilaian' => 'dinilai',
            'nilai' => $request->nilai,
            'hasil_penilaian' => $request->penilaian,
            'dinilai_at' => now(),
        ]);

        // Jika nilai D, hapus data dan reset
        if ($request->nilai === 'D') {
            $this->handleNilaiD($presentasi);
        }

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan!');
    }

    /**
     * Admin: Review Laporan
     */
    public function reviewLaporan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,revisi',
            'keterangan' => 'nullable|string',
        ]);

        $presentasi = Presentasi::findOrFail($id);

        $presentasi->update([
            'status_laporan' => $request->status,
            'keterangan_review' => $request->keterangan,
        ]);

        if ($request->status === 'approved') {
            $presentasi->update(['status_final' => 'selesai']);
            $this->generateSuratSelesai($presentasi);
        }

        return back()->with('success', 'Review laporan berhasil!');
    }

    /**
     * Handle Nilai D: Hapus semua data penelitian
     */
    private function handleNilaiD($presentasi)
    {
        // Hapus file
        if ($presentasi->file_ppt) Storage::delete($presentasi->file_ppt);
        if ($presentasi->file_laporan) Storage::delete($presentasi->file_laporan);

        // Hapus konsultasi
        Konsultasi::where('pra_penelitian_id', $presentasi->pra_penelitian_id)->delete();

        // Hapus presentasi
        $presentasi->delete();

        // Hapus pra penelitian
        PraPenelitian::find($presentasi->pra_penelitian_id)->delete();

        // Reset pengajuan
        $pengajuan = Pengajuan::find($presentasi->pengajuan_id);
        $pengajuan->update([
            'status' => 'rejected',
            'status_galasan' => 'pending',
            'status_pembayaran' => 'pending',
            'surat_balasan' => null,
            'invoice' => null,
            'bukti_pembayaran' => null,
            'ci_nama' => null,
            'ci_no_hp' => null,
            'ci_bidang' => null,
            'ruangan' => null,
        ]);
    }

    /**
     * Generate Surat Selesai & Sertifikat
     */
    private function generateSuratSelesai($presentasi)
    {
        // Generate PDF Surat Selesai
        $pdf = Pdf::loadView('pdf.surat-selesai', ['presentasi' => $presentasi]);
        $fileName = 'surat_selesai_' . $presentasi->user->name . '_' . time() . '.pdf';
        $path = 'surat_selesai/' . $fileName;
        Storage::put('public/' . $path, $pdf->output());

        // Generate Sertifikat
        $pdfCert = Pdf::loadView('pdf.sertifikat-penelitian', ['presentasi' => $presentasi]);
        $certName = 'sertifikat_' . $presentasi->user->name . '_' . time() . '.pdf';
        $certPath = 'sertifikat/' . $certName;
        Storage::put('public/' . $certPath, $pdfCert->output());

        $presentasi->update([
            'surat_selesai' => $path,
            'sertifikat' => $certPath,
        ]);
    }

    /**
     * Admin: Daftar Semua Presentasi
     */
    public function adminIndex()
    {
        // ✅ BENAR: Load relasi yang memang ada di tabel presentasi
        $presentasi = Presentasi::with(['user', 'praPenelitian', 'pengajuan'])
            ->latest()
            ->paginate(15);

        return view('admin.presentasi.index', compact('presentasi'));
    }

    public function apiLaporan()
{
    // Ambil semua presentasi yang sudah punya file laporan
    $presentasi = Presentasi::with([
            'user.mou',          // relasi user + mou
            'praPenelitian.mou', // relasi pra_penelitian + mou
        ])
        ->whereNotNull('file_laporan')
        ->get();

    // Mapping ke bentuk JSON yang rapi
    $data = $presentasi->map(function ($item) {
        // Prioritas ambil MOU dari pra_penelitian, kalau nggak ada baru dari user
        $mou = $item->praPenelitian->mou ?? $item->user->mou;

        return [
            'nama_user'             => $item->user->name,
            'tanggal_upload_laporan'=> optional($item->laporan_uploaded_at)->format('Y-m-d'),
            'judul'                 => optional($item->praPenelitian)->judul,
            'nama_instansi'         => $mou ? $mou->nama_instansi : null, // accessor nama_instansi sudah handle nama_universitas
            'program_studi'         => $item->praPenelitian->prodi ?? $item->user->program_studi,
            'file_laporan_url'      => $item->file_laporan 
                                        ? asset('storage/' . $item->file_laporan) 
                                        : null,
        ];
    });

    // Balikkan dalam bentuk JSON murni
    return response()->json($data, 200);
}

}