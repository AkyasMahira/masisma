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
 use Illuminate\Support\Facades\Http;

class PresentasiController extends Controller
{
    /**
     * ==========================================
     * SECTION: ADMIN
     * ==========================================
     */

    /**
     * Admin: Daftar Semua Presentasi
     */
   /**
 * Admin: Daftar Semua Presentasi
 */
public function adminIndex(Request $request)
{
    // Gunakan query builder agar bisa difilter
    $query = Presentasi::with(['user', 'praPenelitian', 'pengajuan'])
        ->whereHas('praPenelitian', function ($q) {
            // MENYEMBUNYIKAN: Data Awal & Uji Validitas
            $q->whereNotIn('jenis_penelitian', ['Uji Validitas', 'Data Awal']);
        });

    // --- FITUR FILTER (Opsional, menyesuaikan input filter di Blade) ---
    if ($request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($u) use ($search) {
                $u->where('name', 'like', "%{$search}%");
            })->orWhereHas('praPenelitian', function($p) use ($search) {
                $p->where('judul', 'like', "%{$search}%");
            });
        });
    }

    if ($request->status_nilai) {
        $request->status_nilai == 'sudah_dinilai' 
            ? $query->whereNotNull('nilai') 
            : $query->whereNull('nilai');
    }

    if ($request->status_final) {
        $query->where('status_final', $request->status_final);
    }

    // Eksekusi Pagination
    $presentasi = $query->latest()->paginate(15);

    return view('admin.presentasi.index', compact('presentasi'));
}

    /**
     * Admin: Form Set Jadwal Presentasi
     */
    public function create($pengajuanId)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($pengajuanId);

        // Ambil pra penelitian TERBARU dengan anggota & mou
        $praPenelitian = PraPenelitian::with(['anggotas', 'mou'])
            ->where('user_id', $pengajuan->user_id)
            ->latest()
            ->first();

        if (!$praPenelitian) {
            return back()->with('error', 'Data pra penelitian tidak ditemukan.');
        }

        // Cek syarat bimbingan minimal 2x
        $totalKonsul = Konsultasi::where('pra_penelitian_id', $praPenelitian->id)->count();

        if ($totalKonsul < 2) {
            return back()->with('error', 'Mahasiswa belum melakukan konsultasi minimal 2x. Total saat ini: ' . $totalKonsul);
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
            'surat_selesai_manual' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $pengajuan = Pengajuan::findOrFail($pengajuanId);
        
        // Pastikan ambil record pra_penelitian TERBARU
        $praPenelitian = PraPenelitian::where('user_id', $pengajuan->user_id)
            ->latest()
            ->firstOrFail();

        $presentasi = Presentasi::create([
            'pra_penelitian_id' => $praPenelitian->id,
            'user_id' => $pengajuan->user_id,
            'pengajuan_id' => $pengajuan->id,
            'tanggal_presentasi' => $request->tanggal_presentasi,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'keterangan_admin' => $request->keterangan_admin,
        ]);

        // Logic Otomatis Lulus untuk Uji Validitas & Data Awal
        if (in_array($praPenelitian->jenis_penelitian, ['Uji Validitas', 'Data Awal'])) {
            $suratPath = null;
            if ($request->hasFile('surat_selesai_manual')) {
                $suratPath = $request->file('surat_selesai_manual')->store('surat_selesai', 'public');
            }

            $presentasi->update([
                'status_penilaian' => 'dinilai',
                'nilai' => 'A',
                'dinilai_at' => now(),
                'status_laporan' => 'approved',
                'status_final' => 'selesai',
                'surat_selesai' => $suratPath,
            ]);
            
            return redirect()->route('admin.pengajuan.index')->with('success', $praPenelitian->jenis_penelitian . ' Berhasil Diselesaikan!');
        }

        return redirect()->route('admin.pengajuan.index')->with('success', 'Jadwal presentasi berhasil dibuat!');
    }

    /**
     * Admin: Lihat detail presentasi
     */
    public function detail($id)
    {
        $presentasi = Presentasi::with(['user', 'praPenelitian.anggotas', 'pengajuan.ci'])
            ->findOrFail($id);

        return view('admin.presentasi.detail', compact('presentasi'));
    }

    /**
     * Admin: Review Laporan Akhir
     */
    public function reviewLaporan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,revisi',
            'keterangan' => 'nullable|string',
        ]);

        $presentasi = Presentasi::with(['praPenelitian', 'pengajuan'])->findOrFail($id);

        $presentasi->update([
            'status_laporan' => $request->status,
            'keterangan_review' => $request->keterangan,
        ]);

        if ($request->status === 'approved') {
            $presentasi->update(['status_final' => 'selesai']);
            $this->generateSuratSelesai($presentasi);
        }

        return back()->with('success', 'Review laporan berhasil disimpan!');
    }

    /**
     * ==========================================
     * SECTION: MAHASISWA
     * ==========================================
     */

    /**
     * Mahasiswa: Lihat Detail & Progress Presentasi
     */
    public function show()
    {
        // AMBIL DATA TERBARU: Menggunakan latest()
        $praPenelitian = PraPenelitian::where('user_id', auth()->id())
            ->latest()
            ->firstOrFail();

        $presentasi = Presentasi::where('pra_penelitian_id', $praPenelitian->id)
            ->firstOrFail();

        $pengajuan = Pengajuan::with(['ci', 'dataRuangan'])->find($presentasi->pengajuan_id);

        return view('presentasi.show', compact('presentasi', 'praPenelitian', 'pengajuan'));
    }

    /**
     * Mahasiswa: Upload PPT
     */
    public function uploadPpt(Request $request, $id)
    {
        $request->validate([
            'file_ppt' => 'required|file|mimes:ppt,pptx,pdf|max:10240',
        ]);

        $presentasi = Presentasi::where('user_id', auth()->id())->findOrFail($id);

        if ($presentasi->file_ppt && $presentasi->nilai !== 'C') {
            return back()->with('error', 'File presentasi sudah diupload sebelumnya.');
        }

        if ($presentasi->file_ppt && Storage::disk('public')->exists($presentasi->file_ppt)) {
            Storage::disk('public')->delete($presentasi->file_ppt);
        }

        $path = $request->file('file_ppt')->store('presentasi', 'public');

        $presentasi->update([
            'file_ppt' => $path,
            'uploaded_at' => now(),
            'status_penilaian' => 'pending',
            'nilai' => null,
        ]);

        return back()->with('success', 'File presentasi berhasil diupload!');
    }

    /**
     * Mahasiswa: Upload Laporan (Setelah Lulus A/B)
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

        if ($presentasi->file_laporan && Storage::disk('public')->exists($presentasi->file_laporan)) {
            Storage::disk('public')->delete($presentasi->file_laporan);
        }

        $path = $request->file('file_laporan')->store('laporan', 'public');

        $presentasi->update([
            'file_laporan' => $path,
            'laporan_uploaded_at' => now(),
            'status_laporan' => 'pending',
        ]);

        return back()->with('success', 'Laporan berhasil diupload!');
    }

    /**
     * ==========================================
     * SECTION: CI (PEMBIMBING LAPANGAN)
     * ==========================================
     */

    /**
     * CI: Form Penilaian
     */
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
        'nama_ci' => 'required|string',
        'skor_angka' => 'required|numeric|min:0|max:100',
        'penilaian' => 'required|array',
    ]);

    $presentasi = Presentasi::findOrFail($token);

    // Simpan ke tabel detail (CI bisa input berkali-kali/CI berbeda)
    $presentasi->penilaianDetails()->create([
        'nama_ci' => $request->nama_ci,
        'skor_angka' => $request->skor_angka,
        'catatan' => $request->penilaian,
    ]);

    return redirect()->back()->with('success', 'Nilai berhasil dikirim!');
}

