<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterEvaluasi extends Model
{
    protected $table = 'master_evaluasi';
    protected $fillable = ['jenis', 'kegiatan_id', 'kode', 'pertanyaan', 'tipe', 'urutan', 'aktif'];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    const JENIS = ['pelatihan' => 'Pelatihan', 'pendidikan' => 'Pendidikan', 'penelitian' => 'Penelitian'];

    public function jawaban()
    {
        return $this->hasMany(EvaluasiJawaban::class, 'master_evaluasi_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }

    /**
     * Pertanyaan yang dipakai di form.
     * Pelatihan: pertanyaan khusus pelatihan tsb bila ada, selain itu default (master).
     */
    public static function untukForm($jenis, $kegiatanId = null)
    {
        if ($jenis === 'pelatihan' && $kegiatanId) {
            $khusus = static::aktif()->where('jenis', 'pelatihan')->where('kegiatan_id', $kegiatanId)
                ->orderBy('urutan')->orderBy('id')->get();
            if ($khusus->count()) return $khusus;
        }
        return static::aktif()->where('jenis', $jenis)->whereNull('kegiatan_id')
            ->orderBy('urutan')->orderBy('id')->get();
    }
}
