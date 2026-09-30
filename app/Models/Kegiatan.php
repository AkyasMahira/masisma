<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KegiatanFasilitator;
class Kegiatan extends Model
{
    protected $table = 'kegiatan';
protected $fillable = [
        'nama_kegiatan', 'deskripsi', 'keahlian', // <-- Tambahkan 'keahlian'
        'jenis_kegiatan', 'tanggal_mulai', 'tanggal_selesai', 
        'penyelenggara_id', 'platform', 'tipe_absen', 
        'jpl', 'token_absensi', 'status_absen', 'warna_tema'
    ];

    // Beritahu Laravel otomatis convert array ke JSON (dan sebaliknya)
    protected $casts = [
        'keahlian' => 'array',
    ];

// Relasi ke Tujuan
public function tujuan() {
    return $this->hasMany(KegiatanTujuan::class, 'kegiatan_id');
}
/**
     * Relasi ke tabel kegiatan_fasilitator
     * 1 Kegiatan memiliki banyak Fasilitator
     */
    public function fasilitator()
    {
        return $this->hasMany(KegiatanFasilitator::class, 'kegiatan_id');
    }
// Relasi ke Kompetensi (Pivot)
public function kompetensi() {
    return $this->belongsToMany(MasterKompetensi::class, 'kegiatan_kompetensi', 'kegiatan_id', 'kompetensi_id')
                ->withPivot('indikator_keberhasilan')
                ->withTimestamps();
}

    // Relasi: Kegiatan ini diselenggarakan oleh instansi mana
    public function penyelenggara()
    {
        return $this->belongsTo(MasterInstansi::class, 'penyelenggara_id');
    }

    // Relasi: Satu kegiatan berisi banyak target daftar peserta (Rumah Peserta)
    public function peserta()
    {
        return $this->hasMany(KegiatanPeserta::class, 'kegiatan_id');
    }

    // Relasi: Satu kegiatan mengumpulkan banyak log absensi aktual
    public function absensi()
    {
        return $this->hasMany(AbsensiKegiatan::class, 'kegiatan_id');
    }

    // Relasi: Satu kegiatan punya satu setting penilaian (jenis penilaian & batas lulus)
    public function penilaianSetting()
    {
        return $this->hasOne(KegiatanPenilaianSetting::class, 'kegiatan_id');
    }

    // Relasi: Satu kegiatan punya banyak item checklist/skor penilaian skill
    public function itemPenilaianSkill()
    {
        return $this->hasMany(ItemPenilaianSkill::class, 'kegiatan_id');
    }
}