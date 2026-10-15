<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensasi extends Model
{
    // Kita hapus 'use HasFactory' agar tidak error lagi
    
    protected $table = 'dispensasis';

    protected $fillable = [
        'mahasiswa_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'file_path',
        'status',
        'catatan_admin',
        'kategori',
        'keterangan_terlambat',
    ];

    // Relasi ke tabel Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Dispensasi yang menjadi wewenang ruangan $ruanganId: tanggalnya jatuh pada
     * periode mahasiswa dijadwalkan di ruangan itu (room_sequences / shift_schedules).
     * Mahasiswa rolling: dispensasi di ruangan lain TIDAK ikut.
     */
    public function scopeUntukRuangan($query, $ruanganId)
    {
        return $query->where(function ($w) use ($ruanganId) {
            $w->whereExists(function ($s) use ($ruanganId) {
                $s->selectRaw('1')->from('room_sequences')
                  ->whereColumn('room_sequences.mahasiswa_id', 'dispensasis.mahasiswa_id')
                  ->where('room_sequences.ruangan_id', $ruanganId)
                  ->whereColumn('room_sequences.start_date', '<=', 'dispensasis.tanggal_selesai')
                  ->whereColumn('room_sequences.end_date', '>=', 'dispensasis.tanggal_mulai');
            })->orWhereExists(function ($s) use ($ruanganId) {
                $s->selectRaw('1')->from('shift_schedules')
                  ->whereColumn('shift_schedules.mahasiswa_id', 'dispensasis.mahasiswa_id')
                  ->where('shift_schedules.ruangan_id', $ruanganId)
                  ->whereColumn('shift_schedules.tanggal', '>=', 'dispensasis.tanggal_mulai')
                  ->whereColumn('shift_schedules.tanggal', '<=', 'dispensasis.tanggal_selesai');
            })->orWhereExists(function ($s) use ($ruanganId) {
                // data lama: mahasiswa tanpa room_sequences, ruangan di kolom mahasiswas.ruangan_id
                $s->selectRaw('1')->from('mahasiswas')
                  ->whereColumn('mahasiswas.id', 'dispensasis.mahasiswa_id')
                  ->where('mahasiswas.ruangan_id', $ruanganId)
                  ->whereNotExists(function ($x) {
                      $x->selectRaw('1')->from('room_sequences')->whereColumn('room_sequences.mahasiswa_id', 'mahasiswas.id');
                  });
            });
        });
    }
}