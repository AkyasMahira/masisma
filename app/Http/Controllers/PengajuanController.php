<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\PraPenelitian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function index()
    {
        // ✅ BENAR - Tanpa with('praPenelitian')
        $pra = Pengajuan::where('user_id', auth()->id())
            ->where('jenis', 'pra_penelitian')
            ->latest()
            ->first();

        $magang = Pengajuan::where('user_id', auth()->id())
            ->where('jenis', 'magang')
            ->latest()
            ->first();

        return view('pengajuan.index', compact('pra', 'magang'));
    }

    public function ajukanPra()
    {
        // Cek apakah sudah pernah mengajukan dan statusnya belum rejected
        $cek = Pengajuan::where('user_id', auth()->id())
            ->where('jenis', 'pra_penelitian')
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($cek) {
            return back()->with('error', 'Anda sudah memiliki pengajuan Pra-Penelitian yang aktif.');
        }

        Pengajuan::create([
            'user_id' => auth()->id(),
            'jenis'   => 'pra_penelitian',
            'status'  => 'pending',
        ]);

        return back()->with('success', 'Pengajuan pra-penelitian berhasil dikirim.');
    }

    public function ajukanMagang()
    {
        // Cek apakah sudah pernah mengajukan
        $cek = Pengajuan::where('user_id', auth()->id())
            ->where('jenis', 'magang')
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($cek) {
            return back()->with('error', 'Anda sudah memiliki pengajuan Magang yang aktif.');
        }

        Pengajuan::create([
            'user_id' => auth()->id(),
            'jenis'   => 'magang',
            'status'  => 'pending',
        ]);

        return back()->with('success', 'Pengajuan magang berhasil dikirim.');
    }

    // ========== MAHASISWA METHODS ==========

    /**
     * Upload bukti pembayaran
     */
    public function uploadBuktiPembayaran(Request $request, Pengajuan $pengajuan)
    {
        // Validasi bahwa pengajuan ini milik user yang login
        if ($pengajuan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Validasi status galasan sudah dikirim
        if ($pengajuan->status_galasan !== 'sent') {
            return back()->with('error', 'Galasan belum dikirim oleh admin.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Hapus file lama jika ada
        if ($pengajuan->bukti_pembayaran && Storage::exists($pengajuan->bukti_pembayaran)) {
            Storage::delete($pengajuan->bukti_pembayaran);
        }

        // Upload file baru
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        $pengajuan->update([
            'bukti_pembayaran' => $path,
            'status_pembayaran' => 'uploaded',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    // ========== ADMIN METHODS ==========

    public function adminIndex()
    {
        $data = Pengajuan::with('user')->latest()->get();
        return view('admin.pengajuan.index', compact('data'));
    }

    public function approve(Pengajuan $pengajuan)
    {
        $pengajuan->update(['status' => 'approved']);
        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function reject(Pengajuan $pengajuan)
    {
        $pengajuan->update(['status' => 'rejected']);
        return back()->with('success', 'Pengajuan ditolak.');
    }

    /**
     * Kirim galasan (surat + invoice)
     */
    public function kirimGalasan(Request $request, Pengajuan $pengajuan)
    {
        // Cek apakah ada pra_penelitian yang sudah approved
        $praPenelitian = PraPenelitian::where('user_id', $pengajuan->user_id)
            ->where('status', 'Approved')
            ->first();

        if (!$praPenelitian) {
            return back()->with('error', 'Form pra penelitian belum di-approve.');
        }

        $request->validate([
            'surat_balasan' => 'required|file|mimes:pdf|max:2048',
            'invoice' => 'required|file|mimes:pdf|max:2048',
        ]);

        $suratPath = $request->file('surat_balasan')->store('surat_balasan', 'public');
        $invoicePath = $request->file('invoice')->store('invoice', 'public');

        $pengajuan->update([
            'surat_balasan' => $suratPath,
            'invoice' => $invoicePath,
            'status_galasan' => 'sent',
        ]);

        return back()->with('success', 'Galasan berhasil dikirim ke mahasiswa.');
    }

    /**
     * Approve bukti pembayaran dan assign CI + ruangan
     */
    public function approvePembayaran(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'ci_nama' => 'required|string|max:255',
            'ci_no_hp' => 'required|string|max:20',
            'ci_bidang' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
        ]);

        $pengajuan->update([
            'status_pembayaran' => 'verified',
            'ci_nama' => $request->ci_nama,
            'ci_no_hp' => $request->ci_no_hp,
            'ci_bidang' => $request->ci_bidang,
            'ruangan' => $request->ruangan,
        ]);

        return back()->with('success', 'Bukti pembayaran diverifikasi dan CI/Ruangan berhasil ditugaskan.');
    }

    /**
     * Show detail pengajuan untuk admin
     */
    public function show(Pengajuan $pengajuan)
    {
        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    /**
     * Tampilkan detail pengajuan (untuk mahasiswa)
     */
    public function detail($jenis)
    {
        $pengajuan = Pengajuan::where('user_id', auth()->id())
            ->where('jenis', $jenis)
            ->where('status', 'approved')
            ->firstOrFail();

        return view('pengajuan.detail', compact('pengajuan', 'jenis'));
    }

    public function destroy(Pengajuan $pengajuan)
    {
        $user = auth()->user();

        // authorize: owner or admin
        if (!$user || ($user->id !== $pengajuan->user_id && $user->role !== 'admin')) {
            abort(403, 'Unauthorized');
        }

        // Delete any uploaded files
        if ($pengajuan->surat_balasan && Storage::exists($pengajuan->surat_balasan)) {
            Storage::delete($pengajuan->surat_balasan);
        }
        if ($pengajuan->invoice && Storage::exists($pengajuan->invoice)) {
            Storage::delete($pengajuan->invoice);
        }
        if ($pengajuan->bukti_pembayaran && Storage::exists($pengajuan->bukti_pembayaran)) {
            Storage::delete($pengajuan->bukti_pembayaran);
        }

        // Remove related Presentasi (and its files)
        $presentasi = \App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first();
        if ($presentasi) {
            // delete presentasi files
            if ($presentasi->file_ppt && Storage::exists($presentasi->file_ppt)) Storage::delete($presentasi->file_ppt);
            if ($presentasi->file_laporan && Storage::exists($presentasi->file_laporan)) Storage::delete($presentasi->file_laporan);
            if ($presentasi->sertifikat && Storage::exists('public/' . $presentasi->sertifikat)) Storage::delete('public/' . $presentasi->sertifikat);
            if ($presentasi->surat_selesai && Storage::exists('public/' . $presentasi->surat_selesai)) Storage::delete('public/' . $presentasi->surat_selesai);

            // delete presentasi record
            $presentasi->delete();
        }

        // Remove PraPenelitian and related records (anggotas, konsultasi)
        $pra = PraPenelitian::where('user_id', $pengajuan->user_id)->first();
        if ($pra) {
            // delete konsultasi
            \App\Models\Konsultasi::where('pra_penelitian_id', $pra->id)->delete();
            // delete anggota
            \App\Models\PraPenelitianAnggota::where('pra_penelitian_id', $pra->id)->delete();
            // finally delete pra penelitian
            $pra->delete();
        }

        // finally delete pengajuan
        $pengajuan->delete();

        if ($user->role === 'admin') {
            return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
        }

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus. Anda bisa mengajukan kembali.');
    }
}
