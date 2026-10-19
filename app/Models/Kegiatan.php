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
        'jpl', 'token_absensi', 'status_absen', 'warna_tema', 'hari_libur'
    ];

    // Beritahu Laravel otomatis convert array ke JSON (dan sebaliknya)
    protected $casts = [
        'keahlian' => 'array',
        'hari_libur' => 'array',   // tanggal (Y-m-d) di dalam rentang yang tidak ada sesi
    ];

    /** Daftar tanggal (Y-m-d) yang ADA sesi: rentang mulai–selesai dikurangi hari_libur. */
    public function hariSesi()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) return [];
        $libur = array_flip((array) ($this->hari_libur ?: []));
        $out = [];
        $d = \Carbon\Carbon::parse($this->tanggal_mulai)->startOfDay();
        $akhir = \Carbon\Carbon::parse($this->tanggal_selesai)->startOfDay();
        $guard = 0;
        while ($d->lte($akhir) && $guard++ < 400) {
            if (!isset($libur[$d->toDateString()])) $out[] = $d->toDateString();
            $d->addDay();
        }
        return $out;
    }

    /** Apakah tanggal tsb hari libur (tidak ada sesi) di dalam rentang pelatihan. */
    public function isHariLibur($tanggal)
    {
        $t = \Carbon\Carbon::parse($tanggal)->toDateString();
        return in_array($t, (array) ($this->hari_libur ?: []), true);
    }

    /** Sesi berikutnya setelah tanggal tsb (Y-m-d) atau null. */
    public function sesiBerikutnya($tanggal)
    {
        $t = \Carbon\Carbon::parse($tanggal)->toDateString();
        foreach ($this->hariSesi() as $s) { if ($s > $t) return $s; }
        return null;
    }

    /**
     * Teks tanggal pelaksanaan, mengelompokkan hari berurutan.
     * Contoh: "19 - 20 & 23 - 24 Oktober 2026".
     */
    public function teksTanggalPelaksanaan()
    {
        $hari = $this->hariSesi();
        if (!$hari) return '';
        // kelompokkan hari yang berurutan
        $grup = []; $mulai = $hari[0]; $prev = $hari[0];
        for ($i = 1; $i <= count($hari); $i++) {
            $cur = $hari[$i] ?? null;
            if ($cur && \Carbon\Carbon::parse($prev)->addDay()->toDateString() === $cur) { $prev = $cur; continue; }
            $grup[] = [\Carbon\Carbon::parse($mulai), \Carbon\Carbon::parse($prev)];
            if ($cur) { $mulai = $cur; $prev = $cur; }
        }
        $akhir = end($grup)[1];
        $parts = [];
        foreach ($grup as $idx => $g) {
            list($a, $b) = $g;
            $last = $idx === count($grup) - 1;
            // tampilkan bulan/tahun hanya bila berbeda dengan tanggal terakhir keseluruhan
            $fmtB = $last ? 'D MMMM YYYY' : ($b->format('Y-m') === $akhir->format('Y-m') ? 'D' : ($b->format('Y') === $akhir->format('Y') ? 'D MMMM' : 'D MMMM YYYY'));
            if ($a->isSameDay($b)) {
                $parts[] = $b->isoFormat($fmtB);
            } else {
                $fmtA = $a->format('Y-m') === $b->format('Y-m') ? 'D' : ($a->format('Y') === $b->format('Y') ? 'D MMMM' : 'D MMMM YYYY');
                $parts[] = $a->isoFormat($fmtA) . ' - ' . $b->isoFormat($fmtB);
            }
        }
        return count($parts) > 1 ? implode(', ', array_slice($parts, 0, -1)) . ' & ' . end($parts) : $parts[0];
    }

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