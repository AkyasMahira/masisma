<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Absensi;
use App\Models\Ruangan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nm_mahasiswa',
        'mou_id',
        'prodi',
        'no_hp',
        'nm_ruangan',
        'ruangan_id',
        'status',
        'share_token',
        'tanggal_mulai',
        'tanggal_berakhir',
        'weekend_aktif',
        'foto_path',
    ];

    protected $appends = ['sisa_hari', 'absensi_percentage'];

    // $casts baru untuk handling boolean
    protected $casts = [
        'weekend_aktif' => 'boolean', // <-- BARU
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public const STATUS_ACTIVE = 'aktif';
    public const STATUS_INACTIVE = 'nonaktif';

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->share_token)) {
                $model->share_token = (string) Str::uuid();
            }
        });
    }

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

    public function getSisaHariAttribute()
    {
        if (!$this->tanggal_berakhir) {
            return '-';
        }

        $today = now()->startOfDay();
        $endDate = \Carbon\Carbon::parse($this->tanggal_berakhir)->startOfDay();

        if ($today > $endDate) {
            return 'Selesai';
        }

        return $today->diffInDays($endDate) . ' hari';
    }

    public function getAbsensiPercentageAttribute()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_berakhir) {
            return 0;
        }

        $startDate = $this->tanggal_mulai;
        // Kita hitung sampai tanggal berakhir (inklusif)
        $endDate = $this->tanggal_berakhir;

        // 1. Hitung total hari yang seharusnya masuk
        $period = CarbonPeriod::create($startDate, $endDate);
        $totalExpectedDays = 0;

        foreach ($period as $date) {
            // Jika weekend_aktif = true, hitung semua hari
            if ($this->weekend_aktif) {
                $totalExpectedDays++;
            } else {
                // Jika weekend_aktif = false, jangan hitung Sabtu (6) dan Minggu (0)
                if (!$date->isWeekend()) {
                    $totalExpectedDays++;
                }
            }
        }

        // Handle jika total hari 0 (misal mulai & selesai di hari yg sama saat weekend)
        if ($totalExpectedDays == 0) {
            return 0;
        }

        // 2. Hitung total hadir aktual
        $totalHadir = $this->absensis()
            ->where('type', 'hadir')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->count();

        // 3. Hitung persentase
        $percentage = ($totalHadir / $totalExpectedDays) * 100;

        // Kembalikan 2 angka desimal, misal: 95.50
        return round($percentage, 2);
    }

    // 1. Relasi ke RoomSchedule
    public function schedules()
    {
        return $this->hasMany(RoomSchedule::class, 'mahasiswa_id');
    }

    // 2. Buat Atribut Kustom: "ruangan_aktif"
    // Ini akan otomatis mencari ruangan berdasarkan tanggal hari ini
    public function getRuanganAktifAttribute()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Cek apakah ada jadwal rolling aktif HARI INI?
        $jadwalRolling = $this->schedules()
            ->with('ruangan') // Load data ruangannya
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($jadwalRolling) {
            // PRIORITAS 1: Ambil dari Jadwal Rolling
            return $jadwalRolling->ruangan; 
        }

        // PRIORITAS 2: Ambil dari data master (fallback)
        return $this->ruangan; 
    }

    // 3. Helper untuk langsung ambil Namanya saja
    public function getNamaRuanganSaatIniAttribute()
    {
        // Panggil logika di atas
        $ruangan = $this->ruangan_aktif;

        if ($ruangan) {
            // Cek kolom nama ruangan di db kamu (nama_ruangan atau nm_ruangan)
            return $ruangan->nama_ruangan ?? $ruangan->nm_ruangan ?? '-';
        }

        // PRIORITAS 3: Jika relasi null, cek kolom string nm_ruangan (legacy)
        return $this->nm_ruangan ?? '-';
    }

}
