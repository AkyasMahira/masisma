<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPenelitian extends Model
{
    protected $table = 'surat_penelitians';

    protected $fillable = [
        'pra_penelitian_id', 'pengajuan_id', 'jenis_surat', 'tahun', 'nomor_urut',
        'nomor_surat', 'tanggal_surat', 'kode_verifikasi', 'file_path',
    ];

    protected $casts = ['tanggal_surat' => 'date'];

    const JENIS = [
        'balasan'   => ['kode' => 'SB-PNL',  'judul' => 'Surat Balasan Izin'],
        'pengantar' => ['kode' => 'SP-PNL',  'judul' => 'Surat Pengantar ke Ruangan'],
        'selesai'   => ['kode' => 'SKS-PNL', 'judul' => 'Surat Keterangan Selesai'],
        'sertifikat' => ['kode' => 'SRT-PNL', 'judul' => 'Sertifikat Penelitian'],
    ];

    public function praPenelitian()
    {
        return $this->belongsTo(PraPenelitian::class);
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function judulSurat()
    {
        return self::JENIS[$this->jenis_surat]['judul'] ?? 'Surat';
    }
}