public function terimaSemuaNilai(Request $request, $id)
{
    $presentasi = Presentasi::with('penilaianDetails')->findOrFail($id);
    
    // Admin bisa mengubah nilai ini lewat form sebelum submit
    $request->validate([
        'nilai_final' => 'required|in:A,B,C,D',
        'skor_final' => 'required|numeric|min:0|max:100',
    ]);

    // Gabungkan semua catatan CI
    $semuaCatatan = [];
    foreach ($presentasi->penilaianDetails as $detail) {
        if ($detail->catatan) {
            foreach ($detail->catatan as $catatan) {
                $catatan['judul'] = $catatan['judul'] . " (CI: " . $detail->nama_ci . ")";
                $semuaCatatan[] = $catatan;
            }
        }
    }

    $presentasi->update([
        'nilai' => $request->nilai_final, // Nilai pilihan admin
        'skor_total' => $request->skor_final, // Skor pilihan admin
        'status_penilaian' => 'dinilai',
        'dinilai_at' => now(),
        'hasil_penilaian' => $semuaCatatan,
    ]);

    if ($request->nilai_final === 'D') {
        $this->handleNilaiD($presentasi);
        return redirect()->route('admin.presentasi.index')->with('error', 'Finalisasi Selesai: Mahasiswa Ditolak.');
    }

    return back()->with('success', 'Nilai Berhasil Difinalisasi!');
}
    /**
     * ==========================================
     * SECTION: DOWNLOADS & HELPER
     * ==========================================
     */

    /**
     * Download Sertifikat Anggota
     */
public function downloadSertifikatAnggota($id, $namaAnggota)
{
    // 1. Ambil data presentasi terbaru beserta relasinya
    $presentasi = Presentasi::with(['praPenelitian.anggotas', 'user', 'pengajuan'])
                            ->findOrFail($id);
    
    // 2. Decode nama anggota dari URL
    $nama_penerima = urldecode($namaAnggota);

    // 3. Load view sertifikat yang baru kita buat
    // Pastikan file blade sertifikat ada di: resources/views/pdf/sertifikat-penelitian.blade.php
    $pdf = Pdf::loadView('pdf.sertifikat-penelitian', [
        'presentasi'    => $presentasi,
        'nama_penerima' => $nama_penerima
    ])->setPaper('a4', 'landscape');

    // 4. Stream/Download (Ini akan men-generate PDF baru setiap kali klik)
    return $pdf->stream('Sertifikat_' . str_replace(' ', '_', $nama_penerima) . '.pdf');
}

    /**
     * Download Surat Selesai Anggota
     */
