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
        'evaluasi_at',
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
            'lupa_pulang' => 0, 'alpha' => 0, 'target_sekarang' => 0, 'target_total' => 0, 'sisa_kerja' => 0,
        ];

        if (!$this->tanggal_mulai || !$this->tanggal_berakhir) {
            return ['stat' => $default, 'kalender' => []];
        }

        try {
            $startStr = optional($this->tanggal_mulai)->format('Y-m-d');
            $endStr   = optional($this->tanggal_berakhir)->format('Y-m-d');
            $todayStr = now()->format('Y-m-d');

            $shiftMap = $this->shiftSchedules()->pluck('shift_type', 'tanggal')->toArray();

            // Peta dispensasi approved (biasa / terlambat / lupa_pulang) per tanggal
            $dispBiasa = [];
            $dispTerlambat = [];
            $dispLupaPulang = []; // dispensasi lupa pulang yang di-ACC (bobot 90%, sama seperti terlambat)
            foreach ($this->dispensasis()->where('status', 'approved')->get() as $disp) {
                foreach (\Carbon\CarbonPeriod::create(\Carbon\Carbon::parse($disp->tanggal_mulai), \Carbon\Carbon::parse($disp->tanggal_selesai)) as $dt) {
                    $tgl = $dt->format('Y-m-d');
                    $kat = strtolower($disp->kategori);
                    if ($kat === 'terlambat') $dispTerlambat[$tgl] = true;
                    elseif ($kat === 'lupa_pulang') $dispLupaPulang[$tgl] = true;
                    else $dispBiasa[$tgl] = true;
                }
            }

            // Tanggal tap masuk aktual, dan tanggal sesi yang SUDAH checkout (keluar)
            $tappedIn = [];
            $keluarDates = [];
            foreach ($this->absensis as $absen) {
                if ($absen->type === 'masuk' && $absen->created_at) {
                    $tappedIn[$absen->created_at->format('Y-m-d')] = true;
                } elseif ($absen->type === 'keluar' && $absen->jam_masuk) {
                    // Sesi dianggap lengkap berdasarkan TANGGAL MASUK sesi tsb (aman untuk backdate)
                    $keluarDates[\Carbon\Carbon::parse($absen->jam_masuk)->format('Y-m-d')] = true;
                }
            }

            $targetTotal = 0; $targetSekarang = 0;
            $poinHadir = 0; $hadirFisik = 0; $totalBiasa = 0; $totalTerlambat = 0; $alpha = 0; $lupaPulang = 0;
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
                } elseif (isset($dispLupaPulang[$tgl])) {
                    // Dispensasi lupa pulang di-ACC: bobot 90% (digabung ke bucket terlambat untuk agregat)
                    $poinHadir += 0.9; $totalTerlambat++;
                    $kalender[$tgl] = ['status' => 'terlambat', 'label' => 'Dispensasi Lupa Pulang (90%)'];
                } elseif (isset($tappedIn[$tgl])) {
                    if (isset($keluarDates[$tgl]) || $tgl === $todayStr) {
                        // Lengkap (ada checkout), atau hari ini (masih bisa checkout) -> hadir penuh
                        $poinHadir += 1; $hadirFisik++;
                        $kalender[$tgl] = ['status' => 'hadir', 'label' => 'Hadir'];
                    } else {
                        // Tap masuk tapi tidak pernah checkout (hari lampau) -> LUPA PULANG, bobot 0.8
                        $poinHadir += 0.8; $lupaPulang++;
                        $kalender[$tgl] = ['status' => 'lupa', 'label' => 'Lupa Pulang (80%)'];
                    }
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
                    'lupa_pulang' => $lupaPulang,
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
    /**
     * SATU SUMBER kalender (jadwal shift/ruangan + realisasi absensi + dispensasi).
     * Dipakai dashboard mahasiswa & halaman detail admin agar kalendernya identik.
     */
    public function kalenderEvents()
    {
        $events = [];
        $today = now()->format('Y-m-d');
        $manualShifts = $this->shiftSchedules->keyBy('tanggal');
        $liburDates = [];

        // A/B. JADWAL (ruangan non-shift & shift)
        foreach ($this->roomSequences as $seq) {
            if (!$seq->ruangan) continue;
            $kategoriRuangan = $seq->ruangan->kategori ?? 'non_shift';
            $ruangName = $seq->ruangan->nm_ruangan;
            $customShifts = $seq->ruangan->roomShifts->keyBy('nama_shift');

            foreach (\Carbon\CarbonPeriod::create($seq->start_date, $seq->end_date) as $dt) {
                $dStr = $dt->format('Y-m-d');
                $dayOfWeek = $dt->dayOfWeekIso;

                if ($kategoriRuangan === 'non_shift') {
                    if ($dt->isWeekend() && !$this->weekend_aktif) {
                        $liburDates[$dStr] = true;
                        $events[] = ['title' => 'LIBUR', 'start' => $dStr, 'color' => '#6c757d', 'extendedProps' => ['jam' => 'Sabtu/Minggu', 'ruang' => $ruangName, 'type' => 'jadwal']];
                        continue;
                    }
                    $shiftName = ($dayOfWeek == 5) ? 'Jumat' : 'Reguler';
                    $jam = ($dayOfWeek == 5) ? '07:00 - 14:30' : '07:15 - 15:30';
                    if (isset($customShifts[$shiftName])) {
                        $jam = \Carbon\Carbon::parse($customShifts[$shiftName]->jam_masuk)->format('H:i') . ' - ' .
                               \Carbon\Carbon::parse($customShifts[$shiftName]->jam_keluar)->format('H:i');
                    }
                    $events[] = ['title' => $shiftName, 'start' => $dStr, 'color' => '#6610f2', 'extendedProps' => ['jam' => $jam, 'ruang' => $ruangName, 'type' => 'jadwal']];
                } else {
                    if (isset($manualShifts[$dStr])) {
                        $type = $manualShifts[$dStr]->shift_type;
                        if ($type === 'Libur') {
                            $liburDates[$dStr] = true;
                            $jamShift = 'Istirahat'; $color = '#6c757d';
                        } else {
                            $color = '#0d6efd'; $jamShift = 'Jam Default';
                            if (isset($customShifts[$type])) {
                                $jamShift = \Carbon\Carbon::parse($customShifts[$type]->jam_masuk)->format('H:i') . ' - ' .
                                            \Carbon\Carbon::parse($customShifts[$type]->jam_keluar)->format('H:i');
                            } elseif ($type == 'Pagi') $jamShift = '07:00 - 14:00';
                            elseif ($type == 'Siang') $jamShift = '14:00 - 21:00';
                            elseif ($type == 'Malam') $jamShift = '21:00 - 07:00';
                        }
                        $events[] = ['title' => ucfirst($type), 'start' => $dStr, 'color' => $color, 'extendedProps' => ['jam' => $jamShift, 'ruang' => $ruangName, 'type' => 'jadwal']];
                    }
                }
            }
        }

        // C. REALISASI ABSENSI
        // Satu sumber dengan hitungKehadiran(): masuk dipetakan ke tanggal tap (created_at),
        // keluar dipetakan ke tanggal SESI (jam_masuk) agar shift malam yang checkout
        // lewat tengah malam tetap berpasangan di tanggal yang sama (bukan dianggap LUPA).
        $masukByDate = [];
        $keluarBySession = [];
        foreach ($this->absensis as $absen) {
            if ($absen->type === 'masuk' && $absen->created_at) {
                $masukByDate[$absen->created_at->format('Y-m-d')] = $absen;
            } elseif ($absen->type === 'keluar' && $absen->jam_masuk) {
                $keluarBySession[\Carbon\Carbon::parse($absen->jam_masuk)->format('Y-m-d')] = $absen;
            }
        }
        // Tanggal yang punya dispensasi "lupa pulang" di-ACC: jangan dicat merah LUPA lagi (sudah diurus)
        $lupaPulangDispDates = [];
        foreach ($this->dispensasis()->where('status', 'approved')->where('kategori', 'lupa_pulang')->get() as $d) {
            foreach (\Carbon\CarbonPeriod::create($d->tanggal_mulai, $d->tanggal_selesai) as $dt) {
                $lupaPulangDispDates[$dt->format('Y-m-d')] = true;
            }
        }
        foreach ($masukByDate as $date => $masuk) {
            $hasKeluar = $keluarBySession[$date] ?? null;
            if ($hasKeluar) {
                $cM = \Carbon\Carbon::parse($masuk->jam_masuk);
                $cK = \Carbon\Carbon::parse($hasKeluar->jam_keluar);
                $jamM = $cM->format('H:i');
                $jamK = $cK->format('H:i');
                // Checkout lewat tengah malam (shift malam): tandai +N hari biar jelas
                $lintasHari = $cK->toDateString() > $cM->toDateString();
                if ($lintasHari) {
                    $selisihHari = \Carbon\Carbon::parse($cM->toDateString())
                        ->diffInDays(\Carbon\Carbon::parse($cK->toDateString()));
                    $jamK .= ' (+' . $selisihHari . ' hari)';
                }
                $events[] = ['title' => 'HADIR', 'start' => $date, 'color' => '#198754', 'extendedProps' => ['jam' => "$jamM - $jamK", 'ruang' => $lintasHari ? 'Hadir • Shift Malam' : 'Absen', 'type' => 'absen']];
            } elseif (isset($lupaPulangDispDates[$date])) {
                // Lupa pulang sudah diajukan & di-ACC -> tampil sebagai dispensasi (bukan LUPA merah)
                $events[] = ['title' => 'DISPEN', 'start' => $date, 'color' => '#fd7e14', 'extendedProps' => ['jam' => \Carbon\Carbon::parse($masuk->jam_masuk)->format('H:i') . ' - (ACC)', 'ruang' => 'Dispen Lupa Pulang', 'type' => 'izin']];
            } else {
                $isToday = $date == $today;
                $events[] = ['title' => $isToday ? 'KERJA' : 'LUPA', 'start' => $date, 'color' => $isToday ? '#ffc107' : '#dc3545', 'extendedProps' => ['jam' => \Carbon\Carbon::parse($masuk->jam_masuk)->format('H:i') . ' - ?', 'ruang' => $isToday ? 'Belum checkout' : 'Lupa Pulang', 'type' => 'absen']];
            }
        }

        // D. DISPENSASI (lupa_pulang sudah dirender di bagian C, jadi dilewati di sini)
        $katLabel = ['biasa' => 'Izin / Sakit', 'terlambat' => 'Dispen Terlambat'];
        foreach ($this->dispensasis()->where('status', 'approved')->get() as $dispen) {
            if (strtolower($dispen->kategori) === 'lupa_pulang') continue;
            $events[] = [
                'title' => 'IZIN', 'start' => $dispen->tanggal_mulai,
                'end'   => \Carbon\Carbon::parse($dispen->tanggal_selesai)->addDay()->format('Y-m-d'),
                'color' => '#fd7e14',
                'extendedProps' => ['jam' => $katLabel[strtolower($dispen->kategori)] ?? 'Dispensasi', 'ruang' => 'Dispensasi', 'type' => 'izin'],
            ];
        }

        return $events;
    }

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