<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;
use App\Models\RuanganKetersediaan;

class Ruangan extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    // BARIS INI SANGAT PENTING
    protected $table = 'ruangans';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
   // App\Models\Ruangan.php

protected $fillable = [
    'nm_ruangan',
    'kategori',
    'kuota_ruangan',
    'user_id', // <--- Tambahkan ini
];

public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

    public function mahasiswa()
    {
        return $this->hasMany(\App\Models\Mahasiswa::class, 'ruangan_id');
    }

    public function kuotaKetersediaan()
    {
        return $this->hasMany(RuanganKetersediaan::class, 'ruangan_id');
    }

    /**
     * Get kuota tersedia for a date (or latest) falling back to kuota_ruangan when no snapshot exists.
     *
     * @param string|null $date YYYY-MM-DD
     * @return int
     */
     // Tambahkan/Update method ini di dalam class Ruangan

// Helper untuk menghitung mahasiswa aktif
public function getMahasiswaCountAttribute()
{
    // Menggunakan collection filtering (agar tidak query ulang ke DB)
    return $this->mahasiswa->where('status', 'aktif')->count();
}
    public function getKuotaTersedia($date = null)
    {
        if ($date === null) {
            $date = now()->toDateString();
        }

        $record = $this->kuotaKetersediaan()->where('tanggal', $date)->latest('id')->first();
        if ($record) {
            return (int) $record->tersedia;
        }

        // fallback to existing column if snapshot not present
        return isset($this->kuota_ruangan) ? (int) $this->kuota_ruangan : 0;
    }
    
    public function roomShifts()
    {
        return $this->hasMany(\App\Models\RoomShift::class, 'ruangan_id');
    }
    /**
 * Sync kuota dengan jumlah mahasiswa aktual
 */
public static function syncAllKuota()
{
    $ruangans = self::all();
    foreach ($ruangans as $ruangan) {
        $actualCount = Mahasiswa::where('ruangan_id', $ruangan->id)->count();
        $ruangan->kuota_ruangan = $ruangan->kapasitas_total - $actualCount;
        $ruangan->save();

        // write snapshot for today
        RuanganKetersediaan::updateOrCreate(
            ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
            ['tersedia' => $ruangan->kuota_ruangan]
        );
    }
}
}
