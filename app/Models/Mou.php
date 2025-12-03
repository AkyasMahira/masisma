<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mou extends Model
{
    /**
     * Nama tabel
     * @var string
     */
    protected $table = 'mous';

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        // Backwards compatible: keep old column while we migrate clients
        'nama_universitas',
        'nama_instansi',
        'tanggal_masuk',
        'tanggal_keluar',
        'file_mou',
        'surat_keterangan',
        'keterangan',
        'alamat_instansi',
        'rencana_kerja_sama',
        'nama_pic_instansi',
        'nomor_kontak_pic',
        'jenis_instansi',
        'jenis_instansi_lainnya',
        // File uploads for pengajuan
        'surat_permohonan',
        'sk_pengangkatan_pimpinan',
        'sertifikat_akreditasi_prodi',
        'draft_mou',
    ];

    /**
     * Tipe data bawaan (casting).
     *
     * @var array
     */
    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    // Accessor for a unified 'nama_instansi' value that falls back to the old column
    public function getNamaInstansiAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->attributes['nama_universitas'] ?? null;
    }

    public function praPenelitians()
    {
        return $this->hasMany(PraPenelitian::class);
    }
}
