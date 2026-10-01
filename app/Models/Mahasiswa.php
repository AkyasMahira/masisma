<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Absensi;
use App\Models\Ruangan;
use App\Models\RoomSequence;
use App\Models\ShiftSchedule;
use App\Models\Mou;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswas';

    // 1. FILLABLE (PASTIKAN 'is_edited' ADA)
    protected $fillable = [
        'user_id',
        'nm_mahasiswa',
        'mou_id',
        'prodi',
        'no_hp',
        'nm_ruangan',     // Field legacy (opsional jika sudah pakai roomSequences)
        'ruangan_id',     // Ruangan Master
        'status',
        'share_token',
        'tanggal_mulai',
        'tipe_mahasiswa',
        'tanggal_berakhir',
        'weekend_aktif',
        'foto_path',
        'is_edited',          
        'is_id_card_approved',
        'nilai_karu',
        'nilai_ruangan_json',
        'nilai_evaluasi',
        'kompetensi_json',
        'kompetensi_dimiliki_json',
    ];

    protected $appends = ['sisa_hari', 'absensi_percentage', 'statistik'];

    protected $casts = [
        'weekend_aktif' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'is_edited' => 'boolean',
        'nilai_evaluasi' => 'array',
        'nilai_ruangan_json' => 'array',
        'kompetensi_json' => 'array',
        'kompetensi_dimiliki_json' => 'array',
    ];

    public const STATUS_ACTIVE = 'aktif';
    public const STATUS_INACTIVE = 'nonaktif';

    // 2. BOOT (UUID TOKEN)
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->share_token)) {
                $model->share_token = (string) Str::uuid();
            }
        });
    }

    // --- SCOPES ---
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // =========================================================================
    // 3. DEFINISI RELASI (RELATIONSHIPS)
    // =========================================================================
    
    public function user() 
    { 
        return $this->belongsTo(\App\Models\User::class); 
    }

    public function absensis() 
    { 
        return $this->hasMany(Absensi::class, 'mahasiswa_id'); 
    }

    public function mou() 
    { 
        return $this->belongsTo(Mou::class, 'mou_id'); 
    }

    public function ruangan() 
    { 
        return $this->belongsTo(Ruangan::class, 'ruangan_id'); 
    }
    
    public function roomSequences()
    {
        return $this->hasMany(RoomSequence::class, 'mahasiswa_id');
    }

    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class, 'mahasiswa_id');
    }
