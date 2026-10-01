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
        return (object) $this->hitungKehadiran()['stat'];
    }

    /**
     * Kalender kehadiran harian (SATU SUMBER untuk semua dashboard).
     * [ 'YYYY-MM-DD' => ['status' => hadir|terlambat|izin|alpha|belum|libur, 'label' => ...] ]
     */
    public function getKalenderKehadiranAttribute()
    {
        return $this->hitungKehadiran()['kalender'];
    }

    /**
     * SATU-SATUNYA logika perhitungan kehadiran (statistik + kalender).
     * Dipakai oleh getStatistikAttribute, getKalenderKehadiranAttribute,
     * getAbsensiPercentageAttribute, dan semua dashboard/sertifikat.
     *
     * Bobot: hadir normal / dispensasi biasa = 1 (100%), dispensasi terlambat = 0.9 (90%).
     * Hari ini TIDAK dihitung alpha sampai hari berakhir (status 'belum').
     */
    public function hitungKehadiran($scopeDates = null)
    {
        // $scopeDates: null = seluruh periode magang (overall); array 'Y-m-d' = batasi ke tanggal itu
        // (dipakai dashboard kepala ruangan untuk performa per-ruangan, formula tetap sama).
        $scope = is_array($scopeDates) ? array_flip($scopeDates) : null;

        $default = [
            'hadir' => 0, 'hadir_fisik' => 0, 'dispensasi_biasa' => 0, 'dispensasi_terlambat' => 0,
            'alpha' => 0, 'target_sekarang' => 0, 'target_total' => 0, 'sisa_kerja' => 0,
        ];

        if (!$this->tanggal_mulai || !$this->tanggal_berakhir) {
            return ['stat' => $default, 'kalender' => []];
        }

        try {
            $startStr = optional($this->tanggal_mulai)->format('Y-m-d');
            $endStr   = optional($this->tanggal_berakhir)->format('Y-m-d');
            $todayStr = now()->format('Y-m-d');

            $shiftMap = $this->shiftSchedules()->pluck('shift_type', 'tanggal')->toArray();

            // Peta dispensasi approved (biasa / terlambat) per tanggal
            $dispBiasa = [];
            $dispTerlambat = [];
            foreach ($this->dispensasis()->where('status', 'approved')->get() as $disp) {
                foreach (\Carbon\CarbonPeriod::create(\Carbon\Carbon::parse($disp->tanggal_mulai), \Carbon\Carbon::parse($disp->tanggal_selesai)) as $dt) {
                    $tgl = $dt->format('Y-m-d');
                    if (strtolower($disp->kategori) === 'terlambat') $dispTerlambat[$tgl] = true;
                    else $dispBiasa[$tgl] = true;
                }
            }

            // Tanggal tap masuk aktual
            $tappedIn = [];
            foreach ($this->absensis as $absen) {
                if ($absen->type !== 'masuk' || !$absen->created_at) continue;
                $tappedIn[$absen->created_at->format('Y-m-d')] = true;
            }

            $targetTotal = 0; $targetSekarang = 0;
            $poinHadir = 0; $hadirFisik = 0; $totalBiasa = 0; $totalTerlambat = 0; $alpha = 0;
            $kalender = [];

            foreach (\Carbon\CarbonPeriod::create($startStr, $endStr) as $dt) {
                $tgl = $dt->format('Y-m-d');

                // Scope per-ruangan: lewati tanggal di luar penugasan ruangan tsb
                if ($scope !== null && !isset($scope[$tgl])) continue;

                $shiftType = $shiftMap[$tgl] ?? null;

                // Hari libur / akhir pekan (tidak wajib)
                $isLibur = ($shiftType === 'Libur') || (!$shiftType && !$this->weekend_aktif && $dt->isWeekend());
                if ($isLibur) {
                    $kalender[$tgl] = ['status' => 'libur', 'label' => 'Libur'];
                    continue;
                }

                $targetTotal++;

                // Masa depan (belum sampai hari ini)
                if ($tgl > $todayStr) {
                    $kalender[$tgl] = ['status' => 'future', 'label' => '-'];
                    continue;
                }

                $targetSekarang++;

                if (isset($dispBiasa[$tgl])) {
                    $poinHadir += 1; $totalBiasa++;
                    $kalender[$tgl] = ['status' => 'izin', 'label' => 'Izin/Dispensasi'];
                } elseif (isset($dispTerlambat[$tgl])) {
                    $poinHadir += 0.9; $totalTerlambat++;
                    $kalender[$tgl] = ['status' => 'terlambat', 'label' => 'Dispensasi Terlambat (90%)'];
                } elseif (isset($tappedIn[$tgl])) {
                    $poinHadir += 1; $hadirFisik++;
                    $kalender[$tgl] = ['status' => 'hadir', 'label' => 'Hadir'];
                } elseif ($tgl === $todayStr) {
                    // Hari ini belum berakhir -> jangan hitung alpha
                    $targetSekarang--;
                    $kalender[$tgl] = ['status' => 'belum', 'label' => 'Belum absen (hari ini)'];
                } else {
                    $alpha++;
                    $kalender[$tgl] = ['status' => 'alpha', 'label' => 'Alpha'];
                }
            }

            return [
                'stat' => [
                    'hadir' => $poinHadir,
                    'hadir_fisik' => $hadirFisik,
                    'dispensasi_biasa' => $totalBiasa,
                    'dispensasi_terlambat' => $totalTerlambat,
                    'alpha' => $alpha,
                    'target_sekarang' => $targetSekarang,
                    'target_total' => $targetTotal,
                    'sisa_kerja' => max(0, $targetTotal - $targetSekarang),
                ],
                'kalender' => $kalender,
            ];
        } catch (\Exception $e) {
            return ['stat' => $default, 'kalender' => []];
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