<?php

namespace App\Support;

use App\Models\Pengajuan;
use App\Models\PraPenelitian;
use App\Models\SuratPenelitian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Surat penelitian otomatis (balasan izin, pengantar ke ruangan, keterangan selesai)
 * untuk jenis Data Awal, Uji Validitas, dan Penelitian. Nomor dibuat sekali lalu tetap.
 */
class SuratPenelitianOtomatis
{
    const ROMAWI = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    /** Istilah kegiatan per jenis penelitian. */
    public static function kegiatan($jenis)
    {
        switch ($jenis) {
            case 'Data Awal':
                return ['nama' => 'Pengambilan Data Awal (Studi Pendahuluan)', 'singkat' => 'pengambilan data awal'];
            case 'Uji Validitas':
                return ['nama' => 'Uji Validitas dan Reliabilitas Instrumen Penelitian', 'singkat' => 'uji validitas dan reliabilitas instrumen'];
            default:
                return ['nama' => 'Penelitian', 'singkat' => 'penelitian'];
        }
    }

    /** Surat mana saja yang sudah bisa dibuat untuk pengajuan ini. */
    public static function tersedia(Pengajuan $pengajuan, PraPenelitian $pra)
    {
        $presentasi = \App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first();
        return [
            'balasan'   => $pra->status === 'Approved',
            'pengantar' => $pengajuan->status_pembayaran === 'verified' && $pengajuan->ruangan_id,
            'selesai'   => $presentasi && $presentasi->status_final === 'selesai',
            'sertifikat' => $pra->jenis_penelitian === 'Penelitian' && $presentasi && $presentasi->status_final === 'selesai',
        ];
    }

    /** Ambil register surat (buat nomor baru bila belum ada). */
    public static function register(PraPenelitian $pra, $jenisSurat, ?Pengajuan $pengajuan = null)
    {
        $ada = SuratPenelitian::where('pra_penelitian_id', $pra->id)->where('jenis_surat', $jenisSurat)->first();
        if ($ada) return $ada;

        return DB::transaction(function () use ($pra, $jenisSurat, $pengajuan) {
            $now = now();
            $urut = (int) SuratPenelitian::where('jenis_surat', $jenisSurat)->where('tahun', $now->year)->lockForUpdate()->max('nomor_urut') + 1;
            $kode = SuratPenelitian::JENIS[$jenisSurat]['kode'];
            return SuratPenelitian::create([
                'pra_penelitian_id' => $pra->id,
                'pengajuan_id'      => $pengajuan ? $pengajuan->id : null,
                'jenis_surat'       => $jenisSurat,
                'tahun'             => $now->year,
                'nomor_urut'        => $urut,
                'nomor_surat'       => sprintf('%03d/%s/DIKLAT/RSUD-SLG/%s/%d', $urut, $kode, self::ROMAWI[$now->month], $now->year),
                'tanggal_surat'     => $now->toDateString(),
                'kode_verifikasi'   => Str::lower(Str::random(24)),
            ]);
        });
    }

    /** Data lengkap untuk view surat. */
    public static function data(SuratPenelitian $surat, PraPenelitian $pra, ?Pengajuan $pengajuan = null, $namaPenerima = null)
    {
        $pra->loadMissing(['user', 'mou', 'anggotas']);
        if ($pengajuan) $pengajuan->loadMissing(['ci', 'ci2', 'dataRuangan']);
        $presentasi = $pengajuan ? \App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first() : null;
        $mou = $pra->mou ?: optional($pra->user)->mou;

        // Balasan izin & keterangan selesai PENELITIAN ditandatangani Direktur; lainnya Koordinator Diklat
        $ttdKey = (in_array($surat->jenis_surat, ['balasan', 'sertifikat'], true) || ($surat->jenis_surat === 'selesai' && $pra->jenis_penelitian === 'Penelitian')) ? 'direktur' : 'diklat';

        return [
            'surat'      => $surat,
            'pra'        => $pra,
            'pengajuan'  => $pengajuan,
            'presentasi' => $presentasi,
            'kegiatan'   => self::kegiatan($pra->jenis_penelitian),
            'institusi'  => $mou ? ($mou->nama_instansi ?: $mou->nama_universitas) : '-',
            'mou'        => $mou,
            'ruangan'    => $pengajuan && $pengajuan->dataRuangan ? $pengajuan->dataRuangan->nm_ruangan : null,
            'tujuan'     => self::tujuan($surat, $pengajuan),
            'kop'        => config('surat.instansi'),
            'ttd'        => config('surat.penandatangan.' . $ttdKey),
            'logo'       => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('logors.png'))),
            'urlVerif'   => route('surat.verifikasi', $surat->kode_verifikasi),
            'namaPenerima' => $namaPenerima,
        ];
    }

    /** Daftar tujuan (Kepada): diatur admin, atau otomatis Kepala Ruangan dari pengajuan. */
    public static function tujuan(SuratPenelitian $surat, ?Pengajuan $pengajuan = null)
    {
        if (!empty($surat->tujuan)) return array_values($surat->tujuan);
        $ruang = $pengajuan && $pengajuan->dataRuangan ? $pengajuan->dataRuangan->nm_ruangan : null;
        return [$ruang ? 'Kepala Ruangan ' . $ruang : 'Kepala Ruangan ..........'];
    }

    public static function view(SuratPenelitian $surat, PraPenelitian $pra)
    {
        if ($surat->jenis_surat === 'selesai' && $pra->jenis_penelitian === 'Penelitian') {
            return 'pdf.penelitian.selesai_penelitian';   // dengan rincian nilai CI & nilai akhir
        }
        return 'pdf.penelitian.' . $surat->jenis_surat;
    }

    public static function pdf(SuratPenelitian $surat, PraPenelitian $pra, ?Pengajuan $pengajuan = null, $namaPenerima = null)
    {
        return Pdf::loadView(self::view($surat, $pra), self::data($surat, $pra, $pengajuan, $namaPenerima))
            ->setPaper('a4', $surat->jenis_surat === 'sertifikat' ? 'landscape' : 'portrait');
    }

    /** Buat (bila perlu) lalu simpan PDF ke storage publik; kembalikan path relatif. */
    public static function simpan(PraPenelitian $pra, $jenisSurat, ?Pengajuan $pengajuan = null)
    {
        $surat = self::register($pra, $jenisSurat, $pengajuan);
        $path = 'surat_penelitian/' . $jenisSurat . '_' . $pra->id . '_' . $surat->kode_verifikasi . '.pdf';
        Storage::disk('public')->put($path, self::pdf($surat, $pra, $pengajuan)->output());
        $surat->update(['file_path' => $path]);
        return $path;
    }

    public static function namaFile(SuratPenelitian $surat, PraPenelitian $pra)
    {
        $nama = optional($pra->user)->name ?: 'Mahasiswa';
        return NamaFile::aman(str_replace(' ', '_', $surat->judulSurat() . ' - ' . $pra->jenis_penelitian . ' - ' . $nama) . '.pdf');
    }
}
