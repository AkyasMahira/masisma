<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentasi extends Model
{
    protected $table = 'presentasi';

    protected $fillable = [
        'pra_penelitian_id',
        'user_id',
        'pengajuan_id',
        'tanggal_presentasi',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'keterangan_admin',
        'file_ppt',
        'uploaded_at',
        'status_penilaian',
        'nilai',
        'hasil_penilaian',
        'dinilai_at',
        'file_laporan',
        'laporan_uploaded_at',
        'status_laporan',
        'keterangan_review',
        'status_final',
        'surat_selesai',
        'sertifikat',
    ];

    protected $casts = [
        'tanggal_presentasi' => 'date',
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
        'uploaded_at' => 'datetime',
        'dinilai_at' => 'datetime',
        'laporan_uploaded_at' => 'datetime',
        'hasil_penilaian' => 'array',
    ];

    public function praPenelitian()
    {
        return $this->belongsTo(PraPenelitian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
    
    public function penilaianDetails() {
    return $this->hasMany(PenilaianDetail::class);
}

// Helper untuk mendapatkan predikat dari angka
// Helper untuk mendapatkan predikat dari angka
public static function getPredikat($angka) {
    if ($angka >= 80) return 'A'; // Predikat A: 80 - 100
    if ($angka >= 70) return 'B'; // Predikat B: 70 - 79
    if ($angka >= 60) return 'C'; // Predikat C: 60 - 69
    return 'D';                   // Predikat D: < 60
}

public static function getKeterangan($predikat) {
    $map = [
        'A' => 'Baik Sekali',
        'B' => 'Baik',
        'C' => 'Cukup',
        'D' => 'Kurang'
    ];
    return $map[$predikat] ?? '-';
}
}