// Tambahkan di dalam class Mahasiswa
public function dispensasis()
{
    return $this->hasMany(Dispensasi::class, 'mahasiswa_id');
}
    // =========================================================================
    // 4. ACCESSORS (LOGIKA HITUNGAN PINTAR)
    // =========================================================================

    /**
     * Menghitung Statistik Lengkap (Hadir, Alpha, Target, Sisa)
     * Mengabaikan hari dengan Shift = 'Libur'
     */
   /**
     * Menghitung Statistik Lengkap (Hadir, Alpha, Target, Sisa)
     * Menggunakan Sistem Poin: Masuk=1, Dispensasi Biasa=1, Terlambat=0.8
     */
    public function getStatistikAttribute()
    {
        $default = [
            'hadir' => 0, 'hadir_fisik' => 0, 'dispensasi_biasa' => 0, 'dispensasi_terlambat' => 0, 
            'alpha' => 0, 'target_sekarang' => 0, 'target_total' => 0, 'sisa_kerja' => 0
        ];

        if (!$this->tanggal_mulai || !$this->tanggal_berakhir) {
            return (object) $default;
        }

        try {
            $startStr = optional($this->tanggal_mulai)->format('Y-m-d');
            $endStr   = optional($this->tanggal_berakhir)->format('Y-m-d');
            $todayStr = now()->format('Y-m-d');

            // 1. Ambil Peta Shift (Tanggal => Tipe)
            $shiftMap = $this->shiftSchedules()->pluck('shift_type', 'tanggal')->toArray();

            // 2. Kumpulkan Hari Wajib Kerja Total
            $targetTotal = 0;
            $periodTotal = \Carbon\CarbonPeriod::create($startStr, $endStr);
            foreach ($periodTotal as $dt) {
                $dateStr = $dt->format('Y-m-d');
                $shiftType = $shiftMap[$dateStr] ?? null;
                
                if ($shiftType === 'Libur') continue;
                if (!$shiftType && !$this->weekend_aktif && $dt->isWeekend()) continue;
                
                $targetTotal++;
            }

            // 3. Kumpulkan Peta Hari Wajib Kerja Sampai Hari Ini
            $calcEndStr = ($todayStr < $endStr) ? $todayStr : $endStr;
            $targetSekarang = 0;
            $workingDays = []; // Array untuk menyimpan tanggal wajib masuk
            
            if ($todayStr >= $startStr) {
                $periodRunning = \Carbon\CarbonPeriod::create($startStr, $calcEndStr);
                foreach ($periodRunning as $dt) {
                    $dateStr = $dt->format('Y-m-d');
                    $shiftType = $shiftMap[$dateStr] ?? null;
                    
                    if ($shiftType === 'Libur') continue;
                    if (!$shiftType && !$this->weekend_aktif && $dt->isWeekend()) continue;

                    $targetSekarang++;
                    $workingDays[] = $dateStr;
                }
            }

            // 4. Cek Kehadiran Fisik Aktual (dari mesin absen)
            $tappedInDates = [];
            foreach ($this->absensis as $absen) {
                if ($absen->type !== 'masuk' || !$absen->created_at) continue;
                $tgl = $absen->created_at->format('Y-m-d');
                
                // Pastikan yang dihitung hanya hari wajib masuk
                if (in_array($tgl, $workingDays) && !in_array($tgl, $tappedInDates)) {
                    $tappedInDates[] = $tgl;
                }
            }

            // 5. Cek Peta Dispensasi yang Approved
            $dispensasiBiasaDates = [];
            $dispensasiTerlambatDates = [];
            
            $approvedDispensasis = $this->dispensasis()->where('status', 'approved')->get();
            foreach ($approvedDispensasis as $disp) {
                $mulai = \Carbon\Carbon::parse($disp->tanggal_mulai);
                $selesai = \Carbon\Carbon::parse($disp->tanggal_selesai);
                $periodDisp = \Carbon\CarbonPeriod::create($mulai, $selesai);

                foreach ($periodDisp as $dt) {
                    $tgl = $dt->format('Y-m-d');
                    // Hanya memproses dispensasi jika hari tersebut memang hari wajib kerja (bukan pas hari libur)
                    if (in_array($tgl, $workingDays)) {
                        if (strtolower($disp->kategori) === 'terlambat') {
                            $dispensasiTerlambatDates[] = $tgl;
                        } else {
                            $dispensasiBiasaDates[] = $tgl;
                        }
                    }
                }
            }

            // 6. Hitung Poin Kehadiran (Logika Utama)
            $poinHadir = 0;
            $hadirFisik = 0;
            $totalBiasa = 0;
            $totalTerlambat = 0;
            $alpha = 0;

            foreach ($workingDays as $tgl) {
                // Cek hierarki kehadiran hari ini. 
                // Jika dia telat, walaupun dia absen di mesin, tetap dihitung telat (0.8)
                if (in_array($tgl, $dispensasiBiasaDates)) {
                    $poinHadir += 1;
                    $totalBiasa++;
                } elseif (in_array($tgl, $dispensasiTerlambatDates)) {
                    $poinHadir += 0.9;
                    $totalTerlambat++;
                } elseif (in_array($tgl, $tappedInDates)) {
                    $poinHadir += 1;
                    $hadirFisik++;
                } else {
                    $alpha++;
                }
            }

            return (object) [
                'hadir' => $poinHadir, // Poin akhir ini yang menentukan kelulusan
                'hadir_fisik' => $hadirFisik,
                'dispensasi_biasa' => $totalBiasa,
                'dispensasi_terlambat' => $totalTerlambat,
                'alpha' => $alpha,
                'target_sekarang' => $targetSekarang,
                'target_total' => $targetTotal,
                'sisa_kerja' => max(0, $targetTotal - $targetSekarang)
            ];

        } catch (\Exception $e) {
            return (object) $default;
        }
    }

    /**
     * Menghitung Persentase Kehadiran
     */
    /**
     * Menghitung Persentase Kehadiran
     */
    public function getAbsensiPercentageAttribute()
    {
        $stat = $this->statistik; 
        
        if ($stat->target_sekarang == 0) return 0; // Hindari division by zero

        // Rumus: (Total Poin / Target Hari) * 100
        $percentage = ($stat->hadir / $stat->target_sekarang) * 100;
        
        // Dibulatkan 1 angka di belakang koma (misal: 88.5%)
        return round(min($percentage, 100), 1); 
    }

    // --- HELPER ATTRIBUTES ---

    public function getUnivAsalAttribute()
    {
        return $this->mou ? ($this->mou->nama_instansi ?? $this->mou->nama_universitas) : null;
    }

    public function getJadwalAktifAttribute()
    {
        $today = \Carbon\Carbon::now()->toDateString();
        return $this->roomSequences()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('start_date', 'desc')
            ->first();
    }

    public function getSisaHariAttribute()
    {
        if (empty($this->tanggal_berakhir)) return '-';

        try {
            $today = \Carbon\Carbon::now()->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->tanggal_berakhir)->startOfDay();

            if ($today->gt($endDate)) return 'Selesai';

            $diff = $today->diffInDays($endDate);
            return ($diff === 0) ? 'Hari Terakhir' : $diff . ' Hari';
        } catch (\Exception $e) {
            return '-';
        }
    }