public function downloadSuratSelesaiAnggota($id, $namaAnggota)
{
    // 1. Ambil data presentasi terbaru beserta relasi yang dibutuhkan
    $presentasi = Presentasi::with(['user', 'praPenelitian.mou', 'penilaianDetails', 'pengajuan'])
                            ->findOrFail($id);
    
    $nama_penerima = urldecode($namaAnggota);

    // 2. Load view Surat Keterangan yang baru saja kita sesuaikan rincian nilainya
    // Pastikan file blade ada di: resources/views/pdf/surat-selesai.blade.php
    $pdf = Pdf::loadView('pdf.surat-selesai', [
        'presentasi'    => $presentasi,
        'nama_penerima' => $nama_penerima
    ]);

    // 3. Set format kertas (Portrait A4 biasanya untuk surat resmi)
    $pdf->setPaper('a4', 'portrait');

    // 4. Stream ke browser
    return $pdf->stream('Surat_Keterangan_Selesai_' . str_replace(' ', '_', $nama_penerima) . '.pdf');
}

    /**
     * API Laporan untuk integrasi data
     */

public function apiLaporan()
{
    $presentasi = Presentasi::with(['user.mou', 'praPenelitian.mou'])
        ->whereNotNull('file_laporan')
        ->get();

    $logs = [];

    foreach ($presentasi as $item) {
        $mou = $item->praPenelitian->mou ?? $item->user->mou;
        $judul = optional($item->praPenelitian)->judul ?? 'Tanpa Judul';

        $payload = [
            'nama_user'        => $item->user->name,
            'judul'            => $judul,
            'nama_instansi'    => $mou->nama_instansi ?? $mou->nama_universitas ?? null,
            'file_laporan_url' => asset('storage/' . $item->file_laporan),
            'jenis'            => 1,
        ];

        try {
            // Kirim ke Perpustakaan
            $response = Http::timeout(10)->post('http://192.168.244.104/perpustakaan/api/sync-sindikat', $payload);
            
            if ($response->successful()) {
                $resData = $response->json();
                $logs[] = [
                    'judul'  => $judul,
                    'status' => 'Berhasil',
                    'info'   => ($resData['action'] ?? 'Success') // Akan berisi 'Created' atau 'Updated'
                ];
            } else {
                $logs[] = [
                    'judul'  => $judul,
                    'status' => 'Gagal',
                    'info'   => 'Server Perpustakaan Error (Status: ' . $response->status() . ')'
                ];
            }
        } catch (\Exception $e) {
            $logs[] = [
                'judul'  => $judul,
                'status' => 'Gagal',
                'info'   => 'Koneksi Terputus / Timeout'
            ];
        }
    }

    return response()->json([
        'message' => 'Proses sinkronisasi selesai.',
        'detail'  => $logs // Ini yang akan dibaca oleh JavaScript
    ], 200);
}
    /**
     * Handle Nilai D: Reset Status Pengajuan
     */
    private function handleNilaiD($presentasi)
    {
        if ($presentasi->file_ppt) Storage::disk('public')->delete($presentasi->file_ppt);
        if ($presentasi->file_laporan) Storage::disk('public')->delete($presentasi->file_laporan);

        $praId = $presentasi->pra_penelitian_id;
        $pengajuanId = $presentasi->pengajuan_id;

        $presentasi->delete();
        Konsultasi::where('pra_penelitian_id', $praId)->delete();
        PraPenelitian::find($praId)->delete();

        Pengajuan::find($pengajuanId)->update([
            'status' => 'rejected',
            'surat_balasan' => null,
            'bukti_pembayaran' => null,
            'ci_id' => null
        ]);
    }

    /**
     * Generate Otomatis Surat & Sertifikat Mahasiswa Utama
     */
    private function generateSuratSelesai($presentasi)
    {
        $userNama = $presentasi->user->name;

        // Generate Surat Selesai
        $pdfSurat = Pdf::loadView('pdf.surat-selesai', ['presentasi' => $presentasi, 'nama_penerima' => $userNama]);
        $pathSurat = 'surat_selesai/surat_' . $presentasi->id . '_' . time() . '.pdf';
        Storage::put('public/' . $pathSurat, $pdfSurat->output());

        // Generate Sertifikat
        $pdfCert = Pdf::loadView('pdf.sertifikat-penelitian', ['presentasi' => $presentasi, 'nama_penerima' => $userNama])->setPaper('a4', 'landscape');
        $pathCert = 'sertifikat/cert_' . $presentasi->id . '_' . time() . '.pdf';
        Storage::put('public/' . $pathCert, $pdfCert->output());

        $presentasi->update([
            'surat_selesai' => $pathSurat,
            'sertifikat' => $pathCert,
        ]);
    }
}