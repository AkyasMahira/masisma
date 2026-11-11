<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\PraPenelitian;
use App\Models\SuratPenelitian;
use App\Support\SuratPenelitianOtomatis;
use Illuminate\Http\Request;

class SuratPenelitianController extends Controller
{
    private function praDari(Pengajuan $pengajuan)
    {
        $pra = PraPenelitian::where('user_id', $pengajuan->user_id)->latest()->first();
        abort_unless($pra, 404, 'Data penelitian tidak ditemukan.');
        return $pra;
    }

    private function tampilkan(Pengajuan $pengajuan, $jenis, $unduh)
    {
        abort_unless(isset(SuratPenelitian::JENIS[$jenis]), 404);
        $pra = $this->praDari($pengajuan);
        $tersedia = SuratPenelitianOtomatis::tersedia($pengajuan, $pra);
        if (empty($tersedia[$jenis])) {
            return back()->with('error', SuratPenelitian::JENIS[$jenis]['judul'] . ' belum bisa dibuat pada tahap ini.');
        }

        $surat = SuratPenelitianOtomatis::register($pra, $jenis, $pengajuan);
        $pdf = SuratPenelitianOtomatis::pdf($surat, $pra, $pengajuan);
        $nama = SuratPenelitianOtomatis::namaFile($surat, $pra);
        return $unduh ? $pdf->download($nama) : $pdf->stream($nama);
    }

    /** Admin: lihat surat (nomor dibuat otomatis saat pertama dibuka). */
    public function admin(Pengajuan $pengajuan, $jenis)
    {
        return $this->tampilkan($pengajuan, $jenis, request()->boolean('unduh'));
    }

    /** Admin: atur tujuan (Kepada) surat pengantar, bisa beberapa ruangan / tujuan lain. Kosong = otomatis. */
    public function tujuan(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'tujuan'   => 'nullable|array|max:15',
            'tujuan.*' => 'nullable|string|max:150',
        ]);
        $pra = $this->praDari($pengajuan);
        if (empty(SuratPenelitianOtomatis::tersedia($pengajuan, $pra)['pengantar'])) {
            return back()->with('error', 'Surat pengantar belum bisa dibuat pada tahap ini.');
        }

        $tujuan = collect($data['tujuan'] ?? [])->map(fn ($t) => trim(preg_replace('/\s+/', ' ', (string) $t)))
            ->filter()->unique()->values()->all();

        $surat = SuratPenelitianOtomatis::register($pra, 'pengantar', $pengajuan);
        $surat->update(['tujuan' => $tujuan ?: null]);
        // perbarui file PDF tersimpan agar sama dengan yang diunduh
        SuratPenelitianOtomatis::simpan($pra, 'pengantar', $pengajuan);

        return back()->with('success', $tujuan
            ? 'Tujuan surat pengantar disimpan (' . count($tujuan) . ' tujuan).'
            : 'Tujuan surat pengantar dikembalikan otomatis sesuai ruangan pengajuan.');
    }

    /** Mahasiswa: unduh surat miliknya sendiri. */
    public function mahasiswa(Pengajuan $pengajuan, $jenis)
    {
        abort_unless((int) $pengajuan->user_id === (int) auth()->id(), 403);
        return $this->tampilkan($pengajuan, $jenis, true);
    }

    /** Publik: verifikasi keaslian surat dari QR. */
    public function verifikasi($kode)
    {
        $surat = SuratPenelitian::with(['praPenelitian.user', 'praPenelitian.mou'])->where('kode_verifikasi', $kode)->first();
        $kegiatan = $surat && $surat->praPenelitian ? SuratPenelitianOtomatis::kegiatan($surat->praPenelitian->jenis_penelitian) : null;
        return view('public.surat_verifikasi', compact('surat', 'kegiatan'));
    }
}
