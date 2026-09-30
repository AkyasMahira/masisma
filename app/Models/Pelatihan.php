<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    protected $table = 'pelatihans';

    protected $fillable = [
        'nama', 'nik', 'bidang', 'jabatan', 'unit', 'status_pegawai',
        'nip', 'golongan', 'pangkat', 'nirp', 'lms_status', 'lms_email',
        'pelatihan_dasar', 'pelatihan_peningkatan_kompetensi',
    ];

    protected $casts = [
        'pelatihan_dasar' => 'array',
        'pelatihan_peningkatan_kompetensi' => 'array',
    ];

    /**
     * Menghitung total JPL berdasarkan tahun tertentu
     */
    public function getTotalJplByYear($year)
    {
        $total = 0;
        
        // Gabungkan kedua jenis pelatihan
        $allPelatihan = array_merge(
            $this->pelatihan_dasar ?? [],
            $this->pelatihan_peningkatan_kompetensi ?? []
        );

        foreach ($allPelatihan as $p) {
            if (isset($p['tahun']) && $p['tahun'] == $year) {
                $total += (int)($p['jpl'] ?? 0);
            }
        }

        return $total;
    }
}