/**
     * Mendapatkan Nilai Karu Final 
     * Aturan: Total Nilai dibagi dengan TOTAL RUANGAN UNIK yang dijadwalkan.
     */
    public function getNilaiKaruFinalAttribute()
    {
        // 1. Hitung jumlah ruangan UNIK, bukan jumlah baris jadwal mingguan
        $totalRuanganDitugaskan = $this->roomSequences->pluck('ruangan_id')->unique()->count();

        // 2. Jika ada data JSON, proses perhitungan
        if (!empty($this->nilai_ruangan_json) && is_array($this->nilai_ruangan_json)) {
            
            // Jumlahkan total skor yang sudah masuk
            $totalNilai = array_sum($this->nilai_ruangan_json);
            
            // Tentukan angka pembagi berdasarkan RUANGAN UNIK
            $pembagi = $totalRuanganDitugaskan > 0 ? $totalRuanganDitugaskan : count($this->nilai_ruangan_json);

            // Hitung rata-rata: Total Skor / Total Ruangan Unik
            return $pembagi > 0 ? round($totalNilai / $pembagi, 1) : 0;
        }

        // 3. Fallback: Jika JSON kosong, gunakan nilai_karu lama yang murni angka
        $rawNilaiKaru = $this->nilai_karu ?? 0;
        
        return is_numeric($rawNilaiKaru) ? (float) $rawNilaiKaru : 0;
    }
    // Mendapatkan Nama Ruangan Saat Ini (Cek Rolling dulu, baru Master)
    public function getNamaRuanganSaatIniAttribute()
    {
        $jadwal = $this->jadwal_aktif;
        if($jadwal && $jadwal->ruangan) {
            return $jadwal->ruangan->nm_ruangan;
        }
        
        if ($this->ruangan) {
            return $this->ruangan->nm_ruangan ?? $this->ruangan->nama_ruangan;
        }

        return $this->nm_ruangan ?? '-';
    }
    
    // Mendapatkan Object Ruangan Aktif
    public function getRuanganAktifAttribute()
    {
        $jadwal = $this->jadwal_aktif;
        if ($jadwal) {
            return $jadwal->ruangan;
        }
        return $this->ruangan; 
    }
}